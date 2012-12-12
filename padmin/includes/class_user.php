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


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_user.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once("includes/class_org.php");

class user extends org {
	/**
	 * Error message after any class method execution
	 *
	 * @var String
	 */
	var $txt_error;
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	var $clave="La mare que em va pari";

	/**
	 * Deletes a user
	 *
	 * @param String $id User id
	 * @param Integer $id_org Organization Id
	 * @return True on success or false on error
	 */
	function delete_user($id, $id_org){

		if ($this->query("select user_id,id_org, name_position from ".$this->prefix."_position where user_id='$id';"))
		{
			if ($this->num_rows()>0) {$this->txt_error=""._USER_HAS_PST.""; return false;}
		} else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}


		if ($this->query("select user_id from ".$this->prefix."_auth_user where id_org=$id_org;")) {
			if ($this->num_rows()>1) {
				if ($this->query("delete from ".$this->prefix."_auth_user where user_id='$id';"))
				{ $this->txt_error=""._DELETED."";return true;}
				else { $this->txt_error=""._ERROR_DELETE.""; return false;}

			} else {$this->txt_error=""._ERROR_LAST_USER.""; return false;}
		} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Updates user fields
	 *
	 * @param String $user_id User id
	 * @param String_Array $fields User field table values
	 * @return True on success or false on error
	 */
	function update_user($user_id, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_auth_user set $st_field where user_id='$user_id';"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}

	}
	/**
	 * Adds new user
	 *
	 * @param Integer $id_org Organization id
	 * @param String_Array $fields User field table values
	 * @return True on success or false on error
	 */
	function add_user($id_org, $fields ){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		if ($fields["password"]==$fields["username"]) { $this->txt_error=""._ERROR_PSW_USERNAME.""; return false;}
		if ($fields["password"]==$fields["passwd1"]){

			reset($fields);
			$st_field="";
			$st_value="";
			while (list($key,$value)=each($fields))
			{
				if ($key!="passwd1"){
					if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
					$st_field=$st_field.$key;
					$st_value=$st_value.$value;
				}
			}

			$id=md5(uniqid($this->clave));
			if ($this->user_exist($fields["username"]))
			{
				if ($this->query("insert into ".$this->prefix."_auth_user ( user_id, id_org, $st_field) values ( '$id', $id_org, $st_value);"))
				{$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
			} else { $this->txt_error=""._ERROR_USNAME_EXIST.""; return false;}
		} else { $this->txt_error=""._ERROR_PSW_ERROR.""; return false;}

	}
	/**
	 * Find out if a username exists whitin all users
	 *
	 * @param String $username Username
	 * @return True if username exists or false if not
	 */
	function user_exist($username){
		if ($this->query("select username from ".$this->prefix."_auth_user where username=$username;"))
		{if ($this->num_rows()==0) return true; else return false;}
		else return false;
	}
	/**
	 * Gets user full name
	 *
	 * @param Integer $id_uid User id
	 * @return User Full Name or false
	 */
	function name_user($id_uid){
		if ($this->query("select name_user from ".$this->prefix."_auth_user where user_id='$id_uid';"))
		{$this->next_record(); list($name)=($this->Record); return $name;}
		else { $this->txt_error=""._ER_SELET_TBL." USER"; return false;}
	}
	/**
	 * Selects user details.
	 * You can get all fields from $this->Record array.
	 *
	 * @param String $user_id User Id
	 * @return True on success or false on error
	 */
	function user_dtl($user_id){

		if ($this->query("select * from ".$this->prefix."_auth_user where user_id='$user_id';"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." USER"; return false;}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_org
	 * @param unknown_type $from
	 * @param unknown_type $offset
	 * @return unknown
	 */
	function user_list($id_org,$from,$offset){
		//ELT(user_type,"._LST_USER_TYPES.") AS user_type
		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";  //ELT(perms,"._LST_ROLES.") as perms
		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_auth_user.user_id, name_user, username, name_org, ".$this->prefix."_position.id_org  from "
		."(".$this->prefix."_auth_user LEFT JOIN ".$this->prefix."_position ON ".$this->prefix."_auth_user.user_id = ".$this->prefix."_position.user_id) "
		."LEFT JOIN ".$this->prefix."_org ON ".$this->prefix."_position.id_org = ".$this->prefix."_org.id_org "
		." where ".$this->prefix."_auth_user.id_org=$id_org and ".$this->prefix."_auth_user.user_type=1 $limit;"))
		{if ($this->num_rows()==0) return false; else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." USER"; return false;}
	}
	/**
	 * List user without positions and search full name.
	 * You can get result in $this->select_array().
	 * 
	 * @param Integer $id_org
	 * @param Integer $from
	 * @param Integer $offset
	 * @param String $NameSearch
	 * @return True on success or false on error
	 */
	function UserNameSearch($id_org,$from,$offset, $NameSearch){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		
		if (isset($NameSearch) and $NameSearch<>null) 
		$strSqlSearch = $this->prefix."_auth_user.name_user like '%$NameSearch%' and ";
		else  $strSqlSearch="";

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_auth_user.user_id, name_user, username  from "
						."".$this->prefix."_auth_user" 						
						." where "
						.$this->prefix."_auth_user.id_org=$id_org and "
						.$this->prefix."_auth_user.user_type=1 and "
						.$strSqlSearch
						.$this->prefix."_auth_user.user_id not in (select user_id from ".$this->prefix."_position) "
						."$limit;")){
			
			if ($this->num_rows()==0) {
				$this->txt_error=_NO_EMPL;
				return false;
			} else return true;
			
		} else {
			$this->txt_error=""._ER_SELET_TBL." USER";
			return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_uid
	 * @param unknown_type $old_pass
	 * @param unknown_type $pass1
	 * @param unknown_type $pass2
	 * @return unknown
	 */
	function change_pass($id_uid, $old_pass, $pass1, $pass2){

		$fields=array("password"=>$pass1);

		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		if ($this->check_pass($id_uid, $old_pass))
		{
			if ($pass1==$pass2){
				if ($this->query("update ".$this->prefix."_auth_user set password='$pass1' where user_id='$id_uid';"))
				{ $this->txt_error=""._UPDATED.""; return true;}
				else { $this->txt_error=""._ER_SELET_TBL." USER"; return false;}
			} else { $this->txt_error=""._ERROR_PSW_ERROR.""; return false;}
		} else { $this->txt_error=""._ERROR_CURRENT_PSW.""; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_uid
	 * @param unknown_type $pass
	 * @return unknown
	 */
	function check_pass($id_uid, $pass){
		if ($this->query("select password from ".$this->prefix."_auth_user where user_id='$id_uid';"))
		{
			$this->next_record();
			list($name)=($this->Record);
			if ($name==$pass) return true;else return false;
		}
		else { $this->txt_error=""._ERROR_CHECK_PSW.""; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $fields
	 * @return unknown
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "name_user":
					if (strlen($value)<5) { $this->txt_error=""._ERROR_NAME_USER.""; return false;}
					$fields[$key]="'$value'";
					break;
				case "password":
					if(preg_match('/[^A-Za-z0-9]/', $value)){$this->txt_error=""._ERROR_PSW_NOSTR."";return false;}
					if (strlen($value)<5) { $this->txt_error=""._ERROR_PSW_SHORT.""; return false;}
					$fields[$key]="'$value'";
					break;
				case "passwd1":
					$fields[$key]="'$value'";
					break;
				case "username":
					if(preg_match('/[^A-Za-z0-9]/', $value)){$this->txt_error=""._ERROR_USRNAME_NOSTR."";return false;}
					if (strlen($value)<5) { $this->txt_error=""._ERROR_USRNAME_SHORT.""; return false;}
					$fields[$key]="'$value'";
					break;
				case "perms":
					$fields[$key]=$value;
					break;
				case "user_type":
					// sempre es 1 = empleado
					break;
				case "txt_address":
					$fields[$key]="'$value'";
					break;
				case "txt_poblacion":
					$fields[$key]="'$value'";
					break;
				case "txt_cp":
					$fields[$key]="'$value'";
					break;
				case "txt_telf1":
					if ($value=="") {$this->txt_error=""._ERROR_TELF1_USER."";return false;}
					else $fields[$key]="'$value'";
					break;
				case "txt_email":
					if ($value=="") {$this->txt_error=""._ERROR_EMAIL_USER."";return false;}
					else $fields[$key]="'$value'";
					break;
				case "txt_telf2":
					$fields[$key]="'$value'";
					break;
				case "txt_fax":
					$fields[$key]="'$value'";
					break;
				case "txt_web":
					$fields[$key]="'$value'";
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
