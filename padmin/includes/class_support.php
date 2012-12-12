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
 * Class support definition file
 * @author Josep Marxuach  - May 2007
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_support.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_org.php");
//require_once(_DirINCLUDES."class_gallery.php");

/**
 *Handles support - Is a specific class object using table prefix_support
 *@author IT elazos s.l.
 *@version 1.0
 */
class support extends org {
	/**
	 *Error messages after any method execution
	 *@var string
	 */
	var $txt_error;

	function support(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TK_PRIORITY',_IDIOMA);
		$lovs->getLovs('_LST_TK_STATE',_IDIOMA);
	}

	/**
	 *Get ticket() details from the table prefix_tickets
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param integer Ticket id
	 */
	function dtl_ticket($id_ticket){

		$join=$this->prefix."_ticket LEFT JOIN ".$this->prefix."_accounts ON ".$this->prefix."_ticket.id_account = ".$this->prefix."_accounts.id_account";
		if ($this->query("select  ".$this->prefix."_ticket.*, DATE_FORMAT(".$this->prefix."_ticket.dt_create,"._DATE_SQL.") as dt_create, name_account from $join where ".$this->prefix."_ticket.id_ticket=$id_ticket;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." ticket"; return false;}
	}
	/**
	 *Get all ticket messages
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param integer Ticket id
	 */
	function lst_tk_msg($id_ticket){

		$join="".$this->prefix."_tk_msg LEFT JOIN ".$this->prefix."_auth_user ON ".$this->prefix."_tk_msg.user_id = ".$this->prefix."_auth_user.user_id";

		if ($this->query("select id_tk_msg, DATE_FORMAT(".$this->prefix."_tk_msg.dt_create,"._DATE_SQL.") as dt_create,"
		."DATE_FORMAT(".$this->prefix."_tk_msg.dt_create,'%H:%i') as time, username, txt_msg, fg_private"
		."  from $join where ".$this->prefix."_tk_msg.id_ticket=$id_ticket"
		." order by dt_create asc;")) {
			return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." ticket"; return false;
		}
	}
	/**
	 * Select support categories.
	 *
	 * @return True on success or false
	 */
	function lst_ctg(){
		if ($this->query("select id_tk_ctg, nm_tk_ctg from ".$this->prefix."_tk_ctg")) {
			return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." tk_ctg"; return false;
		}
	}
	/**
	 * Gets all messages used for automatic email replies.
	 *
	 * @return True on success or false
	 */
	function GetMsgText(){
		$result=array();
		if ($this->query("select * from ".$this->prefix."_tk_msgtext")) {
			if ( $this->num_rows()>0){
				while($this->next_record_assoc()){
					$result[$this->Record["cod_lang"]][$this->Record["id_msgtext"]]["txt_subject"]=$this->Record["txt_subject"];
					$result[$this->Record["cod_lang"]][$this->Record["id_msgtext"]]["txt_email"]=$this->Record["txt_email"];
				}
			}
			return $result;
		} else {
			$this->txt_error=""._ER_SELET_TBL." tk_msgtext"; return false;
		}
	}
	/**
	 *list prefix_ticket() table rows based on organization, user position, view(My, Team, All), from row number
	 *and offset(rows per page), aditionaly you can link tickets with accounts
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param string Keyword for search on ref_ticket table field
	 *@param integer organization id
	 *@param integer position id
	 *@param string visibility ("My","Team","All")
	 *@param integer inital row number
	 *@param integer number of rows to return
	 *@param integer Account id
	 *@param boolean Type of columns when no position, True: simple, False: complet set of columns.
	 */
	function ver_tickets($fields=NULL, $id_org, $id_position, $view, $from, $offset, $order_by=false, $id_account=false, $tp_cols=false){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		if (isset($id_position)){
			$visible=$this->visibility("_ticket",$view,$id_position,$id_org);
			$columns="id_ticket, ref_ticket, txt_subject, "
			."DATE_FORMAT(dt_create,"._DATETIME_SQL.") as dt_create, ELT(tp_status,"._LST_TK_STATE.") as tp_state , ELT(tp_priority,"._LST_TK_PRIORITY.") as PRIORITY, username";
		}
		else {
			$visible=$this->visibility("_ticket",$view,$id_position,$id_org,false);
			if ($tp_cols) $columns="id_ticket, subject";
			else $columns="id_ticket, subject ";
			$visible["clause_from"]=$visible["clause_from"];
		}

		if (isset($visible["where"])) $where=$visible["where"]." "; else return false;

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

		if ($id_account) $where=$where." and ".$this->prefix."_ticket.id_account=$id_account";

		if ($this->query("select SQL_CALC_FOUND_ROWS $columns from ".$visible["clause_from"]." where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_TICKET.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." TICKET"; return false;}
	}
	/**
	 * Return the number of tickets by status to draw a chart.
	 *
	 * @param integer $id_position
	 * @return array Number of tickets open, waiting and pending
	 */
	function statitics_opened($view, $id_position,  $id_org){
		if (isset($id_position)){
			$visible=$this->visibility("_ticket",$view,$id_position,$id_org);
			$columns="id_ticket, ref_ticket, txt_subject, "
			."DATE_FORMAT(dt_create,"._DATETIME_SQL.") as dt_create, ELT(tp_status,"._LST_TK_STATE.") as tp_state , ELT(tp_priority,"._LST_TK_PRIORITY.") as PRIORITY, username";
		}
		else {
			$visible=$this->visibility("_ticket",$view,$id_position,$id_org,false);
			if ($tp_cols) $columns="id_ticket, subject";
			else $columns="id_ticket, subject ";
			$visible["clause_from"]=$visible["clause_from"];
		}

		if (isset($visible["where"])) $where=$visible["where"]." "; else return false;

		if ($this->query("select tp_status, count(*) as number from ".$visible["clause_from"]." where $where and tp_status<4 group by tp_status order by tp_status asc;"))
		{
			$result=array(0,0,0);
			while($this->next_record()) {
				$result[$this->Record[0]-1]=$this->Record[1];
			}
			return $result;
		}
		else { $this->txt_error=""._ER_SELET_TBL." TICKET"; return false;}

	}
	/**
	 *Update ticket() fields of the table prefix_ticket
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Gallery id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function update_ticket($id_ticket, $user_id, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($key=="txt_msg"||$key=="fg_private") {continue;}
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}
		//echo $st_field;die();
		if ($this->query("update ".$this->prefix."_ticket set $st_field where id_ticket=$id_ticket")){
			if ($this->query("insert into ".$this->prefix."_tk_msg (id_ticket, user_id, dt_create,txt_msg, fg_private) values ($id_ticket, '$user_id', NOW(),".$fields["txt_msg"].",".$fields["fg_private"].")")) {
				$this->txt_error=""._UPDATED."";
				return true;
			} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}

		} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/**
	 *Delete ticket() from the table prefix_ticket and NO delete associated gallery* folder based on table prefix_gallery
	 *@author Josep Marxuach
	 *@access public
	 *@param integer ticket id
	 *@return boolean
	 */
	function delete_ticket($id){
		if ($this->query("delete from ".$this->prefix."_tk_msg where id_ticket=$id;")){
			$this->query("delete from ".$this->prefix."_ticket where id_ticket=$id;");
			$this->txt_error=""._DELETED.""; return true;
		} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 *Add ticket() using fields array to the table prefix_gallery
	 *@author Josep Marxuach
	 *@access public
	 *@param integer position id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean txt_email on success or false
	 */
	function add_ticket($id_org, $id_position, $user_id,$fields){


		if (!is_array($fields)) return false;
		$fields["tp_status"]=1;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($key=="id_position") {$id_position=$value;continue;}
			if ($key=="id_contact") {continue;}
			if ($key=="txt_msg"||$key=="fg_private") {continue;}
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if (array_key_exists("id_contact",$fields) && !array_key_exists("id_account",$fields)) {
			$id_contact=$fields["id_contact"];
			$this->query("select id_account, txt_email from ".$this->prefix."_contacts where id_contact = $id_contact");
			if ($this->num_rows()>0) {
				$this->next_record();
				$st_field.=", id_account, txt_email";
				$st_value.=", ".$this->Record[0].", '".$this->Record[1]."'";
				$fields["txt_email"]=$this->Record[1];
			}
		}
		if ($this->query("insert into ".$this->prefix."_ticket (id_org, id_position, $st_field) values ( $id_org, $id_position, $st_value);")) {
			if ($user_id==null) $user_id="null"; else $user_id="'$user_id'";
			if ($this->query("insert into ".$this->prefix."_tk_msg (id_ticket, user_id, dt_create, txt_msg, fg_private) values (".$this->last_insert_id().", $user_id, NOW(), ".$fields["txt_msg"].",".$fields["fg_private"].")")) {
				$this->txt_error=""._INSERTED."";
				return $fields["txt_email"];
			} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
		} else { $this->txt_error=""._ERROR_INSERT.""; return false;}

	}
	/**
	 *Add ticket message using fields array to the table prefix_tk_msg
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Ticket id
	 *@param integer User id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function add_msg($id_ticket, $user_id,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($key=="fg_closed") continue;
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}
		if ($user_id==null) $user_id="null"; else $user_id="'$user_id'";
		if ($this->query("insert into ".$this->prefix."_tk_msg (id_ticket, user_id, dt_create, $st_field) values ($id_ticket, $user_id, NOW(), $st_value)")) {

			if ($fields["fg_private"]=="0"){ //If is not a private msg, we change status
				if (!array_key_exists("fg_closed",$fields)) {
					if ($user_id=="null") $tp_status = "3"; else $tp_status = "2";
				} else {
					if ($fields["fg_closed"]=="1") $tp_status = "4";else
					if ($user_id=="null") $tp_status = "3"; else $tp_status = "2";
				}
					
				if ($this->query("update ".$this->prefix."_ticket set tp_status = $tp_status where id_ticket = $id_ticket")){
					$this->txt_error=""._INSERTED."";
					return true;
				} else {
					$this->txt_error=""._ERROR_INSERT." table prefix_ticket"; return false;
				}
			}
		} else { $this->txt_error=""._ERROR_INSERT." table prefix_tk_msg"; return false;}
	}
	/**
	 * List of Support categories
	 *
	 * @param integer $from
	 * @param integer $offset
	 * @return true on success or false if not
	 */
	function verctg_list( $from, $offset ) {

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($this->Query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->prefix."_tk_ctg $limit;")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}
	/**
	 * Deletes a support category
	 *
	 * @param integer $id_tk_ctg Category id
	 * @return True on success
	 */
	function del_ctg( $id_tk_ctg ) {
		if ($this->query("delete from ".$this->prefix."_tk_ctg where id_tk_ctg=$id_tk_ctg;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Gets all category data
	 *
	 * @param integer $id_tk_ctg
	 * @return True on success
	 */
	function ctg_dtl($id_tk_ctg){

		if ($this->query("select * from ".$this->prefix."_tk_ctg where id_tk_ctg=$id_tk_ctg;"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." TK_CTG"; return false;}
	}
	/**
	 * Updates a Support category
	 *
	 * @param integer $view_id
	 * @param array $fields
	 * @return True on Success or false
	 */
	function update_ctg($id_tk_ctg,$fields) {

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_tk_ctg set $st_field where id_tk_ctg=$id_tk_ctg;"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Adds a support category
	 *
	 * @param Assoc_array $fields
	 * @return True on success
	 */
	function add_ctg($fields) {

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields)) {
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_tk_ctg ( $st_field ) values ( $st_value );"))
		{$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Returns a list of organization depts
	 *
	 * @param integer $id_org Organization id
	 * @return True on success or false
	 */
	function org_lst($id_org){
		if ($this->query("select id_org, name_org from ".$this->prefix."_org where root_id_org = $id_org"))
		{if ($this->num_rows()==0) return false;
		else {
			$prfs=$this->select_array();
			foreach($prfs as $value) $result[$value["id_org"]]=$value["name_org"];
			return $result;
		}
		}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $txt_email
	 * @return True on success on false
	 */
	function GetContactAccount( $txt_email){
		if ($this->query("select con.id_contact, con.nm_contact, con.id_account, acc.cod_lang from ".$this->prefix."_contacts con, ".$this->prefix."_accounts acc "
		." where con.id_account =  acc.id_account and con.txt_email = '$txt_email'"))
		{if ($this->num_rows()==0) return false;
		else {
			return true;
		}
		}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $ref_ticket
	 * @return id_ticket on success or false
	 */
	function GetTicketByRef($ref_ticket){
		if ($this->query("select id_ticket from ".$this->prefix."_ticket where ref_ticket = '$ref_ticket'")){
			if ($this->num_rows()==0) return false;
			else {
				$this->next_record();
				return $this->Record[0];
			}
		} else {
			$this->txt_error=""._ER_SELET_TBL." TICKET";
			return false;
		}
	}
	/**
	 * Adds organization that handles the category support tickets
	 *
	 * @param integer $id_tk_ctg
	 * @param Assoc_Array $fields
	 * @return True on succes or false
	 */
	function add_ctg_orgs( $id_tk_ctg, $fields ) {

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

		if ($this->query("insert into ".$this->prefix."_tk_ctg_org (id_tk_ctg, $st_field) values ($id_tk_ctg, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * List all organizations that handles a support category
	 *
	 * @param integer $id_tk_ctg
	 * @return unknown
	 */
	function ctg_orgs_list( $id_tk_ctg ) {
		if ($this->Query("select ctg.id_org, name_org FROM ".$this->prefix."_tk_ctg_org ctg, ".$this->prefix."_org org where org.id_org = ctg.id_org and ctg.id_tk_ctg = $id_tk_ctg")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}
	/**
	 * Deletes an organization associated with a category.
	 * The employees of the organization handles the support tickets of the category
	 *
	 * @param integer $id_tk_ctg
	 * @param integer $id_org
	 * @return True on success or false
	 */
	function del_ctg_org( $id_tk_ctg, $id_org ) {
		if ($this->query("delete from ".$this->prefix."_tk_ctg_org where id_tk_ctg=$id_tk_ctg and id_org=$id_org;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Generates a unique ticket reference identifier
	 *
	 * @return Ticket Reference Identifierd
	 */
	function GenerateTkRef(){
		return "T".strtoupper(substr(md5(uniqid("")), 0, 9));
	}
	/**
	 * Adds a support email templat
	 *
	 * @param Assoc_array $fields
	 * @return True on success
	 */
	function add_emailTemplate($fields) {

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields)) {
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_tk_msgtext ( $st_field ) values ( $st_value );"))
		{$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Updates a Support Email template
	 *
	 * @param integer $id_msgtext
	 * @param array $fields
	 * @return True on Success or false
	 */
	function update_emailTemplate($id_msgtext,$fields) {

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_tk_msgtext set $st_field where cod_lang=".$fields["cod_lang"]." and id_msgtext='$id_msgtext'"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 *Check ticket() fields before any add or update to the table prefix_ticket
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
				case "nm_tk_ctg":
				case "pophost":
				case "popuser":
				case "poppass":
				case "txt_sign":
				case "txt_subject":
				case "nm_account":
				case "txt_email":
					if (strlen($value)==0) {$this->txt_error=_MANDATORY;return false;}
					$fields[$key]="'".mysql_escape_string($value)."'";
					break;
				case "txt_msg":
					if (strlen($value)==0) {$this->txt_error=_MANDATORY;return false;}
					$fields[$key]="'".mysql_escape_string($value)."'";
					break;
				case "ref_ticket":
					if (strlen($value)==0) $fields[$key]="'".GenerateTkRef."'";
					else $fields[$key]="'".$value."'";
					break;

				case "txt_phone":
				case "txt_ip":
				case "txt_trans_msg":
					if($fields[$key]!="") $fields[$key]="'".mysql_escape_string($value)."'"; else $fields[$key]="null";
					break;
				case "dt_create":
					//if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					//    {$this->txt_error=""._ERROR_IN." "._DT_VALID;return false;}
					$fields[$key]="NOW()";
					break;
				case "id_msgtext";
				case "cod_lang":
					$fields[$key]="'$value'";
					break;
				case "id_org";
				case "id_tk_ctg";
				case "id_account":
				case "id_contact":
				case "id_position":
				case "id_tk_group":
				case "tp_status":
				case "tp_priority":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "fg_private":
				case "fg_closed":
				case "fg_hidden":
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