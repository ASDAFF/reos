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
 *Class Account file
 *@author Josep Marxuach  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_account.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 *Class org file
 **/
require_once(_DirINCLUDES."class_org.php");
/**
 *Handles Accounts - using table prefix_accounts.
 *Gestiona clientes con la tabla prefix_accounts.
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by Josep Marxuach
 *@package BusObj
 */
class account extends org {
	/**
	 *Message after execution of any method.
	 *Se almacenan el mensaje generado por la ejecuci�n de un metodo.
	 *@var string
	 */
	var $txt_error;

	/**
	 *Get Account details from the table prefix_accounts
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Account id
	 *@return boolean
	 */
	function dtl_account($id){
		if ($this->query("select ".$this->prefix."_accounts.*,DATE_FORMAT(".$this->prefix."_accounts.dt_create,"._DATE_SQL.") as dt_create, ".$this->prefix."_acc_perfil.ind_active from ".$this->prefix."_accounts LEFT JOIN ".$this->prefix."_acc_perfil ON ".$this->prefix."_accounts.id_account = ".$this->prefix."_acc_perfil.id_account where ".$this->prefix."_accounts.id_account=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." "._ACCOUNTS; return false;}
	}
	/**
	 *Get Account profile details from the table prefix_acc_perfil
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Account id
	 *@return boolean
	 */
	function dtl_accperfil($id){
		if ($this->query("select ".$this->prefix."_acc_perfil.*, ".$this->prefix."_accounts.id_account,DATE_FORMAT(".$this->prefix."_acc_perfil.dt_create,"._DATE_SQL.") as dt_create, ".$this->prefix."_accounts.name_account from ".$this->prefix."_accounts LEFT JOIN ".$this->prefix."_acc_perfil ON ".$this->prefix."_accounts.id_account = ".$this->prefix."_acc_perfil.id_account where ".$this->prefix."_accounts.id_account=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." "._ACCOUNTS; return false;}
	}
	/**
	 * Used to show list of accounds with a search form.
	 * Creates and execute the SQL and leave the result in DB object.
	 * You can get the result as an array  using $this->select_array
	 * Or just making you own loop with $this->next_record()
	 *
	 * @access public
	 * @return boolean True on success or false
	 * @param string Keyword for search on ref_immo table field
	 * @param integer organization id
	 * @param integer position id
	 * @param string visibility ("My","Team","All")
	 * @param integer inital row number
	 * @param integer number of rows to return
	 * @param string Order by field name
	 */
	function ver_accs($fields=NULL, $id_org, $id_position, $view, $from, $offset,$order_by=false,$show_fields=false, $contacts = false){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		if ($view!="Join") {
			$visible=$this->visibility("_accounts",$view,$id_position,$id_org);$join="";
			$campos="".$this->prefix."_accounts.id_account, name_account, DATE_FORMAT(".$this->prefix."_accounts.dt_create,"._DATE_SQL.") as dt_create,  ELT(tp_state,"._LST_TP_ACCSTATE.") as tp_state, ELT(ind_mailing,"._LST_IND_ACTIVE.") as ind_mailing, ".$this->prefix."_auth_user.username, txt_email1, txt_poblacion";
		}
		else
		{$join="".$this->prefix."_accounts INNER JOIN ".$this->prefix."_acc_perfil ON ".$this->prefix."_accounts.id_account = ".$this->prefix."_acc_perfil.id_account";
		//$visible=$this->visibility("_accounts","All",$id_position,$id_org);
		$visible["where"]="".$this->prefix."_accounts.id_org=$id_org";
		$visible["clause_from"]="(".$join.")";
		$campos="".$this->prefix."_accounts.id_account, name_account, precio_compra, precio_alquiler";
		}
			
		if ($show_fields) $campos="*";

		$where="";

		if (isset($visible["where"])) $where=$visible["where"]."$where"; else return false;

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="name_acc_search") {$key="name_account";$value="'%".addslashes($value)."%'";$operator=" like ";}
				if ($key=="id_poblacion") $visible["clause_from"].= " INNER JOIN ".$this->prefix."_pob_acc ON ".$this->prefix."_accounts.id_account = ".$this->prefix."_pob_acc.id_account";
				if ($key=="tp_servicio" || $key=="tp_propiedad" )
				{$st_field=$st_field." AND FIND_IN_SET($value,$key)";continue;}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($contacts) {
			$visible["clause_from"].= " INNER JOIN ".$this->prefix."_contacts ON ".$this->prefix."_accounts.id_account = ".$this->prefix."_contacts.id_account";
			$campos="id_contact, CONCAT(nm_contact,', ',name_account) nm_contact , ".$this->prefix."_contacts.txt_email,  ".$this->prefix."_auth_user.username  ";
		}

		//echo "select id_account, name_account, username, txt_email1, txt_poblacion from ".$visible["clause_from"]." where $where $limit;";

		if ($this->query("select SQL_CALC_FOUND_ROWS $campos from ".$visible["clause_from"]." where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_ACCOUNT.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." "._ACCOUNTS; return false;}
	}

	/**
	 *Updates Account fields of the table prefix_accounts
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Account id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function update_account($id, $fields, $table="_accounts"){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix.$table." set $st_field where id_account=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 *Deletes an account from the table prefix_accounts
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Account id
	 *@return boolean
	 */
	function delete_account($id){

		if ($this->query("delete from ".$this->prefix."_pob_acc where id_account=$id;")
		&& $this->query("delete from ".$this->prefix."_acc_perfil where id_account=$id;")
		&& $this->query("delete from ".$this->prefix."_accounts where id_account=$id;")
		&& $this->query("update ".$this->prefix."_immos set id_account=NULL where id_account=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}

	}
	/**
	 * Adds an Account to the organization using table field array $field to the table prefix_accounts
	 * @author Josep Marxuach
	 * @access public
	 * @param integer organization id
	 * @param integer Position id
	 * @param array Assoc array[fieldname]=value
	 * @return boolean
	 */
	function add_account($id_org, $id_position,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false;

		if (!array_key_exists("username",$fields)) {
			$fields["username"] = $fields["txt_email1"];
			$fields["password"] = "'".substr(md5(uniqid("")), 0, 7)."'";
		}

		if ($this->username_exists($id_org,$fields["username"])) { $this->txt_error=""._ERROR_USRNAME_EXISTS."";return false;}


		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))	{
			if ($key=="id_position") {$id_position=$value;continue;}
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_accounts (id_org, id_position, $st_field) values ($id_org, $id_position, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 *Adds an Account profile using table field array $field to the table prefix_acc_perfil
	 *@author Josep Marxuach
	 *@access public
	 *@param integer id_account
	 *@param integer Position id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function add_profile($id_account, $fields){

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

		if ($this->query("insert into ".$this->prefix."_acc_perfil (id_account, $st_field) values ($id_account, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 *Checks Account fields before any add or update to the table prefix_account
	 *@author Josep Marxuach
	 *@access private
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "name_account":
					if (strlen($value)==0) {$this->txt_error=""._IN_NAME_ACCOUNT."";return false;}
				case "txt_address1":
				case "txt_cif":
				case "txt_poblacion":
				case "txt_cp":
				case "txt_telf1":
				case "txt_telf2":
				case "txt_fax":
				case "txt_email2":
				case "txt_web":
				case "cod_lang":
				case "txt_comment":
					if($fields[$key]=="") $fields[$key]="null";else $fields[$key]="'".addslashes($value)."'";
					break;
				case "ind_active":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "precio_compra":
				case "precio_alquiler":
					if (strpos($value,".")) $fields[$key]=str_replace(".","",$value);
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "num_dormitorios":
					if($fields[$key]=="") {$fields[$key]="NULL";}//$this->txt_error=""._ERROR_NUM_DOM."";return false;};
					break;
				case "num_wc":
					if($fields[$key]=="") {$fields[$key]="NULL";}//{$this->txt_error=""._ERROR_NUM_WC."";return false;};
					break;
				case "username":
					$regexp='/^[.\w-]+@?([\w-]+\.?)+[a-zA-Z]{2,6}$/';					
					if(!preg_match($regexp, $value)){$this->txt_error=""._ERROR_USRNAME_NOSTR."";return false;}
					if ($fields[$key]=="") $fields[$key]="NULL";
					else if (strlen($value)<5) { $this->txt_error=""._ERROR_USRNAME_SHORT.""; return false;}
					else $fields[$key]="'$value'";
					break;
				case "password":
					if(preg_match('/[^A-Za-z0-9]/', $value)){$this->txt_error=""._ERROR_PSW_NOSTR."";return false;}
					if ($fields[$key]=="") $fields[$key]="NULL";
					else if (strlen($value)<5) { $this->txt_error=""._ERROR_PSW_SHORT.""; return false;}
					else $fields[$key]="'$value'";
					break;
				case "dt_create":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_CREATE;return false;}
					break;
				case "tp_propiedad":
				case "tp_servicio":
					if($fields[$key]=="") $fields[$key]="null"; else
					if (is_array($value)) $fields[$key]="'".$this->array2list($value,",")."'";
					break;
				case "id_position":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "name_acc_search":
				case "id_poblacion":
				case "tp_state":
				case "ind_mailing":
					break;
				case "txt_email1":
					if (!preg_match('/^[.\w-]+@([\w-]+\.)+[a-zA-Z]{2,6}$/', $value))
					{$this->txt_error=_MAIL_FORMAT;return false;}
					else $fields[$key]="'".$value."'";
					break;
				default;
				unset($fields[$key]);
				break;
			}

		}
			
		if (array_key_exists("username",$fields)) {
			if ($fields["password"]==$fields["username"] && $fields["username"]!="NULL") { $this->txt_error=""._ERROR_PSW_USERNAME.""; return false;}
			if (($fields["username"]=="NULL" && $fields["password"]!="NULL")
			|| ($fields["username"]!="NULL" && $fields["password"]=="NULL")) {$this->txt_error=""._ERROR_PASSUSR_NULL.""; return false;}
		}

		return true;
	}
	/**
	 *Activa o desactiva una poblaci�n por cliente
	 **/
	function active_pob($id_account, $id_pob){
		if ($this->query("select * from ".$this->prefix."_pob_acc where id_account=$id_account and id_poblacion=$id_pob;"))
		{
			if ($this->num_rows()==0) $todo="add";else $todo="delete";
		} else { $this->txt_error=""._ER_SELET_TBL." POB"; return false;}

		if ($todo=="add")
		{
			if ($this->query("insert into ".$this->prefix."_pob_acc (id_account, id_poblacion) VALUES($id_account, $id_pob);"))
			return true;
			else { $this->txt_error=""._ERROR_INSERT.""; return false;}
		} else
		{
			if ($this->query("delete from ".$this->prefix."_pob_acc where id_account=$id_account and id_poblacion=$id_pob;"))
			return true;
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		}
	}
	/**
	 *Devuelve un array con las poblaciones y zonas de la provincia id.
	 **/
	function ver_pobs($id_account, $id_prov, $keyword, $from, $offset){
		
	if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		
		$where = "";
				
		if ($id_prov != null) $where = $this->prefix."_poblacion.id_prov=$id_prov";		
		
		if (isset($keyword) && $keyword!="" && $keyword!=false) 
		    $where.=" and pob.name_pob like '%$keyword%'";		
		
		$sql="SELECT SQL_CALC_FOUND_ROWS pob.id_poblacion, IF (acc.id_account,'"._SELECTED."','"._NOSELECTED."') as state, pob.name_pob, pro.prov_name, com.comdad_name"
		." FROM ".$this->prefix."_poblacion pob left JOIN tb_pob_acc acc ON pob.id_poblacion = acc.id_poblacion and acc.id_account = $id_account , ".$this->prefix."_provincia pro, ".$this->prefix."_comunidad com where pob.id_prov = pro.id_prov and pro.id_comunidad = com.id_comunidad $where $limit;";

		if ($this->query($sql))
		{ if ($this->num_rows()==0)
		{
			$this->txt_error=""._ERROR_NOPOB."";
			return false;
		} else return true;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 * Devuelve la lista de poblaciones preferidas del cliente.
	 * Se refiere al perfil de compra.
	 **/
	function poblaciones($id_account,$field=true){

		$sql="SELECT ".$this->prefix."_poblacion.id_poblacion, ".$this->prefix."_poblacion.name_pob"
		." FROM ".$this->prefix."_poblacion LEFT JOIN ".$this->prefix."_pob_acc ON ".$this->prefix."_poblacion.id_poblacion = ".$this->prefix."_pob_acc.id_poblacion"
		." WHERE ".$this->prefix."_pob_acc.id_account=$id_account";

		if ($this->query($sql))
		{
			$result="";
			if ($field) $field="name_pob";else $field="id_poblacion";
			while ($this->next_record())
			if ($result=="") $result=$this->Record[$field];else $result=$result.", ".$this->Record[$field];

			return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 *Devuelve la lista de poblaciones seleccionadas por cliente
	 **/
	function pobs_select(){

		if ($this->query("SELECT DISTINCT ".$this->prefix."_pob_acc.id_poblacion, ".$this->prefix."_poblacion.name_pob"
		." FROM ".$this->prefix."_pob_acc INNER JOIN ".$this->prefix."_poblacion ON ".$this->prefix."_pob_acc.id_poblacion = ".$this->prefix."_poblacion.id_poblacion;"))
		{
			if ($this->num_rows()==0)
			{
				$this->txt_error=""._ERROR_NOPOB."";
				return false;
			} else return true;

		}
		else { $this->txt_error=""._ER_SELET_TBL." POB"; return false;}
	}
	/**
	 *Returns username and password to be sent to user.
	 *When forgotten username/password.
	 *@author Josep Marxuach
	 *@access public
	 *@param string eMail
	 *@return boolean If account no exists returns false, otherwise return arrar with username and password
	 */
	function remember($id_org,$email){
		if ($this->query("select name_account, username, password from ".$this->prefix."_accounts where id_org=$id_org and txt_email1='$email';")) {
			if ($this->num_rows()>0) {
				$this->next_record();
				return $this->Record;
			}else return false;
		} else return false;
		return false;
	}
	/**
	 *Checks if the username of an account exists
	 *@author Josep Marxuach
	 *@access private
	 *@param string username
	 *@return boolean
	 */
	function username_exists($id_org,$username){
		if ($this->query("select username from ".$this->prefix."_accounts where id_org=$id_org and username=$username;")) {
			if ($this->num_rows()>0) return true; else return false;
		} else return false;
		return false;
	}
	/**
	 *Confirm username
	 *@author Josep Marxuach
	 *@access public
	 *@param string username
	 *@param string password
	 *@return boolean
	 */
	function confirm($id_org,$username,$password){

		if ($this->query("select id_account from ".$this->prefix."_accounts where id_org=$id_org and username='$username' and password='$password';")) {
			if ($this->num_rows()==1) {

				$this->next_record();$id_account=$this->Record["id_account"];
				if ($this->query("update ".$this->prefix."_accounts set tp_state=1 where id_account=$id_account;"))
				return $id_account; else return false;
					
			} else return false;
		} else return false;

		return false;
	}

	/**
	 *List of active accounts with mail
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 */
	function mailing($id_org,$send_to,$lang=null){

		if ($send_to==1) $where="AND ind_mailing=1 "; else $where="";
		if (isset($lang)) $where .="AND cod_lang='$lang'";

		if ($this->query("select id_account, name_account as name, txt_email1 as email from ".$this->prefix."_accounts where id_org=$id_org and tp_state=1 $where and txt_email1 is not null;")) {
			if ($this->num_rows()>0) {
				return true;
			} else return false;
		} else return false;

		return false;
	}
	/**
	 *List of to validate accounts
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 */
	function ToValidate($id_org){

		$where =" and ind_mailing = 1 ";

		if ($this->query("select id_account, name_account as name, txt_email1 as email, cod_lang, username, password from ".$this->prefix."_accounts where id_org=$id_org and tp_state=3 $where and txt_email1 is not null;")) {
			if ($this->num_rows()>0) {
				return true;
			} else return false;
		} else return false;

		return false;
	}
	/**
	 *Unregister account to Mailings
	 *@author Josep Marxuach
	 *@access public
	 *@param integer id_org
	 *@param integer id_account
	 *@return boolean
	 */
	function unreg($id_org,$id_account){

		if ($this->query("select id_account from ".$this->prefix."_accounts where id_org=$id_org and id_account=$id_account;")) {
			if ($this->num_rows()==1) {
				if ($this->query("update ".$this->prefix."_accounts set ind_mailing=2 where id_org=$id_org and id_account=$id_account;"))
				return $id_account; else return false;

			} else return false;
		} else return false;

		return false;
	}
	/***************************************END CLASS*****************************************************/
}






?>
