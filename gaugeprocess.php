#!/usr/bin/php
<?php
/*
	Change Log
	
	2013-03-22 Add loss control polling and message caching
*/
require_once("/var/www/html/libs/properties.php");

declare(ticks = 1);
openlog("gaugeprocess", LOG_PID | LOG_PERROR, LOG_LOCAL0);
ob_implicit_flush();

$uname=php_uname("n");
$pieces=explode(".",$uname);
$uname=$pieces[0];

$to = "ms2v0@intellifuel.com";
$subject="RUBYOTHERVRGAUGE-LN3V1-MINI-REV0.0";
$cmd="SOH100";
$gmdate=false;
$ewworkbench="NO";
$ip="127.0.0.1";
$port = "10001";
$from = $uname;
$autodetect = "NO";
$validcomm = false;
$savedir = "/root/save/gauges";

$fp = false;

date_default_timezone_set("UTC");

// 1 mandatory argument, config file, may be preceeded by f=
if($argc != 2) {
	die("\n\nusage: gaugeprocess /path/to/config_file\n\n");
}

$config = $argv[1];
if(substr($argv[1],0,2) == "f=") {
	$config = substr($argv[1],2);
}
echo "config file is $config\n";
$cfg = new Properties($config);
//var_dump($cfg);
$ip = $cfg->assign("ip",$ip);
$port = $cfg->assign("port",$port);
$to = $cfg->assign("to",$to);
$subject = $cfg->assign("subject",$subject);
$from = $cfg->assign("from",$from);
$gmdate = $cfg->assign("GMT",$gmdate);
$tankcnt = $cfg->assign("tankcnt",5);
$ewrate = $cfg->assign("ewrate","0");
$ewworkbench = ($ewrate == "0") ? "NO" : "YES";
$droprate = $cfg->assign("droprate",240);
$rate = $cfg->assign("rate",120);
$zipfile = $cfg->assign("zipfile","YES");
$centeron = $cfg->exists("centeron");
$incon = $cfg->exists("incon");
$timeout = $cfg->assign("timeout",5000);
// passthru cache
$ptcache = $cfg->assign("ptcache","NO");
// rate to poll for loss control
$lossrate = $cfg->assign("lossrate",0); // disabled by default
$maxcachetime = $cfg->assign("maxcachetime",1800); // 30 minutes
// smooth out inventory and delivery polling, try not to cross minute boundries
$smooth = $cfg->assign("smooth",0);
$secure = $cfg->assign("secure",false);
// only use valid crc responses when polling 'i' messages
$machine_code = $cfg->exists("machine_code");

$ptrate = 0;
$ptexp = 0;
$ptcmd = "";

// timeout is in milliseconds, we need seconds
// we also add 2 seconds to the configure timeout to make sure multicom times out first
$timeout = (int)($timeout / 1000);
$timeout += 2;

$gaugetype = "TLS250";
$autodetect = "YES";
// don't auto detect if we configured it or if we enabled enviro's
if($cfg->exists("gt")) {
	$gaugetype = $cfg->assign("gt",$gaugetype);
	$autodetect = "NO";
} else if($ewworkbench == "YES") {
	$gaugetype = "TLS350";
	$autodetect = "NO";
}

if($centeron || $gaugetype == "CENTERON") {
	$gaugetype = "CENTERON";
	$autodetect = "NO";
	$ewworkbench = "NO";
	$centeron = true;
} else if($incon || $gaugetype == "INCON") {
	$gaugetype = "INCON";
	$autodetect = "NO";
	$incon = true;
}

if(! is_dir($savedir))
	mkdir($savedir,0755);
if(! is_file("/tmp/macaddress")) {
	system("cat /sys/class/net/eth*/address > /tmp/macaddress");
	system("/bin/sync");
}

$from="$uname-$port@intellifuel.com";
$filedata=file("/tmp/macaddress");
$fromemail=trim($filedata[0]);
$fromemail=str_replace(":","",$fromemail);
$from="mini$fromemail@intellifuel.net";

// first, see if we are sunoco
//if(strstr($uname,"SUNOCO")) {
if(strtoupper(substr($uname,0,6)) == "SUNOCO") {
	syslog (LOG_INFO ,"startup - auto detected SUNOCO settings");
	$autodetect = "NO";
	$gaugetype = "TLS350";
	//$ewworkbench = "YES";
	//$ewrate = 24;
}

