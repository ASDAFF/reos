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
 *Class immo definition file (Real State Management)
 *@author Josep Marxuach  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_immo.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_org.php");
require_once(_DirINCLUDES."class_gallery.php");

/**
 *Handles Immo(Properties or real state) - Is a specific class object using table prefix_immos
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by Josep Marxuach
 */
class immo extends org {
	/**
	 *Error messages after any method execution
	 *@var string
	 */
	var $txt_error;

	function immo(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);
		$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
		$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
		$lovs->getLovs('_LST_TP_STATE',_IDIOMA);
		$lovs->getLovs('_LST_PISCINA',_IDIOMA);
	}

	/**
	 *Get immo(Property) details from the table prefix_immos
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param integer immo id
	 */
	function dtl_immo($id){

		$join="".$this->prefix."_immos LEFT JOIN ".$this->prefix."_accounts ON ".$this->prefix."_immos.id_account = ".$this->prefix."_accounts.id_account";

		if ($this->query("select  ".$this->prefix."_immos.*, DATE_FORMAT(".$this->prefix."_immos.dt_create,"._DATE_SQL.") as dt_create, DATE_FORMAT(".$this->prefix."_immos.dt_valid,"._DATE_SQL.") as dt_valid, ".$this->prefix."_accounts.name_account from $join where ".$this->prefix."_immos.id_immo=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." IMMO"; return false;}
	}
	/**
	 *list prefix_immos(Properties) table rows based on organization, user position, view(My, Team, All), from row number
	 *and offset(rows per page), aditionaly you can link immos with accounts
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param string Keyword for search on ref_immo table field
	 *@param integer organization id
	 *@param integer position id
	 *@param string visibility ("My","Team","All")
	 *@param integer inital row number
	 *@param integer number of rows to return
	 *@param integer Account id
	 *@param boolean Type of columns when no position, True: simple, False: complet set of columns.
	 */
	function ver_immos($fields=NULL, $id_org, $id_position, $view, $from, $offset, $order_by=false, $id_account=false, $tp_cols=false){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		if (isset($id_position)){
			$visible=$this->visibility("_immos",$view,$id_position,$id_org);
			$columns="id_immo, ref_immo, DATE_FORMAT(".$this->prefix."_immos.dt_create,"._DATE_SQL.") as dt_create, ELT(tp_state,"._LST_TP_STATE.") as tp_state, DATE_FORMAT(".$this->prefix."_immos.dt_valid,"._DATE_SQL.") as dt_valid, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, txt_poblacion, txt_zona, username, precio, ELT(tp_price,"._LST_TP_PRICE.") as tp_price";
		}
		else {
			$visible=$this->visibility("_immos",$view,$id_position,$id_org,false);
			if ($tp_cols) $columns="id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_state,"._LST_TP_STATE.") as tp_state, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, txt_poblacion, txt_zona, precio,ELT(tp_price,"._LST_TP_PRICE.") as tp_price";
			else $columns="id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ind_piscina, txt_poblacion, txt_zona, precio, num_dormitorios, num_wc, int_superficie, int_superficie_const, int_terrace, num_parking, img_front, dir_gal, set_properties, set_intro, ELT(tp_price,"._LST_TP_PRICE.") as tp_price, id_seasons ";
			$visible["clause_from"]=$visible["clause_from"]." LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_immos.id_gal=".$this->prefix."_gallery.id_gal";
		}



		if (isset($visible["where"])) $where=$visible["where"]." "; else return false;

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="precio_min") {$key="precio";$operator=">=";}
				if ($key=="precio_max") {$key="precio";$operator="<=";}
				if ($key=="dt_isvalid") {$key="dt_valid";$operator=">=";}
				if ($key=="ind_isvalid") {
					$key="dt_valid";
					if ($value==1) $operator=">=";else $operator="<";
					$value=$this->date_sql_format(date(""._DATE_FORMAT.""),""._DATE_FORMAT."");
				}
				if ($key=="dt_create") {$operator=">=";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($id_account) $where=$where." and ".$this->prefix."_immos.id_account=$id_account";

		if ($this->query("select SQL_CALC_FOUND_ROWS $columns from ".$visible["clause_from"]." where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_IMMO.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." IMMO"; return false;}
	}

	/**
	 *Update immo(Property) fields of the table prefix_immos
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Gallery id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function update_immo($id, $fields){

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
		if ($this->query("update ".$this->prefix."_immos set $st_field where id_immo=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/**
	 *Delete immo(Property) from the table prefix_immos and NO delete associated gallery* folder based on table prefix_gallery
	 *@author Josep Marxuach
	 *@access public
	 *@param integer immo id
	 *@return boolean
	 */
	function delete_immo($id){
		$num_rows=0;
		if ($this->query("select id_gal from ".$this->prefix."_immos where id_immo=$id;")){
			$num_rows=$this->num_rows();
			if ($num_rows==1) {
				$this->next_record();
				if (isset($this->Record["id_gal"]) && $this->Record["id_gal"]!="") {
					require_once(_DirINCLUDES."class_prefs.php");

					$prefs = new prefs;

					$prefs->getPrefId("_IMAGE_SIZE");
					$prefs->getPrefId("_THUMB_SIZE");
					$dbi= new gallery(_IMAGE_SIZE,_THUMB_SIZE);
					$dbi->delete_gallery($this->Record["id_gal"]);
				}
			}
		}
		if ($num_rows==1) {
			if ($this->query("delete from ".$this->prefix."_point where id_immo=$id;")			
			&& $this->query("delete from ".$this->prefix."_immos where id_immo=$id;"))
			{ $this->txt_error=""._DELETED.""; return true;}
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		}
	}
	/**
	 *Add immo(Property) using fields array to the table prefix_gallery
	 *@author Josep Marxuach
	 *@access public
	 *@param integer organization id
	 *@param integer position id
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function add_immo($id_org, $id_position,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($key=="id_position") {$id_position=$value;continue;}
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}
		if ($this->query("insert into ".$this->prefix."_immos (id_org, id_position, $st_field) values ($id_org, $id_position, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 *Get google point details from the table prefix_point
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param integer immo id
	 */
	function dtl_point($id){

		$table=$this->prefix."_point";

		if ($this->query("select * from $table where id_immo=$id;")){
			if ($this->num_rows()>0){
				$this->next_record();
				return true;
			} else return false;
		}
		else {$this->txt_error=""._ER_SELET_TBL." IMMO POINT"; return false;}
	}
	/**
	 *Get google point details from the table prefix_point
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean
	 *@param integer immo id
	 */
	function upd_point($txt_x, $txt_y, $id){

		$table=$this->prefix."_point";

		// Remove point if $txt_x or txt_y are empty

		if($txt_x=="" || $txt_y=="") {
			$this->query("delete from $table where id_immo=$id;");
			return true;
		}

		if ($this->query("select * from $table where id_immo=$id;")){
			if ($this->num_rows()>0){
				$this->query("update $table set txt_x= '$txt_x' , txt_y = '$txt_y' where id_immo=$id;");
			} else {
				$this->query("insert into $table (txt_x, txt_y, id_immo) values ('$txt_x','$txt_y',$id);");
			}
			return true;
		} else {$this->txt_error=""._ER_SELET_TBL." IMMO POINT"; return false;}
	}
	/**
	 * Search properties wihout reservations.
	 * Use dt_start_bk and dt_start_bk as start and end data of the reservation.
	 * All other immo fields can be used.
	 *
	 * @param unknown_type $fields
	 * @param unknown_type $id_org
	 * @param unknown_type $from
	 * @param unknown_type $offset
	 * @param unknown_type $order_by
	 * @return True on success, or false
	 */
	function search_booking($fields = NULL, $id_org,$from, $offset, $order_by = false, $isAdmin = false){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		if (!$isAdmin) {
			$columns=$this->prefix."_immos.id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ind_piscina, txt_poblacion, txt_zona, precio, num_dormitorios, num_wc, int_superficie, int_superficie_const, int_terrace, num_parking, img_front, dir_gal, set_properties, set_intro, ELT(tp_price,"._LST_TP_PRICE.") as tp_price, id_seasons ";
			$visible["clause_from"]=$this->prefix."_immos LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_immos.id_gal=".$this->prefix."_gallery.id_gal";
		} else {
			$columns=$this->prefix."_immos.id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ELT(ind_piscina,"._LST_PISCINA.") as ind_piscina, txt_poblacion, txt_zona, num_dormitorios, num_wc, int_superficie, int_superficie_const, int_terrace, num_parking, precio,ELT(tp_price,"._LST_TP_PRICE.") as tp_price ";
			$visible["clause_from"]=$this->prefix."_immos";	
		}

		$where = $this->prefix."_immos.id_org = $id_org";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";			
			while (list($key,$value)=each($fields)){
				$operator="=";	
				if ($key=="dt_start_bk" || $key=="dt_end_bk") continue;			
				if ($key=="precio_min") {$key="precio";$operator=">=";}
				if ($key=="precio_max") {$key="precio";$operator="<=";}
				if ($key=="dt_isvalid") {$key="dt_valid";$operator=">=";}
				if ($key=="dt_create")  {$operator=">=";}

				if ($key=="ind_isvalid") {
					$key="dt_valid";
					if ($value==1) $operator=">=";else $operator="<";
					$value=$this->date_sql_format(date(""._DATE_FORMAT.""),""._DATE_FORMAT."");
				}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if (array_key_exists("dt_start_bk",$fields) && array_key_exists("dt_end_bk",$fields)) {			
			$dt_values = "tp_state = 3"
			." and ((".$fields["dt_start_bk"].">=dt_start and ".$fields["dt_start_bk"]."<=dt_end)"
			." or (".$fields["dt_end_bk"].">=dt_start and ".$fields["dt_end_bk"]."<=dt_end))";
			$where.=" and ".$this->prefix."_immos.id_immo not in (select id_immo from ".$this->prefix."_bookings where $dt_values)";
		}

		if ($this->query("select SQL_CALC_FOUND_ROWS $columns from ".$visible["clause_from"]." where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_IMMO.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." IMMO"; return false;}
	}
	/**
	 *Get email of the owner of the property
	 *@author IT ELAZOS S.L.
	 *@access public
	 *@return String Email Address or blank
	 *@param String Ref Immo 
	 */
	function GetEmailOwner($ref_immo){

		$join="".$this->prefix."_immos LEFT JOIN ".$this->prefix."_accounts ON ".$this->prefix."_immos.id_account = ".$this->prefix."_accounts.id_account";

		if ($this->query("select ".$this->prefix."_accounts.txt_email1 as txt_email from $join where ".$this->prefix."_immos.ref_immo = '$ref_immo';")){
			if ($this->num_rows()==1){
				$this->next_record();return $this->Record["txt_email"];
			} else return "";
		} else {$this->txt_error=""._ER_SELET_TBL." IMMO"; return "";}
	}

	/**
	 * Check immo(Property) fields before any add or update to the table prefix_immos
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
				case "ref_immo":
					if (strlen($value)==0) {$this->txt_error=""._IN_NAME_IMMO."";return false;}
					$fields[$key]="'$value'";
					break;
				case "txt_poblacion":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'";
					else {$this->txt_error=""._IN_POB_IMMO."";return false;}
					break;
				case "txt_address1":
				case "txt_comment":
				case "txt_zona":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'"; else $fields[$key]="null";
					break;
				case "set_observ":
				case "set_equip":
				case "set_activities":
				case "set_services":
				case "set_intro":
				case "set_properties":
					if($fields[$key]=="") $fields[$key]="null"; else $fields[$key]="'".$this->array2list($value,",")."'";
					break;
				case "add_zona":
					if($fields[$key]!="") $fields["txt_zona"]="'".addslashes($value)."'";
					unset($fields[$key]);
					break;
				case "txt_cp":
					$fields[$key]="'$value'";
					break;
				case "num_dormitorios":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_NUM_DOM."";return false;};
					break;
				case "num_wc":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_NUM_WC."";return false;};
				case "num_parking":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_NUM_PARKING."";return false;};
					break;
				case "int_superficie":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_SUPERF."";return false;};
					break;
				case "int_terrace":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_SUPERT."";return false;};
					break;
				case "int_capacity":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_CAPACITY."";return false;};
					break;
				case "int_superficie_const":
					if($fields[$key]=="") {$this->txt_error=""._ERROR_SUPERF_CONST."";return false;};
					break;
				case "precio":
					if (strpos($value,".")) $fields[$key]=str_replace(".","",$value);
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "tp_servicio":
				case "tp_price":
				case "tp_propiedad":
				case "ind_piscina":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "dt_create":
				case "dt_start_bk":
				case "dt_end_bk":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DATE;return false;}
					break;
				case "dt_valid":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_VALID;return false;}
					if ($fields["dt_create"]>=$fields[$key]) {$this->txt_error=""._ERROR_IN." "._DT_VALID;return false;}
					break;
				case "id_seasons":
				case "id_account":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "id_gal":
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "tp_state":
					if($fields[$key]=="") $fields[$key]=1;
					break;
				case "ind_oferta":
					if($fields[$key]=="") $fields[$key]=2;
					break;
				case "precio_min":
					break;
				case "precio_max":
					break;
				case "dt_isvalid":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_VALID;return false;}
					break;
				case "ind_isvalid":
					break;
				case "id_position":
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
