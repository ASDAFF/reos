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
 * Class account contacts definition file
 * @author IT elazos s.l.  - Mars 2008
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_contacts.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_org.php");

/**
 * Handles account contacts - Is a specific class object using table prefix_contacts
 * @author IT elazos s.l.
 * @version 1.0
 */
class contact extends org {
	/**
	 * Error messages after any method execution
	 * @var string
	 */
	var $txt_error;

	/**
	 * Get contact details from the table prefix_contacts
	 * @author IT elazos s.l.
	 * @access public
	 * @return boolean
	 * @param integer contact id
	 */
	function dtl_contact($id){

		$join="".$this->prefix."_contacts LEFT JOIN ".$this->prefix."_accounts ON ".$this->prefix."_contacts.id_account = ".$this->prefix."_accounts.id_account";

		if ($this->query("select  ".$this->prefix."_contacts.*, DATE_FORMAT(".$this->prefix."_contacts.dt_create,"._DATE_SQL.") as dt_create, ".$this->prefix."_accounts.name_account from $join where ".$this->prefix."_contacts.id_contact=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." CONTACT"; return false;}
	}
	/**
	 * list prefix_contacts table rows based on organization, user position, view(My, Team, All), from row number
	 * and offset(rows per page), aditionaly you can link contacts with accounts
	 * @author IT elazos s.l.
	 * @access public
	 * @return boolean
	 * @param Array Search values
	 * @param integer inital row number
	 * @param integer number of rows to return
	 * @param integer Account id
	 * @param boolean Type of columns when no position, True: simple, False: complet set of columns.
	 */
	function ver_contacts($fields=NULL, $from, $offset, $order_by=false, $id_account=false){
		$where = "";

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		$columns="id_contact,  nm_contact";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="dt_create") {$operator=">=";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($id_account)
		if ($where!="") $where=$where." and ".$this->prefix."_contacts.id_account=$id_account";
		else $where=" ".$this->prefix."_contacts.id_account=$id_account";

		if ($this->query("select SQL_CALC_FOUND_ROWS $columns from  ".$this->prefix."_contacts where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_CONTACT.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." CONTACT"; return false;}
	}

	/**
	 * Update contact fields of the table prefix_contacts
	 * @author IT elazos s.l.
	 * @access public
	 * @param integer Contact id
	 * @param array Assoc array[fieldname]=value
	 * @return boolean
	 */
	function update_contact($id, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		if (isset($fields["tp_servicio"]) && $fields["tp_servicio"]!=3)
		{$fields["id_seasons"]="NULL";$fields["set_services"]="NULL";
		$fields["set_observ"]="NULL";$fields["set_activities"]="NULL";
		$fields["int_capacity"]="NULL"; $fields["set_equip"]="NULL"; }

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}
		//echo $st_field;die();
		if ($this->query("update ".$this->prefix."_contacts set $st_field where id_contact=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/**
	 * Delete contact from the table prefix_contacts
	 * @author IT elazos s.l.
	 * @access public
	 * @param integer contact id
	 * @return boolean
	 */
	function delete_contact($id){

		if ($this->query("delete from ".$this->prefix."_contacts where id_contact=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}

	}
	/**
	 * Add contact using fields array to the table prefix_contacts
	 * @author IT elazos s.l.
	 * @access public
	 * @param array Assoc array[fieldname]=value
	 * @return boolean
	 */
	function add_contact($fields){

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
		if ($this->query("insert into ".$this->prefix."_contacts ($st_field) values ( $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Check contact fields before any add or update to the table prefix_contacts
	 * @author IT elazos s.l.
	 * @access private
	 * @param array Assoc array[fieldname]=value
	 * @return boolean
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "txt_comment":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'"; else $fields[$key]="null";
					break;
				case "txt_email":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'"; else
					{$this->txt_error=""._ERROR_IN." "._TXT_EMAIL;return false;};
					break;
				case "nm_contact":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'"; else
					{$this->txt_error=""._ERROR_IN." "._NM_CONTACT;return false;};
					break;
				case "dt_create":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_VALID;return false;}
					break;
				case "id_account":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				default:
					unset($fields[$key]);
					break;
			}
			 
		}
		return true;
	}
	/***************************************END CLASS*****************************************************/
}

?>