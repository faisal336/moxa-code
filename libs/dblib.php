<?php
	define("HOST","localhost");
	define("LOGIN","root");
	define("PASSWORD","intellifuel");
	define("DB","serialqueue");
	define("TRUE",1);
	define("FALSE",0);

	class DatabaseHandle {
	
		// class members
		var $sock;
		var $db;
		var $host;
		var $login;
		var $passwd;
		var $result;
		
		// initialize to defaults
		function DatabaseHandle()
		{
			$this->db = DB;
			$this->host = HOST;
			$this->login = LOGIN;
			$this->passwd = PASSWORD;
		}
		
		function setDatabase($database)
		{
			$this->db = $database;
		}
		function setHost($h)
		{
			$this->host = $h;
		}
		function setLogin($l)
		{
			$this->login = $l;
		}
		function setPassword($p)
		{
			$this->passwd = $p;
		}
	
		// connect to the set database with login/passwd
		function connect()
		{
			$this->sock = mysql_connect($this->host,$this->login,$this->passwd);
			return($this->sock);
		}

		// end the connection
		function close()
		{
			mysql_close($this->sock);
		}

		// queries for table list and returns the number of tables
		function getTableList()
		{
			$this->result = mysql_list_tables($this->db);
			return($this->getRowCount());
		}
		// gets the table name of the corresponding index
		// must have a successful call to getTableList first
		function getTableName($i)
		{
			return(mysql_tablename($this->result,$i));
		}
		function getRowCount()
		{
			return(mysql_num_rows($this->result));
		}

		// setup the result set, must free the result before ANY other query is performed
		// return true on success, false otherwise
		function query($q)
		{
			$this->result = mysql_db_query($this->db,$q,$this->sock);
			if($this->result)
			{
				return(TRUE);
			}
			else
			{
				//mysql_free_result($this->result);
				return(FALSE);
			}
		}
		
		// use this to iterate through the list
		function nextRow()
		{
			return(mysql_fetch_object($this->result));
		}
		
		function freeResult()
		{
			if($this->result)
				mysql_free_result($this->result);
		}
		
		// get results from a query which only returns one row
		function getSingleResultRow($q)
		{
			if($this->query($q))
			{
				$row = $this->nextRow();
				// free it manually
				//$this->freeResult();
				return($row);
			}
			return(FALSE);
		}
		// array calls use associative arrays, use index calls for index
		function getSingleResultArray($q)
		{
			if($this->query($q))
			{
				//$row = mysql_fetch_array($this->result,MYSQL_BOTH);
				$row = $this->nextArrayRow();
				return($row);
			}
			return(FALSE);
		}
		function nextArrayRow()
		{
			return(mysql_fetch_array($this->result,MYSQL_BOTH));
		}
		function getSingleResultIndex($q)
		{
			if($this->query($q))
			{
				$row = $this->nextIndexRow();
				return($row);
			}
			return(FALSE);
		}
		function nextIndexRow()
		{
			return(mysql_fetch_row($this->result));
		}
		function getFieldCount()
		{
			return(mysql_num_fields($this->result));
		}
		/*function getRowCount()
		{
			return(mysql_num_rows($this->result));
		}*/
		// index is field position starting at 1 in this instance
		// so make sure to do an i+1 if looping through an array index.
		function getFieldName($index)
		{
			return(mysql_field_name($this->result,$index));
		}
	}
?>