// convert to seconds
$ewrate = $ewrate * 3600;
$droprate = $droprate * 60;
$rate = $rate * 60;

// don't proceed until we can connect to multicom
syslog (LOG_INFO ,"startup - connecting to multicom");
if($secure !== false && substr($secure,0,4) == "PIN:" && substr($secure,-1) == ":") {
	$secure = substr($secure,4,-1);
	syslog (LOG_INFO ,"using secure mode: ".$secure);
}

while(!connectSocket()) {
		sleep(3);
		syslog (LOG_INFO ,"startup - waiting for multicom");
}

/*
do {
	$fp=fsockopen($ip,$port);
	if($fp) {
		sleep(1);
	} else {
		sleep(3);
		syslog (LOG_INFO ,"startup - waiting for multicom");
	}
} while(!$fp);
fclose($fp);
*/
syslog (LOG_INFO ,"startup - connected");

// attempt autodetect the gauge monitor type, very simple for now.
// start with TLS250 and only set to 350 if we get a good response to i20101
if($autodetect == "YES") {
	// should still be open
	//$fp=fsockopen($ip,$port);
	if ($fp) {
		// try a few times
		for($i=0; $i<10; $i++) {
			sleep(1);
			if($secure !== false) {
				$cmd=sprintf("%c%si20101",0x01,$secure);
			} else {
				$cmd=sprintf("%ci20101",0x01);
			}
			//stream_set_timeout($fp,$timeout);
			//fputs($fp,"$cmd\r");
			sendSocket($cmd);
			//$r = fread($fp,8192);
			$r = readSocket();

			if(strlen($r) > 0) {
				if(strstr($r,"i20101")) {
					$gaugetype = "TLS350";
					$validcomm = true;
					break;
				} else if(strstr($r,"9999FF1B")) {
					$gaugetype = "TLS250";
					$validcomm = true;
					break;
				}
			}
		}
		// don't close yet
		//fclose ($fp);
	}
}

// lets try a tank report for everything but centeron and incon
if(!($centeron || $incon)) {
	sleep(1);
	//$fp=fsockopen($ip,$port);
	if ($fp) {
		$found = false;
		$cnt = "";
		for($i=0; $i<2; $i++) {
			sleep(1);
			$found = false;
			$cnt = "";
			$cmd=sprintf("%c200",0x01);
			//stream_set_timeout($fp,$timeout);
			syslog (LOG_INFO ,"attempt auto detect tank count");
			//fputs($fp,"$cmd\r");
			sendSocket($cmd);
			//$r = fread($fp,4096);
			$r = readSocket();
			//if(strlen($r) > 0 && substr($r,3,3)=="200") {
			if(strlen($r) > 0 && strstr(substr($r,0,10),"200") && strstr($r,"TANK  PRODUCT")) {
				// send it to file
				file_put_contents("/tmp/tankrpt.txt",toPrintableString($r,false));
				// read it back in
				$data = file("/tmp/tankrpt.txt");
				// loop thru it
				foreach($data as $line) {
					$line = trim($line);
					if(substr($line,0,13) == "TANK  PRODUCT") {
						$found = true;
					} else if($found && strlen($line) > 0 && !strstr($line,"ETX")) {
						$parts = explode(" ",$line);
						$cnt = $parts[0];
					}
				}
			} else if(strlen($r) > 0) {
				echo toPrintableString($r,false)."\n\n";
				syslog (LOG_INFO ,"bad response");
			}
			if($found) break;
		}
		//fclose ($fp);
		if($found && strlen($cnt) > 0 && ($cnt > 0 && $cnt <= 8)) {
			echo "fount a tank count ".$cnt."\n";
			syslog (LOG_INFO ,"startup - detected tank count $cnt");
			$tankcnt = $cnt;
		}
	}
}
echo "building poll arrays with zip = $zipfile and timeout = $timeout\n";
/*if($fp) {
	fclose($fp);
	sleep(2);
}*/


$invlist = array();
$droplist = array();
$ewlist = array();
$alarmlist = array();


