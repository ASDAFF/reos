<?php
/**
 * ReOS is a vertical software for real estates.
 * Copyright 2010 IT ELAZOS S.L.
 *
 * This file is part of ReOS v2.x.x.
 *
 * ReOS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ReOS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with ReOS.  If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * MySQL Database Management 
 *
 *@package Database
 **/

class DB_Sql {

	/* public: connection parameters
	 var $Host     = "";
	 var $Database = "";
	 var $User     = "";
	 var $Password = "";*/

	var $Host     = _DB_HOST;
	var $Database = _DB_NAME; // var $Database = "marxua2_arpa";
	var $User     = _DB_USER; // "marxua2_user1"
	var $Password = _DB_PWD;
	var $prefix   = _TABLE_PREFIX;
	var $no_updates = false; // if true no updates are sent to the database


	/* public: configuration parameters */
	var $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
	var $Debug         = 0;     ## Set to 1 for debugging messages.
	var $Halt_On_Error = "report"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, save warnings in messages variable),"print" (ignore reports and echo them
	var $Error_Messages = array(); ##
	var $PConnect      = 0;     ## Set to 1 to use persistent database connections
	var $Seq_Table     = "db_sequence";

	/* public: result array and current row number */
	var $Record   = array();
	var $Row;

	/* public: current error number and error text */
	var $Errno    = 0;
	var $Error    = "";

	/* public: this is an api revision, not a CVS revision. */
	var $type     = "mysql";
	var $revision = "1.2";

	/* private: link and query handles */
	var $Link_ID  = 0;
	var $Query_ID = 0;

	var $locked   = false;      ## set to true while we have a lock

	/* public: constructor */
	function DB_Sql($query = "") {
		$this->query($query);
	}

	/* public: some trivial reporting */
	function link_id() {
		return $this->Link_ID;
	}

	function query_id() {
		return $this->Query_ID;
	}

	/* public: connection management */
	function connect($Database = "", $Host = "", $User = "", $Password = "") {
		/* Handle defaults */
		if ("" == $Database)
		$Database = $this->Database;
		if ("" == $Host)
		$Host     = $this->Host;
		if ("" == $User)
		$User     = $this->User;
		if ("" == $Password)
		$Password = $this->Password;

		/* establish connection, select database */
		if ( 0 == $this->Link_ID ) {

			if(!$this->PConnect) {
				$this->Link_ID = mysql_connect($Host, $User, $Password);
			} else {
				$this->Link_ID = mysql_pconnect($Host, $User, $Password);
			}
			if (!$this->Link_ID) {
				$this->halt("connect($Host, $User, \$Password) failed.");
				return 0;
			}

			if (!@mysql_select_db($Database,$this->Link_ID)) {
				$this->halt("cannot use database ".$Database);
				return 0;
			}
		}
		
		return $this->Link_ID;
	}

	/* public: discard the query result */
	function free() {
		@mysql_free_result($this->Query_ID);
		$this->Query_ID = 0;
	}

	/* public: perform a query */
	function query($Query_String) {
		/* No empty queries, please, since PHP4 chokes on them. */

		//echo "$Query_String";die();
		if ($this->no_updates &&
		(stristr(substr($Query_String,0,6),"update")
		|| stristr(substr($Query_String,0,6),"delete")
		|| stristr(substr($Query_String,0,6),"insert")
		)) {return true;}

		if ($Query_String == "")
		/* The empty query string is passed on from the constructor,
		 * when calling the class without a query, e.g. in situations
		 * like these: '$db = new DB_Sql_Subclass;'
		 */
		return 0;

		if (!$this->connect()) {
			return 0; /* we already complained in connect() about that. */
		};

		# New query, discard previous result.
		if ($this->Query_ID) {
			$this->free();
		}

		if ($this->Debug)
		printf("Debug: query = %s<br>\n", $Query_String);

		$this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
		$this->Row   = 0;
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();
		if (!$this->Query_ID) {
			$this->halt("Invalid SQL: ".$Query_String);
		}

		# Will return nada if it fails. That's fine.
		return $this->Query_ID;
	}

	/* public: walk result set */
	function next_record() {
		if (!$this->Query_ID) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		$this->Record = @mysql_fetch_array($this->Query_ID);
		$this->Row   += 1;
		$this->Errno  = mysql_errno();
		$this->Error  = mysql_error();

		$stat = is_array($this->Record);
		if (!$stat && $this->Auto_Free) {
			$this->free();
		}
		return $stat;
	}
	/* public: walk result set */
	function next_record_assoc() {
		if (!$this->Query_ID) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		$this->Record = @mysql_fetch_array($this->Query_ID,MYSQL_ASSOC);
		$this->Row   += 1;
		$this->Errno  = mysql_errno();
		$this->Error  = mysql_error();

		$stat = is_array($this->Record);
		if (!$stat && $this->Auto_Free) {
			$this->free();
		}
		return $stat;
	}
	/* public: return an array of select elements with column names on the first row */
	function select_array()
	{
		while ($this->next_record_assoc())
		{
			$total[]=$this->Record;
		}
		if (isset($total)) return $total; else return null;
	}
	/**
	 * Same as select_array but return special chars
	 *
	 * @return Record with all values with htmlspecialchars
	 */
	function select_array_xml() {
		while ($this->next_record_assoc()) {
			foreach($this->Record as $key=>$value){
				$this->Record[$key]=htmlspecialchars($value);
			}
			$total[]=$this->Record;
		}
		if (isset($total)) return $total; else return null;
	}
	/**
	 *Return an xml of the query
	 *ex : <row><field_name>value</field_name></row>
	 *@access public
	 *@param boolean Grid layout, if true node <hrow> contains fields name labels,
	 *if false attribute "label" contains the field name label
	 *@param string name of the node row, default is "row"
	 **/
	function select_xml($types=null,$grid=false,$row="row"){
		$xml="";
		if ($grid) $first=true;else $first=false;
		while ($this->next_record_assoc())
		{
			if ($first){
				$xml=$xml."<h$row>";
				foreach($this->Record as $name => $elem){
					if (defined(strtoupper("_".$name))) $label=constant(strtoupper("_".$name)); else $label="_".$name;
					$xml=$xml."<$name>".$label."</$name>";
				}
				$first=False;
				$xml=$xml."</h$row>";
				reset($this->Record);
			}
			$xml=$xml."<$row>";
			foreach($this->Record as $name => $elem)
			if ( $grid || (isset($elem) )) {
				$tp="";
				if ($types!=null) {if (array_key_exists($name,$types)) $tp=" type=\"".$types[$name]."\" ";}
				if (!$grid && defined(strtoupper("_".$name))) $label=" label=\"".constant(strtoupper("_".$name))."\""; else $label="";
				$xml=$xml."<$name$tp$label>".htmlspecialchars($elem)."</$name>";
			}
			$xml=$xml."</$row>\n";
		}
		return $xml;
	}

	/* public: position in result set */
	function seek($pos = 0) {
		$status = @mysql_data_seek($this->Query_ID, $pos);
		if ($status)
		$this->Row = $pos;
		else {
			$this->halt("seek($pos) failed: result has ".$this->num_rows()." rows.");

			/* half assed attempt to save the day,
			 * but do not consider this documented or even
			 * desireable behaviour.
			 */
			@mysql_data_seek($this->Query_ID, $this->num_rows());
			$this->Row = $this->num_rows();
			return 0;
		}

		return 1;
	}

	/* public: table locking */
	function lock($table, $mode = "write") {
		$query = "lock tables ";
		if(is_array($table)) {
			while(list($key,$value) = each($table)) {
				// text keys are "read", "read local", "write", "low priority write"
				if(is_int($key)) $key = $mode;
				if(strpos($value, ",")) {
					$query .= str_replace(",", " $key, ", $value) . " $key, ";
				} else {
					$query .= "$value $key, ";
				}
			}
			$query = substr($query, 0, -2);
		} elseif(strpos($table, ",")) {
			$query .= str_replace(",", " $mode, ", $table) . " $mode";
		} else {
			$query .= "$table $mode";
		}
		if(!$this->query($query)) {
			$this->halt("lock() failed.");
			return false;
		}
		$this->locked = true;
		return true;
	}

	function unlock() {

		// set before unlock to avoid potential loop
		$this->locked = false;

		if(!$this->query("unlock tables")) {
			$this->halt("unlock() failed.");
			return false;
		}
		return true;
	}

	/* public: evaluate the result (size, width) */
	function affected_rows() {
		return @mysql_affected_rows($this->Link_ID);
	}

	function found_rows() {
		$query = @mysql_query("SELECT FOUND_ROWS();",$this->Link_ID);
		$record = @mysql_fetch_array($query);
		list($key,$value)=each($record);
		return $value;
	}

	function num_rows() {
		return @mysql_num_rows($this->Query_ID);
	}

	function last_insert_id() {
		return @mysql_insert_id($this->Link_ID);
	}

	function num_fields() {
		return @mysql_num_fields($this->Query_ID);
	}

	/* public: shorthand notation */
	function nf() {
		return $this->num_rows();
	}

	function np() {
		print $this->num_rows();
	}

	function f($Name) {
		if (isset($this->Record[$Name])) {
			return $this->Record[$Name];
		}
	}

	function p($Name) {
		if (isset($this->Record[$Name])) {
			print $this->Record[$Name];
		}
	}

	/* public: sequence numbers */
	function nextid($seq_name) {
		/* if no current lock, lock sequence table */
		if(!$this->locked) {
			if($this->lock($this->Seq_Table)) {
				$locked = true;
			} else {
				$this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
				return 0;
			}
		}

		/* get sequence number and increment */
		$q = sprintf("select nextid from %s where seq_name = '%s'",
		$this->Seq_Table,
		$seq_name);
		if(!$this->query($q)) {
			$this->halt('query failed in nextid: '.$q);
			return 0;
		}

		/* No current value, make one */
		if(!$this->next_record()) {
			$currentid = 0;
			$q = sprintf("insert into %s values('%s', %s)",
			$this->Seq_Table,
			$seq_name,
			$currentid);
			if(!$this->query($q)) {
				$this->halt('query failed in nextid: '.$q);
				return 0;
			}
		} else {
			$currentid = $this->f("nextid");
		}
		$nextid = $currentid + 1;
		$q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
		$this->Seq_Table,
		$nextid,
		$seq_name);
		if(!$this->query($q)) {
			$this->halt('query failed in nextid: '.$q);
			return 0;
		}

		/* if nextid() locked the sequence table, unlock it */
		if($locked) {
			$this->unlock();
		}

		return $nextid;
	}

	/* public: return table metadata */
	function metadata($table = "", $full = false) {
		$count = 0;
		$id    = 0;
		$res   = array();

		/*
		 * Due to compatibility problems with Table we changed the behavior
		 * of metadata();
		 * depending on $full, metadata returns the following values:
		 *
		 * - full is false (default):
		 * $result[]:
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *
		 * - full is true
		 * $result[]:
		 *   ["num_fields"] number of metadata records
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *   ["meta"][field name]  index of field named "field name"
		 *   This last one could be used if you have a field name, but no index.
		 *   Test:  if (isset($result['meta']['myfield'])) { ...
		 */

		// if no $table specified, assume that we are working with a query
		// result
		if ($table) {
			$this->connect();
			$id = @mysql_list_fields($this->Database, $table);
			if (!$id) {
				$this->halt("Metadata query failed.");
				return false;
			}
		} else {
			$id = $this->Query_ID;
			if (!$id) {
				$this->halt("No query specified.");
				return false;
			}
		}

		$count = @mysql_num_fields($id);

		// made this IF due to performance (one if is faster than $count if's)
		if (!$full) {
			for ($i=0; $i<$count; $i++) {
				$res[$i]["table"] = @mysql_field_table ($id, $i);
				$res[$i]["name"]  = @mysql_field_name  ($id, $i);
				$res[$i]["type"]  = @mysql_field_type  ($id, $i);
				$res[$i]["len"]   = @mysql_field_len   ($id, $i);
				$res[$i]["flags"] = @mysql_field_flags ($id, $i);
			}
		} else { // full
			$res["num_fields"]= $count;

			for ($i=0; $i<$count; $i++) {
				$res[$i]["table"] = @mysql_field_table ($id, $i);
				$res[$i]["name"]  = @mysql_field_name  ($id, $i);
				$res[$i]["type"]  = @mysql_field_type  ($id, $i);
				$res[$i]["len"]   = @mysql_field_len   ($id, $i);
				$res[$i]["flags"] = @mysql_field_flags ($id, $i);
				$res["meta"][$res[$i]["name"]] = $i;
			}
		}

		// free the result only if we were called on a table
		if ($table) {
			@mysql_free_result($id);
		}
		return $res;
	}

	/* public: find available table names */
	function table_names() {
		$this->connect();
		$h = @mysql_query("show tables", $this->Link_ID);
		$i = 0;
		while ($info = @mysql_fetch_row($h)) {
			$return[$i]["table_name"]      = $info[0];
			$return[$i]["tablespace_name"] = $this->Database;
			$return[$i]["database"]        = $this->Database;
			$i++;
		}

		@mysql_free_result($h);
		return $return;
	}

	/* public: find table columns and types */
	function table_columns($tableName) {
		$this->connect();
		$strSQL ="SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT"
		." FROM INFORMATION_SCHEMA.COLUMNS"
		." WHERE table_schema='".$this->Database."' and table_name = '$tableName'" ;
		$h = @mysql_query($strSQL, $this->Link_ID);

		while ($info = @mysql_fetch_row($h)) {
			$return[$info[0]] = $info[1];
		}

		@mysql_free_result($h);
		return $return;
	}

	/* private: error handling */
	function halt($msg) {
		$this->Error = @mysql_error($this->Link_ID);
		$this->Errno = @mysql_errno($this->Link_ID);

		if ($this->locked) {
			$this->unlock();
		}

		if ($this->Halt_On_Error == "no")
		return;

		$this->haltmsg($msg);

		if ($this->Halt_On_Error != "report" && $this->Halt_On_Error != "print")
		die("Session halted.");
	}

	function haltmsg($msg) {
		if ($this->Halt_On_Error == "print") {
			printf("<b>Database error:</b> %s<br/>\n", $msg);
			printf("<b>MySQL Error</b>: %s (%s)<br/>\n",
			$this->Errno,
			$this->Error);
		}
		if ($this->Halt_On_Error == "report") {
			$this->Error_Messages[]= "<b>Database error:</b> $msg<br/><b>MySQL Error</b>: ".$this->Errno." ".$this->Error."<br/>";			
		}
	}

}
?>
