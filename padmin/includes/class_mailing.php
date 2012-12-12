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
 * Class Mailing definition file.
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_mailing.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Mailing.
 * Handles mailing jobs.
 **/
class mailing extends utils {
	var $txt_error;

	/**
	 * Class Constructor
	 * Get list of values for listbox and others. 
	 *
	 * @return mailing
	 */
	function mailing(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TP_STE_MAIL',_IDIOMA);
	}

	/**
	 * Deletes a mailing.
	 *
	 * @param integer $id Mailing id
	 * @param integer $id_org Organization Id
	 * @return boolean True on success or false
	 */
	function delete_mailing($id, $id_org){

		if ($this->query("select id_mailing from ".$this->prefix."_mailing where id_org=$id_org;")) {
			if ($this->num_rows()>0) {
				if ($this->query("delete from ".$this->prefix."_mailing where id_mailing=$id;"))
				{ $this->txt_error=""._DELETED."";return true;}
				else { $this->txt_error=""._ERROR_DELETE.""; return false;}
			} else { $this->txt_error=""._ERROR_DELETE.""; return false;}

		} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}

	/**
	 * Updates a mailing
	 *
	 * @param integer $id_mailing Mailing id
	 * @param array $fields Assoc array[fieldname]=value to be updated
	 * @return boolean True on success or false
	 */
	function update_mailing($id_mailing, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // ckeck fields

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_mailing set $st_field where id_mailing=$id_mailing;"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}

	}

	/**
	 * Adds a new mailing
	 *
	 * @param integer $id_org Organization id
	 * @param array $fields Assoc array[fieldname]=value to be added
	 * @return boolean True on success or false
	 */
	function add_mailing($id_org, $fields ){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_mailing ( id_org, $st_field) values ( $id_org, $st_value);"))
		{$this->txt_error=""._INSERTED."";return true;}
		else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Returns mailing details in $this->Record assoc array
	 *
	 * @param integer $id_mailing Mailing id
	 * @return boolean True on success or false
	 */
	function mailing_dtl($id_mailing){

		if ($this->query("select *,DATE_FORMAT(".$this->prefix."_mailing.dt_create,"._DATE_SQL.") as dt_create,DATE_FORMAT(".$this->prefix."_mailing.dt_sent,"._DATE_SQL.") as dt_sent from ".$this->prefix."_mailing where id_mailing=$id_mailing;"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." MAILING"; return false;}
	}

	/**
	 * Used to show a list of mailings with a search form.
	 * It just creates and execute the SQL and leave the result in DB object.
	 * You can get the result as an array using $this->select_array()
	 * Or just making you own loop with $this->next_record().
	 * 
	 * @param array $fields Assoc array[fieldname]=value used to create WHERE SQL statement
	 * @param integer $id_org Organization id
	 * @param integer $from
	 * @param integer $offset number of rows to return by page
	 * @param string $order_by list of fields separated by comma for order by SQL statement
	 * @return boolean True on success or false
	 */
	function mailing_list($fields, $id_org,$from,$offset,$order_by){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";
		$where=" where ".$this->prefix."_mailing.id_org=$id_org ";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="name_mail_search") {$key="name_mailing";$value="'%".addslashes($value)."%'";$operator=" like ";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_mailing.id_mailing, name_mailing, "
		."ELT(tp_state,"._LST_TP_STE_MAIL.") AS tp_state, "
		."DATE_FORMAT(".$this->prefix."_mailing.dt_create,"._DATE_SQL.") as dt_create, "
		."DATE_FORMAT(".$this->prefix."_mailing.dt_sent,"._DATE_SQL.") as dt_sent from "
		.$this->prefix."_mailing"
		." $where $order_by $limit;"))
		{if ($this->num_rows()==0) {$this->txt_error=""._NO_MAILING."";return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." MAILING"; return false;}
	}
	/**
	 * Gets the mailing list that must be sent.
	 *
	 * @param unknown_type $id_org 
	 * @return boolean True on success or false
	 */
	function mail_jobs($id_org){

		$where=" where id_org=$id_org and tp_state=1 and dt_sent<=".$this->date_sql_format(date(""._DATE_FORMAT.""),""._DATE_FORMAT."");

		if ($this->query("select * from ".$this->prefix."_mailing  $where ;"))
		{if ($this->num_rows()==0) {$this->txt_error=""._NO_MAILING."";return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." MAILING"; return false;}
	}
	/**
	 * Checks mailing fields before any add or update to the table prefix_mailing
	 *
	 * @param array $fields Assoc array[fieldname]=value to be checked out
	 * @return boolean True on success or false
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "name_mailing":
					if (strlen($value)<5) { $this->txt_error=""._ERROR_NAME_MAILING.""; return false;}
					$fields[$key]="'".addslashes($value)."'";
					break;
				case "dt_create":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_CREATE;return false;}
					break;
				case "dt_sent":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_SENT;return false;}
					if (array_key_exists("dt_create",$fields) && $fields[$key]<$fields["dt_create"])
					{$this->txt_error=""._ERROR_IN." "._DT_SENT;return false;}
					break;
				case "txt_subject":
					if ($value=="") {$this->txt_error=""._ERROR_SUBJ_MAILING."";return false;}
					else $fields[$key]="'".addslashes($value)."'";
					break;
				case "txt_content":
					if ($value=="") {$this->txt_error=""._ERROR_CONTENT_MAILING."";return false;}
					else $fields[$key]="'".addslashes($value)."'";
					break;
				case "txt_idioma":
					$fields[$key]="'".$value."'";
					break;
				case "tp_send":
				case "tp_send_to":
				case "tp_state":
				case "id_news":
				case "num_sent":
				case "name_mail_search":
					break;
				default:
					unset($fields[$key]);
					break;
			}
		}
		return true;
	}
	//*******************************END CLASS********************************************************
}


?>