if ($gaugetype=="EBW") {
	if($zipfile == "YES") {
			$droplist[]="150";
			$invlist[]="100";
	} else {
		for ($i=0;$i<$tankcnt;$i++)
			$droplist[]=sprintf("15%d",$i+1);
		for ($i=0;$i<$tankcnt;$i++)
			$invlist[]=sprintf("10%d",$i+1);
	}
} else if ($gaugetype=="TLS350") {

	// build alarm list by keys and hold last response as value
	$alarmlist["I10100"] = "";

        $invlist[] = "i20100";
        $droplist[] = "i20200";


	// setup which commands are polled by tank
	for ($i=0;$i<$tankcnt;$i++) {
		// inventory
		//$invlist[]=sprintf("i201%02d",$i+1);
		// deliveries
		//$droplist[]=sprintf("i202%02d",$i+1);
		// env by tank
		$ewlist[]=sprintf("I201%02d",$i+1);
		$ewlist[]=sprintf("I202%02d",$i+1);
		$ewlist[]=sprintf("I203%02d",$i+1);
		$ewlist[]=sprintf("I204%02d",$i+1);
		$ewlist[]=sprintf("I205%02d",$i+1);
		$ewlist[]=sprintf("I206%02d",$i+1);
		$ewlist[]=sprintf("I207%02d",$i+1);
		$ewlist[]=sprintf("I208%02d",$i+1);
		$ewlist[]=sprintf("I20A%02d",$i+1);
		$ewlist[]=sprintf("I20B%02d",$i+1);
		$ewlist[]=sprintf("I20C%02d",$i+1);
		$ewlist[]=sprintf("I20D%02d",$i+1);

		$ewlist[]=sprintf("I211%02d",$i+1);
		$ewlist[]=sprintf("I221%02d",$i+1);
		$ewlist[]=sprintf("I222%02d",$i+1);
		$ewlist[]=sprintf("I225%02d",$i+1);
		$ewlist[]=sprintf("I226%02d",$i+1);
		$ewlist[]=sprintf("I227%02d",$i+1);
		$ewlist[]=sprintf("I251%02d",$i+1);
		$ewlist[]=sprintf("I281%02d",$i+1);
		$ewlist[]=sprintf("I282%02d",$i+1);
		$ewlist[]=sprintf("I2E2%02d",$i+1);
		$ewlist[]=sprintf("I391%02d",$i+1);

	}
	// also poll this with drops
	$droplist[]=sprintf("I20500");

	// These env's are polled as 00 - global
	$ewlist[] = "I10100";
	$ewlist[] = "I10200";
	$ewlist[] = "I11100";
	$ewlist[] = "I11200";
	$ewlist[] = "I11300";
	$ewlist[] = "I11400";
	$ewlist[] = "I11600";
	$ewlist[] = "I30100";
	$ewlist[] = "I30200";
	$ewlist[] = "I30600";
	$ewlist[] = "I30700";
	$ewlist[] = "I31100";
	$ewlist[] = "I31200";
	$ewlist[] = "I31500";
	$ewlist[] = "I34100";
	$ewlist[] = "I34200";
	$ewlist[] = "I34600";
	$ewlist[] = "I34700";
	$ewlist[] = "I34B00";
	$ewlist[] = "I34C00";
	$ewlist[] = "I35100";
	$ewlist[] = "I35200";
	$ewlist[] = "I35300";
	$ewlist[] = "I37300";
	$ewlist[] = "I37400";
	$ewlist[] = "I38100";
	$ewlist[] = "I38200";
	$ewlist[] = "I38300";
	$ewlist[] = "I38400";
	$ewlist[] = "I38600";
	$ewlist[] = "I38700";
	$ewlist[] = "I38800";
	$ewlist[] = "I38900";
	$ewlist[] = "I40100";
	$ewlist[] = "I40200";
	$ewlist[] = "I40300";
	$ewlist[] = "I40600";
	$ewlist[] = "I70400";
	$ewlist[] = "I72300";
	$ewlist[] = "I74400";

	// sort to make it poll nice
	sort($ewlist);
} else if($gaugetype=="CENTERON") {
	$invlist[] = "MMM";
} else if($gaugetype=="INCON") {
	$droplist[] = sprintf("1b0027cd%c",0x03);
	$invlist[] = sprintf("1800bec2%c",0x03);
	$ewlist[] = sprintf("1j008e6c%c",0x03);

} else { // default to tls250
	if($zipfile == "YES") {
			$invlist[]="100";
			$droplist[]="200";
			$droplist[]="150";
	} else {
		// inventory
		for ($i=0;$i<$tankcnt;$i++)
			$invlist[]=sprintf("10%d",$i+1);
		$droplist[]=sprintf("200");
		// delivery
		for ($i=0;$i<$tankcnt;$i++)
			$droplist[]=sprintf("15%d",$i+1);
	}
}

