<?php
/*
	Load a file and parse into name=value properties.
	stand alone flags are given a value of true
	
	ex:
	gaugetype=TLS50
	processrailcars
	
	in the above example, properties->getValue("gaugetype") will return "TLS350"
	and properties->exists("processrailcars") will return true.
*/

class Properties {

	var $fname;
	var $cfg;

	function Properties($f) {
		$this->cfg = array();
		if(file_exists($f)) {
			$this->fname = $f;
			$lines = file($this->fname);
			foreach($lines as $line) {
				$line = trim($line);
				if(strlen($line) > 0 && substr($line,0,1) !== "#") {
					// config, see if its a flag or name=value
					$fields = explode("=",$line);
					if(count($fields)==2) {
						$this->cfg[$fields[0]] = $fields[1];
					} else if(count($fields)==1) {
						$this->cfg[$fields[0]] = true;
					}
				}
			}
		}
	}
	function exists($key) {
		return array_key_exists($key,$this->cfg);
	}
	function getValue($key) {
		return $this->exists($key) ? $this->cfg[$key] : false;
	}
	function assign($key,$dflt=false) {
		return $this->exists($key) ? $this->cfg[$key] : $dflt;
	}
}


?>
