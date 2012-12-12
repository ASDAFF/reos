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
 * Class Booking definition file.
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_booking.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Booking.
 * Handles Booking for properties.
 **/
class booking extends utils {
	var $txt_error;

	/**
	 * Class Constructor
	 * Get list of values for listbox and others.
	 *
	 * @return booking
	 */
	function booking(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TP_RESERVSTATE',_IDIOMA);
	}

	/**
	 * Deletes a Booking.
	 *
	 * @param integer $id Booking id
	 * @param integer $id_org Organization Id
	 * @return boolean True on success or false
	 */
	function delete_booking($id){
		if ($this->query("delete from ".$this->prefix."_bookings where id_bookings=$id;")){
			$this->txt_error=""._DELETED."";
			return true;
		} else {
			$this->txt_error=""._ERROR_DELETE."";
			return false;
		}
	}

	/**
	 * Updates a booking
	 *
	 * @param integer $id_bookings Booking id
	 * @param array $fields Assoc array[fieldname]=value to be updated
	 * @return boolean True on success or false
	 */
	function update_booking($id_bookings, $fields, $int_capacity){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // ckeck fields

		if ($fields["int_pers"]>$int_capacity){
			$this->txt_error = _INT_CAPACITY."=".$int_capacity;
			return false;
		}
		if ($fields["tp_state"]=="3") {
			if ($this->query("select id_bookings from ".$this->prefix."_bookings"
			." where id_bookings<>$id_bookings and id_immo=".$fields["id_immo"]." and tp_state = 3"
			." and ((".$fields["dt_start"].">=dt_start and ".$fields["dt_start"]."<=dt_end)"
			." or (".$fields["dt_end"].">=dt_start and ".$fields["dt_end"]."<=dt_end));")){
				if ($this->num_rows()>=1) {$this->txt_error=_BUSYBOOKING;return false; }
			} else {
				$this->txt_error=_ER_SELET_TBL." BOOKINGS";
				return false;
			}
		}

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields)){
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_bookings set $st_field where id_bookings=$id_bookings;"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}

	}

	/**
	 * Adds a new booking
	 *
	 * @param integer $id_org Organization id
	 * @param array $fields Assoc array[fieldname]=value to be added
	 * @return boolean True on success or false
	 */
	function add_booking($fields, $int_capacity = null){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
		
		if ($int_capacity==null) $int_capacity = $this->GetCapacity($fields["id_immo"]);
		
		if ($fields["int_pers"]>$int_capacity){
			$this->txt_error = _INT_CAPACITY."=".$int_capacity;
			return false;
		}
		if (!$this->CheckAvailability($fields["dt_start"],$fields["dt_end"],$fields["id_immo"])) return false;

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields)) {
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_bookings ($st_field) values ($st_value);")){
			$this->txt_error=""._INSERTED."";
			return true;
		} else {
			$this->txt_error=""._ERROR_INSERT."";
			return false;
		}
	}
	/**
	 * Returns booking details in $this->Record assoc array
	 *
	 * @param integer $id_bookings Booking id
	 * @return boolean True on success or false
	 */
	function booking_dtl($id_bookings){
		if ($this->query("select ".$this->prefix."_bookings.*, name_account, ref_immo, int_capacity, "
		."DATE_FORMAT(".$this->prefix."_bookings.dt_create,"._DATE_SQL.") as dt_create,"
		."DATE_FORMAT(".$this->prefix."_bookings.dt_start,"._DATE_SQL.") as dt_start, "
		."DATE_FORMAT(".$this->prefix."_bookings.dt_end,"._DATE_SQL.") as dt_end "
		." from ".$this->prefix."_bookings"
		." left join ".$this->prefix."_accounts on ".$this->prefix."_accounts.id_account = ".$this->prefix."_bookings.id_account"
		." left join ".$this->prefix."_immos on ".$this->prefix."_immos.id_immo = ".$this->prefix."_bookings.id_immo"
		." where id_bookings=$id_bookings;"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=_ER_SELET_TBL." BOOKINGS"; return false;}
	}

	/**
	 * Used to show a list of bookings with a search form.
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
	function booking_list($fields, $from,$offset,$order_by){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		$where="";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="tp_state") {$key=$this->prefix."_bookings.tp_state";}
				if ($st_field=="") $st_field=$key.$operator.$value;
				else $st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($where!="") $where="where ".$where;

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_bookings.id_bookings,  "
		."ref_immo, "
		."DATE_FORMAT(".$this->prefix."_bookings.dt_create,"._DATE_SQL.") as dt_create, "
		."ELT(".$this->prefix."_bookings.tp_state,"._LST_TP_RESERVSTATE.") AS tp_state, "
		."DATE_FORMAT(".$this->prefix."_bookings.dt_start,"._DATE_SQL.") as dt_start, "
		."DATE_FORMAT(".$this->prefix."_bookings.dt_end,"._DATE_SQL.") as dt_end, "
		." int_pers, int_capacity from "
		.$this->prefix."_bookings left join ".$this->prefix."_immos on ".$this->prefix."_bookings.id_immo = ".$this->prefix."_immos.id_immo"
		." $where $order_by $limit;"))
		{if ($this->num_rows()==0) {$this->txt_error=_NOTFOUND;return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." BOOKING"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_org
	 * @param unknown_type $username
	 * @param unknown_type $id_bookings
	 * @return unknown
	 */
	function validate($id_account,$id_bookings){

		if ($this->query("select id_account from ".$this->prefix."_accounts where id_account=$id_account;")) {
			if ($this->num_rows()==1) {
				$this->query("update ".$this->prefix."_accounts set tp_state=1 where id_account=$id_account;");
				$this->query("update ".$this->prefix."_bookings set tp_state=2 where id_bookings=$id_bookings;");
				return true;
			} else return false;
		} else return false;

		return false;
	}
	/**
	 * Get number of people per holiday rental
	 *
	 * @param integer $id_immo Property id
	 * @return integer Number of people per holiday rental or false
	 */
	function GetCapacity($id_immo){
		
		if ($this->query("select int_capacity from ".$this->prefix."_immos where id_immo=$id_immo;")) {
			if ($this->num_rows()==1) {
				$this->next_record();				
				return $this->Record[0];
			} else return false;
		} else return false;

		return false;
		
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $dt_start
	 * @param unknown_type $dt_end
	 * @return unknown
	 */
	function CheckAvailability($dt_start,$dt_end, $id_immo = null){

		$Where_id_immo = "";
		if ($id_immo!=null) $Where_id_immo = " and id_immo=$id_immo";

		if ($this->query("select id_bookings from ".$this->prefix."_bookings"
		." where tp_state = 3 ".$Where_id_immo
		." and (($dt_start>=dt_start and $dt_start<=dt_end)"
		." or ($dt_end>=dt_start and $dt_end<=dt_end));")){
			if ($this->num_rows()>=1) {
				$this->txt_error=_BUSYBOOKING;
				return false;
			} else return true;
		} else {
			$this->txt_error=_ER_SELET_TBL." BOOKINGS";
			return false;
		}

	}
	/**
	 * Checks booking fields before any add or update to the table prefix_bookings
	 *
	 * @param array $fields Assoc array[fieldname]=value to be checked out
	 * @return boolean True on success or false
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields)){
			switch($key){
				case "id_account":
					if ($fields[$key]=="") {$this->txt_error = _IN_NAME_ACCOUNT ;return false;}
					break;
				case "id_immo":
					if ($fields[$key]=="") {$this->txt_error = _IMMO ;return false;}
					break;
				case "tp_state":
					if ($fields[$key]=="") {$this->txt_error = _TP_STATE ;return false;}
					break;
				case "int_pers":
					if($fields[$key]=="0") {$this->txt_error = _INT_PERS;return false;};
					break;
				case "dt_create":
				case "dt_start":
				case "dt_end":
					if ($fields[$key]=="" || !($fields[$key]=$this->date_sql_format($value,_DATE_FORMAT)))
					{$this->txt_error = _ERROR_IN." "._DATE;return false;}
					break;
				case "txt_comment":
					if (strlen($fields[$key])<=2)	$fields[$key]="NULL"; else $fields[$key]="'".addslashes($value)."'";
					break;
				default:
					unset($fields[$key]);
					break;
			}
		}

		if (array_key_exists("dt_start",$fields)){
			if (intval($fields["dt_start"])>intval($fields["dt_end"])) {
				$this->txt_error = _ERROR_IN." "._DATE;
				return false;
			}
		}
		return true;
	}
	//*******************************END CLASS********************************************************
}


?>