function toPrintableString($r, $crlf=true)
{
		$data="";
		$l = strlen($r);
		for ($i=0;$i<$l;$i++) {
			$x=substr($r,$i,1);
			$d=ord($x);
			if ($d==0x01)
				$data .="SOH";
			else if ($d==0x03)
				$data .="ETX";
			else if ($d==0x0d && $crlf)
				$data .="CCR";
			else if ($d==0x0a && $crlf)
				$data .="LLF";
			else
				$data .=$x;
		}
		return $data;
}

// poll a set of commands using the IP interface to multicom
function poll($cmdlist, $cachemsg=false, $pullfromcache=false, $sendemail=true) {
	//global $ip, $port, $timeout;
	global $to, $from, $subject, $gmdate;
	global $lossrate, $retry, $zipfile;
	global $fp,$secure;

	$retry = array();
	
	//$fp=fsockopen($ip,$port);
	if(!connectSocket())
	//if ($fp==false)
	   return false;
	sleep(1);
	foreach ($cmdlist as $cmd2) {
		if($secure !== false) {
			$cmd2 = $secure.$cmd2;
			//syslog (LOG_INFO ,"secure cmd: $cmd");
		}
		// this is the sunoco passthru cache
		if($cachemsg) {
			if(file_exists("/tmp/ptcache-".$cmd2)) {
				unlink("/tmp/ptcache-".$cmd2);
			}
		}
		$data="";
		if($pullfromcache && (($tmppull = pullFromCache($cmd2)) !== false) && strlen($tmppull) >= 5) {
			$r = $tmppull;
		} else {
			$cmd=sprintf("%c$cmd2",0x01);
			//stream_set_timeout($fp,$timeout);
			syslog (LOG_INFO ,"send $cmd");
			//fputs($fp,"$cmd\r");
			sendSocket($cmd);
			$r = readSocket();
			/*
			$r = "";
			while(!strstr($r,0x03))
				$r .= fread($fp,8192);
			*/
		}
		$l = strlen($r);	
	
		if ($l<5) {
			syslog (LOG_INFO ,"timeout");
			$retry[] = $cmd2;
			continue;
		}

		for ($i=0;$i<$l;$i++) {
			$x=substr($r,$i,1);
			$d=ord($x);
			if ($d==0x01)
				$data .="SOH";
			else if ($d==0x03)
				$data .="ETX";
			else if ($d==0x0d)
				$data .="CCR";
			else if ($d==0x0a)
				$data .="LLF";
			else
				$data .=$x;
		}
		syslog (LOG_INFO ,"read $data");
		// this is sunoco passthru cache only
		if($cachemsg) {
			if(substr($data,-3) == "ETX") {
				syslog (LOG_INFO ,"caching response");
				file_put_contents("/tmp/ptcache-".$cmd2,$r);
			} else {
				syslog (LOG_INFO ,"failed to cache response...no ETX");
			}
			continue;
		}
		// if we're doing loss control, save the message but don't re-save a cache msg
		if($lossrate > 0 && !$pullfromcache) {
			lccache($cmd2,$r);
		}
		if ($gmdate)
			$date=gmdate("YmdHi");
		else
			$date=date("YmdHi");
		$date=substr($date,2);

		$message ="<STARTOFPORTC>";
		$message .=$date;
		$message .="k00C11";
		$message .=$data;
		echo "$to $from $subject message ($message)\r\n";
		//if($zipfile == "YES") {// && strlen($message) > 500) {
		// send zip format for messages that use the 00 syntax for environmentals
		if($zipfile == "YES" && $gaugetype = "TLS350" && substr($cmd2,-2) == "00") {
			$fname = "/tmp/gptmp";
			$zip = "/tmp/gptmp.gz";
			system("rm -f $fname");
			system("rm -f $zip");
			file_put_contents($fname,$message);
			system("gzip ".$fname);
			$content = chunk_split(base64_encode(file_get_contents($zip)));
			$headers = "From: $from\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: application/zip; name=\"gptmp.gz\"\r\n";
			$headers .= "Content-Transfer-Encoding: base64\r\n";

			if($sendemail)
				//mailto("ms2-test@intellifuel.com",$subject,$content,$headers);
				mailto($to,$subject,$content,$headers);
		} else if($sendemail) {
				// check for multiple fragments of SOH--ETX
				$etxcnt = substr_count($data,"ETX");
				if($etxcnt > 1) {
					syslog (LOG_INFO ,"multiple fragments - $etxcnt");
					$etxmsg = explode("ETX",$data);
					foreach($etxmsg as $tmpmsg) {
						syslog (LOG_INFO ,"checking fragment $tmpmsg");
						if(strlen($tmpmsg) <= 0) continue;
						$message = "<STARTOFPORTC>";
				                $message .= $date;
               					$message .= "k00C11";
                				$message .= $tmpmsg."ETX";
						syslog (LOG_INFO ,"sending fragment $message");
						mailto($to,$subject,$message,"From: $from\r\n");
					}
				} else
					mailto($to,$subject,$message,"From: $from\r\n");
		}
	}
	//fclose ($fp);
	//$fp = false;
}

function checkAlarms($force=false) {
	//global $ip, $port, $alarmlist, $timeout;
	global $alarmlist, $timeout, $fp;
	global $to, $from, $subject, $gmdate, $secure;

	if(count($alarmlist) < 1)
		return;

	// we have at least 1 alarm
	//$fp=fsockopen($ip,$port);
	if(!connectSocket())
	//if ($fp==false)
	   return false;
	sleep(1);
	foreach ($alarmlist as $key => $val) {

		$cmd=sprintf("%c$key\r",0x01);
		if($secure !== false) {
			$cmd=sprintf("%c$secure$key\r",0x01);
		}
		//stream_set_timeout($fp,$timeout);
		syslog (LOG_INFO ,"check alarms: send $cmd");
		//fputs($fp,"$cmd");
		sendSocket($cmd);
		$r = readSocket();
		
		//$r = fread($fp,8192);
		$l = strlen($r);	
	
		if ($l<strlen($cmd)) {
			syslog (LOG_INFO ,"timeout");
			continue;
		}
		syslog (LOG_INFO ,"check alarms: read $r");        
		if(strlen($val) < 1) {
			// first response, nothing to compare
			syslog (LOG_INFO ,"storing first alarm response");
			$alarmlist[$key] = $r;
			continue;
		}

		//syslog (LOG_INFO ,"ALARMDBG: $r");
		//syslog (LOG_INFO ,"ALARMDBG: $alarmlist[$key]");
		// write it out
		$data = toPrintableString($r,false);
		file_put_contents("/tmp/gp-$key",$data);
		// write out the previous response
		file_put_contents("/tmp/gp-last-$key",toPrintableString($alarmlist[$key],false));
		
		$cur = file("/tmp/gp-$key");
		$prev = file("/tmp/gp-last-$key");
		
		// check for differences after line 3
		if(count($cur) < 3 || count($prev) < 3) {
			// bad data
			syslog (LOG_INFO ,"bad data trying to compare");
			continue;
		}

		// data is good, save the response
		$alarmlist[$key] = $r;

		$diff = false;
		if(count($cur) == count($prev)) {
			// could be the same, lets check
			$cnt = count($cur);
			for($i=3; $i<$cnt; $i++) {
				if(strcmp($cur[$i],$prev[$i]) != 0) {
					$diff = true; // found a difference
					syslog (LOG_INFO ,"found alarm change at line $cur[$i]");
					break;
				}
			}
			if(!$diff && !$force) {
				//syslog (LOG_INFO ,"no alarm change in $cnt lines");
				continue;
			}
		}
		// if we made it here, it changed
		if ($gmdate)
			$date=gmdate("YmdHi");
		else
			$date=date("YmdHi");
		$date=substr($date,2);

		// re-convert for email
		$data = toPrintableString($r,true);
		$message ="<STARTOFPORTC>";
		$message .=$date;
		$message .="k00C11";
		$message .=$data;
		//echo "$to $from $subject message ($message)\r\n";
		syslog (LOG_INFO ,"ALARMDBG MAIL to $to, $subject, $message");
		mailto($to,$subject,$message,"From: $from\r\n");
		// now follow up with two more messages
		$almrqst = array("i11300","i10100");
		poll($almrqst);
	}
	//fclose ($fp);
	//$fp = false;
}


// every rate, send the drop command list
// every ewrate, send the ew command list
// do a lot of sleeping
$lastew = 0;
$lastdrop = 0;
$lastinv = 0;
$lastalarm = 0;
$lastpurge = 0;
$lastpt = 0;
$lastlc = 0;

$retry = array();

// randomize the hour of the day that we do enviro
$envhr = rand(10,14);
$envmin = rand(0,59);
// what time is it now?
if($gmdate) {
	$hr = gmdate("H");
	$min = gmdate("i");
} else {
	$hr = date("H");
	$min = date("i");
}
// put it in seconds past midnight
$nowspm = ($hr * 3600) + ($min * 60);
$envspm = ($envhr * 3600) + ($envmin * 60);
// get our time now
$now = time();
// adjust to midnight and yesterday
$midnight = $now - $nowspm;
$yesterday_midnight = $midnight - 86400;
$lastew = $yesterday_midnight + $envspm;

// setup the passthru if it's there
if($ptcache != "NO") {
	$parts = explode(",",$ptcache);
	if(count($parts)==2) {
		$ptcmd = $parts[0];
		$ptrate = $parts[1];
		syslog (LOG_INFO ,"startup - passthru cache is $ptcmd / $ptrate");
	}
}
syslog (LOG_INFO ,"startup - gauge type is $gaugetype");
syslog (LOG_INFO ,"startup - tank count is $tankcnt");
syslog (LOG_INFO ,"startup - inventory rate is $rate seconds");
syslog (LOG_INFO ,"startup - drop rate is $droprate seconds");
syslog (LOG_INFO ,"startup - EW is $ewworkbench and random poll time is $envhr:$envmin for ".count($ewlist)." messages");
syslog (LOG_INFO ,"startup - Machine code polling is ".$machine_code);

if($machine_code && $gaugetype == "TLS350") {
	/*
	// replace each env message with 'i' version in machine mode
	for($i=0; $i<count($ewlist); $i++) {
		$ewlist[$i] = str_replace('I','i',$ewlist[$i]);
	}
	*/
	$imsgarr = array();
	// duplicate each env message with 'i' version
	foreach($ewlist as $tmpi) {
		$imsgarr[] = str_replace('I','i',$tmpi);
	}
	// add back in, in the same order
	foreach($imsgarr as $tmpimsg) {
		$ewlist[] = $tmpimsg;
	}
}

$csock = false;

while(true) {
	$now = time();
	if($centeron) {
		if(!$csock) {
			// centeron has to stay connected and just listen
			syslog (LOG_INFO ,"Initializing Centeron socket");
			$csock=fsockopen($ip,$port);
			stream_set_timeout($csock,30);
		}
		if(($now - $lastinv) > $rate) {
			fputs($csock,"MMM\r");
		}
		$in = fread($csock,8192);
		syslog (LOG_INFO ,"Centeron input: ".$in);
		$records = explode("=",$in);
		$rcnt = 0;
		foreach($records as $data) {
			// need to adjust split strings for parser.  Add the '=' back in and
			// for the first one, add the =xcrlf so it's not skipped
			if($rcnt == 0) $data = "=01\r\n".$data;
			else $data = "=".$data;
			$rcnt++;

			if(strlen($data) > 0 && stristr($data,"Waiting")) {
				// just connected, reserve for future use
				syslog (LOG_INFO ,"Centeron recv: ".$data);
				$lastinv = $now;
			} else if(strlen($data) > 10) {
				$data = toPrintableString($data);
				syslog (LOG_INFO ,"Centeron recv: ".$data);
				$lastinv = $now;
				$pos = strpos($data,"Sig[dB]");
				if($pos !== false && $pos > 0) {
					$data = substr($data,0,$pos+7);
					if ($gmdate)
						$date=gmdate("YmdHi");
					else
						$date=date("YmdHi");
					$date=substr($date,2);
		
					$message ="<STARTOFPORTC>";
					$message .=$date;
					$message .="k00C11";
					$message .=$data;
					syslog (LOG_INFO ,"Centeron reporting: ".$message);
					mailto($to,$subject,$message,"From: $from\r\n");
				}
			}
		}
		sleep(1);
		continue;
	}

	if(($lossrate > 0) && (($now - $lastlc) > $lossrate)) {
		// poll drops & inv and cache it
		syslog (LOG_INFO ,"poll inventory and drops with caching on");
		smoothPoll($smooth);
		poll($invlist,false,false,false);
		$lastlc = time();
	}
	if(($now - $lastinv) > $rate) {
		// poll drops
		syslog (LOG_INFO ,"poll inventory");
		smoothPoll($smooth);
		poll($invlist,false,($lossrate > 0),true);
		$lastinv = time();
	}
	if(($now - $lastdrop) > $droprate) {
		// poll drops
		syslog (LOG_INFO ,"poll drops");
		smoothPoll($smooth);
		poll($droplist,false,($lossrate > 0),true);
		$lastdrop = time();
	}
	if(($now - $lastalarm) > 60) {
		checkAlarms();
		$lastalarm = time();
	}
	if($ewworkbench == "YES" && ($now - $lastew) > $ewrate) {
		// poll ew, mark the time before polling because it may take a long time
		// and we don't want to drift the time of day we poll these messages
		$lastew = time();
		syslog (LOG_INFO ,"poll Environmental Workbench");
		// always check alarms and force email
		checkAlarms(true);
		poll($ewlist,false,false,true);
		if(count($retry) > 0) {
			// retry the env's so we don't miss one
			syslog (LOG_INFO ,"re-poll Environmental Workbench with ".count($retry)." retry items");
			poll($retry,false,false,true);
		}
	}
	if($ptcache != "NO" && ($now - $lastpt) > $ptrate) {
		// send the command
		$ptarray = array();
		$ptarray[] = $ptcmd;
		poll($ptarray,true,false,false);
		$lastpt = time();
	}
	// check for close
	if(file_exists("/tmp/gotclose")) {
		syslog (LOG_INFO ,"got close");
		$lastinv = 0;
		$lastdrop = 0;
		unlink("/tmp/gotclose");
	}
	// check for purge once a day
	if(($now - $lastpurge) > 86400) {
		syslog (LOG_INFO ,"purging logs");
		purge_logs($now);
		$lastpurge = $now;
	}
	closeSocket();
	sleep(5);
}
closelog();
exit(0);

// override the mail function to also save a copy like sendemail.php
function mailto($to, $subject, $message, $headers) {
	global $savedir;

	mail($to, $subject, $message, $headers);
	// save a copy
	$fname = $savedir."/gauge-".date("Ymd-His");
	if(file_exists($fname))
		$fname = $fname."~";
	file_put_contents($fname,$message);
}

// purge anything older than x days from the time passed in.
// 0 days will clear a directory
function purge_logs($now, $days = 7)
{
	global $savedir;

	$dir = opendir($savedir);
	if(!$dir)
		return;

	// keep for 1 week
	$ptime = 86400 * $days;
	$cnt = 0;
	while(($f = readdir($dir)) !== false) {
		if($f == "." || $f == "..")
			continue;
		$fname = $savedir."/".$f;
		$info = stat($fname);
		if($info) {
			$t = $info[9];
			if(($now - $t) > $ptime) {
				unlink($fname);
				$cnt++;
			}
		}
	}
	syslog (LOG_INFO ,"purged $cnt entries");
}
// cache for loss control
// save a response to a command by time stamp directory structure
function lccache($cmd,$response) {
	global $gmdate,$savedir;

	if(strlen($cmd) <= 0 || strlen($response) <= 0) 
		return;
	
	if($gmdate) {
		$yr = gmdate('Y');
		$month = gmdate('m');
		$day = gmdate('d');
		$stamp = gmdate('YmdHis');
	} else {
		$yr = date('Y');
		$month = date('m');
		$day = date('d');
		$stamp = date('YmdHis');
	}
	// year	
	$cdir = $savedir."/".$yr;
	if(! is_dir($cdir))
		mkdir($cdir,0755);
	// month
	$cdir = $cdir."/".$month;
	if(! is_dir($cdir))
		mkdir($cdir,0755);
	// day
	$cdir = $cdir."/".$day;
	if(! is_dir($cdir))
		mkdir($cdir,0755);
	
	$outfile = $cdir."/".$cmd."-".$stamp;
	file_put_contents($outfile,$response);
	echo "caching $cmd @ $stamp\n";
}

// return a cached message or false
function pullFromCache($cmd) {
	global $gmdate,$savedir,$maxcachetime;

	if($gmdate) {
		$yr = gmdate('Y');
		$month = gmdate('m');
		$day = gmdate('d');
		$now = gmdate('YmdHis');
	} else {
		$yr = date('Y');
		$month = date('m');
		$day = date('d');
		$now = date('YmdHis');
	}
	
	$dir = $savedir."/".$yr."/".$month."/".$day;
	if(file_exists($dir) && is_dir($dir)) {
		$target = $cmd."-";
		$cmdlen = strlen($target);
		$last_response = false;
		$last_stamp = 0;

		$handle = opendir($dir);
		if($handle) {
			while(($file = readdir($handle)) !== false) {
				if(substr($file,0,$cmdlen) == $target) {
					// get the stamp
					$parts = explode("-",$file);
					if(count($parts) == 2) {
						// part 0 is the cmd
						// part 1 is the timestamp
						if($parts[1] > $last_stamp && ($now - $parts[1]) <= $maxcachetime) {
							$last_stamp = $parts[1];
							$last_response = file_get_contents($dir."/".$file);
							// debug
							echo "found newer cached response for $cmd @ $last_stamp\n";
						}
					}
				}
			}
			closedir($handle);
			if($last_response && $last_stamp > 0)
				return $last_response;
		}
	}
	return false;
}

// network functions
// if already connected, don't reconnect
function connectSocket() {
	global $fp, $ip, $port, $timeout;
	
	if($fp) return true;

	$fp = fsockopen($ip,$port,$errno,$errstr,5);
	if(!$fp) {
		syslog (LOG_INFO ,"failed to connect to $ip:$port ($errno) $errstr");
		return false;
	}
	syslog(LOG_INFO,"connected to $ip:$port");
	return true;
}
function closeSocket() {
	global $fp;
	if($fp)
		fclose($fp);
	$fp = false;
}
function readSocket() {
	global $fp, $timeout, $gaugetype;

	$r = "";

	if(!$fp) return $r;
	
	stream_set_timeout($fp,$timeout);
	$start = time();

	$r = fread($fp,4096);
	while(strpos($r,0x03) === FALSE) {
		sleep(1);
		$r .= fread($fp,8192);
		$now = time();
		if($now - $start >= $timeout) break;
		//echo "subread: $r (".strlen($r).") bytes";
	}

	// now, lets make sure we didn't overlap responses from a previous timeout, split at ETX
	$tmp = strpos($r,0x03);
	if($tmp !== FALSE && (strlen($r) > ($tmp+1)) && $gaugetype != "INCON") {
		// we have an overlap, discard the first part
		$r = substr($r,$tmp+1);
		// now continue again until we have ETX
		$start = time();
		while(strpos($r,0x03) === FALSE) {
			$r .= fread($fp,8192);
			sleep(1);
			$now = time();
			if($now - $start >= $timeout) break;
			//echo "subread: $r (".strlen($r).") bytes";
		}
	} else if($tmp === FALSE) {
		// no ETX, log it;
		syslog(LOG_INFO, "response missing ETX: $r");
	}
	return $r;
}
function sendSocket($cmd) {
	global $fp;
	if(!$fp) return;
	
	fputs($fp,"$cmd\r");
	//fflush($fp);
}

// don't return unless the minute is less that $smooth_time seconds old
// <= 0 is disabled
function smoothPoll($smooth_time) {

	$t = intval($smooth_time);
	if($t <= 0) return;
	// don't allow 1-5 or values over 45 seconds
	if($t < 5 || $t > 45)
		$t = 30;

	$s = date('s');
	$s = intval($s);

	while($s > $t) {
		sleep(1);
		$s = date('s');
		$s = intval($s);
	}	
}
?>
	

