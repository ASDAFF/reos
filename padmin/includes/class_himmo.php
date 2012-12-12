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
 * Class holiday rentals definition file
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_himmo.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_org.php");
require_once(_DirINCLUDES."class_gallery.php");

/**
 * Handles Immo(Properties or real state) - Is a specific class object using table prefix_himmos
 * @version 1.0
 * @copyright 2005 by IT ELAZOS S.L.
 */
class himmo extends org {
	/**
	 * Error messages after any method execution
	 * @var string
	 */
	var $txt_error;

	function himmo(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);
		$lovs->getLovs('_LST_TP_SDAYS',_IDIOMA);
		$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
		$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
		$lovs->getLovs('_LST_TP_STATE',_IDIOMA);
	}
	/**
	 * Get bookings immo(Property) details from the table prefix_bookings
	 * @access public
	 * @return boolean true on success or false
	 * @param integer immo id
	 * @param date booking from start date
	 * @param integer State of the reservation - 3 is confirmed - _LST_TP_RESERVSTATE
	 */
	function bookings($id,$dt_start,$tp_state){
		if ($this->query("select DATE_FORMAT(dt_start,'%Y%m%d') as dt_start, DATE_FORMAT(dt_end,'%Y%m%d') as dt_end from ".$this->prefix."_bookings where ".$this->prefix."_bookings.id_immo=$id AND ".$this->prefix."_bookings.dt_start>=$dt_start and tp_state = $tp_state;"))
		{return true;}
		else {$this->txt_error=""._ER_SELET_TBL." BOOK"; return false;}
	}
	/**
	 * Get season details for id_immo from the table prefix_himmos join with prefix_seasons
	 * @access public
	 * @return boolean true on success or false
	 * @param integer immo id
	 */
	function seasons($id){
		$join="INNER JOIN ".$this->prefix."_sdays ON ".$this->prefix."_seasons.id_seasons = ".$this->prefix."_sdays.id_seasons";
		$from=" (".$this->prefix."_immos INNER JOIN ".$this->prefix."_seasons ON ".$this->prefix."_immos.id_seasons = ".$this->prefix."_seasons.id_seasons)";

		if ($this->query("select ".$this->prefix."_sdays.precio,ELT(".$this->prefix."_seasons.tp_price,"._LST_TP_PRICE.") as tp_price,".$this->prefix."_sdays.tp_sdays, DATE_FORMAT(".$this->prefix."_sdays.dt_start,'%Y%m%d') as dt_start, DATE_FORMAT(".$this->prefix."_sdays.dt_end,'%Y%m%d') as dt_end from $from $join where ".$this->prefix."_immos.id_immo=$id order by ".$this->prefix."_sdays.dt_end ASC;"))
		{return true;}
		else {$this->txt_error=""._ER_SELET_TBL." SEA"; return false;}
	}
	/**
	 * List of seasons
	 * @access public
	 * @return boolean true on success or false
	 */
	function seasons_list($fields, $id_org,$from,$offset,$order_by){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";
		$where=" where ".$this->prefix."_seasons.id_org=$id_org ";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="name_seasons_search") {$key="name_seasons";$value="'%".addslashes($value)."%'";$operator=" like ";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_seasons.id_seasons, name_seasons, ELT(tp_price,"._LST_TP_PRICE.") as tp_price "
		."from "
		.$this->prefix."_seasons"
		." $where $order_by $limit;"))
		{if ($this->num_rows()==0) {$this->txt_error=""._NO_SEASONS."";return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." SEA"; return false;}
	}
	/**
	 * Deletes a season and its days.
	 *
	 * @param integer $id Season id
	 * @return boolean true on success or false
	 */
	function delete_season($id){

		if ($this->query("select id_immo from ".$this->prefix."_immos where id_seasons=$id;")){
			if ($this->num_rows()>0) {$this->txt_error=""._SEASON_IS_USED.""; return false;}
		} else { $this->txt_error=""._ER_SELET_TBL." SEA"; return false;}

		if ($this->query("delete from ".$this->prefix."_sdays where id_seasons=$id;")){
			if ($this->query("delete from ".$this->prefix."_seasons where id_seasons=$id;"))
			{ $this->txt_error=""._DELETED."";return true;}
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Deletes a season day
	 *
	 * @param unknown_type $id Day id
	 * @return boolean true on success or false
	 */
	function delete_sday($id){

		if ($this->query("delete from ".$this->prefix."_sdays where id_sdays=$id;"))
		{ $this->txt_error=""._DELETED."";return true;}
		else { $this->txt_error=""._ER_SELET_TBL." SDAY"; return false;}
	}

	/**
	 * Get days season list from the table prefix_sdays
	 * @access public
	 * @param integer season id
	 * @return boolean true on success or false
	 */
	function days_season($id){

		if ($this->query("select id_sdays, ELT(tp_sdays,"._LST_TP_SDAYS.") as tp_sdays, DATE_FORMAT(".$this->prefix."_sdays.dt_start,"._DATE_SQL.") as dt_start, DATE_FORMAT(".$this->prefix."_sdays.dt_end,"._DATE_SQL.") as dt_end, "
		.$this->prefix."_sdays.precio from ".$this->prefix."_sdays where ".$this->prefix."_sdays.id_seasons=$id;"))
		{return true;}
		else {$this->txt_error=""._ER_SELET_TBL." SEA"; return false;}
	}
	/**
	 * Get season details from the table prefix_seasons
	 * @author Josep Marxuach
	 * @access public
	 * @return boolean true on success or false
	 * @param integer season id
	 */
	function dtl_season($id){

		if ($this->query("select  ".$this->prefix."_seasons.*  from ".$this->prefix."_seasons where ".$this->prefix."_seasons.id_seasons=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." SEA"; return false;}
	}
	/**
	 * Get day details from the table prefix_sdays
	 * @author Josep Marxuach
	 * @access public
	 * @return boolean true on success or false
	 * @param integer sday id
	 */
	function dtl_sday($id){
		$join="INNER JOIN ".$this->prefix."_seasons ON ".$this->prefix."_seasons.id_seasons = ".$this->prefix."_sdays.id_seasons";
		if ($this->query("select  ".$this->prefix."_seasons.name_seasons ,".$this->prefix."_sdays.*, "
		."DATE_FORMAT(".$this->prefix."_sdays.dt_start,"._DATE_SQL.") as dt_start,  "
		."DATE_FORMAT(".$this->prefix."_sdays.dt_end,"._DATE_SQL.") as dt_end  "
		."from ".$this->prefix."_sdays $join where ".$this->prefix."_sdays.id_sdays=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." SDAY"; return false;}
	}
	/**
	 * Check if days intervals don't have interferences
	 * 
	 * @access public
	 * @return boolean true on success or false
	 * @param integer Season id
	 * @param Date Date start
	 * @param Date Date End
	 */
	function check_days($id_sdays,$dt_start,$dt_end,$id_seasons=NULL){

		if (isset($id_sdays))
		{$this->query("select ".$this->prefix."_sdays.id_seasons from ".$this->prefix."_sdays"
		." where ".$this->prefix."_sdays.id_sdays=$id_sdays;");
		$this->next_record();$id_seasons=$this->Record["id_seasons"];
		}

		if (isset($id_sdays)) $sql_sdays="AND ".$this->prefix."_sdays.id_sdays!=$id_sdays"; else $sql_sdays="";

		if ($this->query("select ".$this->prefix."_sdays.id_sdays from ".$this->prefix."_sdays"
		." where (( ".$this->prefix."_sdays.dt_start<=$dt_start AND $dt_start<=".$this->prefix."_sdays.dt_end ) "
		."OR ( ".$this->prefix."_sdays.dt_start<=$dt_end AND $dt_end<=".$this->prefix."_sdays.dt_end ))"
		." AND ".$this->prefix."_sdays.id_seasons=$id_seasons $sql_sdays;"))
		{
			if ($this->num_rows()>0) {$this->txt_error=""._ERROR_SDAYS_EXISTS.""; return false; } else return true;
		}
		else {$this->txt_error=""._ER_SELET_TBL." SEA"; return false;}
	}

	/**
	 * Get immo(Property) details from the table prefix_himmos
	 * @author Josep Marxuach
	 * @access public
	 * @return boolean true on success or false
	 * @param integer immo id
	 */
	function dtl_immo($id){

		$join="".$this->prefix."_himmos LEFT JOIN ".$this->prefix."_accounts ON ".$this->prefix."_himmos.id_account = ".$this->prefix."_accounts.id_account";

		if ($this->query("select  ".$this->prefix."_himmos.*, DATE_FORMAT(".$this->prefix."_himmos.dt_create,"._DATE_SQL.") as dt_create, DATE_FORMAT(".$this->prefix."_himmos.dt_valid,"._DATE_SQL.") as dt_valid, ".$this->prefix."_accounts.name_account from $join where ".$this->prefix."_himmos.id_immo=$id;"))
		{$this->next_record();return true;}
		else {$this->txt_error=""._ER_SELET_TBL." IMMO"; return false;}
	}


	/**
	 * list prefix_himmos(Properties) table rows based on organization, user position, view(My, Team, All), from row number
	 * and offset(rows per page), aditionaly you can link immos with accounts
	 * @author Josep Marxuach
	 * @access public
	 * @return boolean true on success or false
	 * @param string Keyword for search on ref_immo table field
	 * @param integer organization id
	 * @param integer position id
	 * @param string visibility ("My","Team","All")
	 * @param integer inital row number
	 * @param integer number of rows to return
	 * @param integer Account id
	 * @param boolean Type of columns when no position, True: simple, False: complet set of columns.
	 */
	function ver_himmos($fields=NULL, $id_org, $id_position, $view, $from, $offset, $order_by=false, $id_account=false, $tp_cols=false){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";

		if (isset($id_position)){
			$visible=$this->visibility("_himmos",$view,$id_position,$id_org);
			$columns="id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, txt_poblacion, txt_zona, username, precio";
		}
		else {
			$visible=$this->visibility("_himmos",$view,$id_position,$id_org,false);
			if ($tp_cols) $columns="id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_state,"._LST_TP_STATE.") as tp_state, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, txt_poblacion, txt_zona, precio";
			else $columns="id_immo, ref_immo, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ind_piscina, txt_poblacion, txt_zona, precio, num_dormitorios, num_wc, int_superficie, int_superficie_const, num_parking, img_front, dir_gal, set_properties, set_intro";
			$visible["clause_from"]=$visible["clause_from"]." LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_himmos.id_gal=".$this->prefix."_gallery.id_gal";
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

		if ($id_account) $where=$where." and ".$this->prefix."_himmos.id_account=$id_account";

		if ($this->query("select SQL_CALC_FOUND_ROWS $columns from ".$visible["clause_from"]." where $where $order_by $limit;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_IMMO.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." IMMO"; return false;}
	}

	/**
	 * Update fields of a table prefix_himmos
	 * @author Josep Marxuach
	 * @access public
	 * @param integer id
	 * @param array Assoc array[fieldname]=value
	 * @param string Table name
	 * @return boolean true on success or false
	 */
	function update_vals($id, $fields, $table){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
		switch($table)
		{
			case "seasons":
				$id_field="id_seasons";
				break;
			case "sdays":
				$id_field="id_sdays";
				if (array_key_exists("dt_start",$fields))
				if (!$this->check_days($id,$fields["dt_start"],$fields["dt_end"])) return false;
				break;
			default:
				return false;
				break;
		}
		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}
		//echo $st_field;die();
		if ($this->query("update ".$this->prefix."_$table set $st_field where ".$this->prefix."_$table.$id_field=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/**
	 * Delete immo(Property) from the table prefix_himmos and NO delete associated gallery* folder based on table prefix_gallery
	 * @author Josep Marxuach
	 * @access public
	 * @param integer immo id
	 * @return boolean true on success or false
	 */
	function delete_immo($id){
		$num_rows=0;
		if ($this->query("select id_gal from ".$this->prefix."_himmos where id_immo=$id;")){
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
			if ($this->query("delete from ".$this->prefix."_himmos where id_immo=$id;"))
			{ $this->txt_error=""._DELETED.""; return true;}
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		}
	}
	/**
	 * Add Seasons or sDays using fields array to the table name
	 * @author Josep Marxuach
	 * @access public
	 * @param integer organization id
	 * @param integer position id
	 * @param array Assoc array[fieldname]=value
	 * @return boolean true on success or false
	 */
	function add_vals($id_value, $fields, $table){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		switch($table)
		{
			case "seasons":
				$id_field="id_org";
				break;
			case "sdays":
				$id_field="id_seasons";
				if (array_key_exists("dt_start",$fields))
				if (!$this->check_days(NULL,$fields["dt_start"],$fields["dt_end"],$id_value)) return false;
				break;
			case "tp_price";
			break;
			default:
				return false;
				break;
		}

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}
		if ($this->query("insert into ".$this->prefix."_$table ($id_field, $st_field) values ($id_value, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Add immo(Property) using fields array to the table prefix_gallery
	 * @author Josep Marxuach
	 * @access public
	 * @param integer organization id
	 * @param integer position id
	 * @param array Assoc array[fieldname]=value
	 * @return boolean true on success or false
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
		if ($this->query("insert into ".$this->prefix."_himmos (id_org, id_position, $st_field) values ($id_org, $id_position, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Check immo(Property) fields before any add or update to the table prefix_himmos
	 * @author Josep Marxuach
	 * @access private
	 * @param array Assoc array[fieldname]=value
	 * @return boolean true on success or false
	 */
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "name_seasons_search":
					break;
				case "name_seasons":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'";
					else {$this->txt_error=""._ERROR_NAME_SEASONS."";return false;}
					break;
				case "dt_start":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_START;return false;}
					break;
				case "dt_end":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." "._DT_END;return false;}
					if ($fields["dt_start"]>=$fields[$key]) {$this->txt_error=""._ERROR_IN." "._DT_END;return false;}
					break;
				case "precio":
					if (strpos($value,".")) $fields[$key]=str_replace(".","",$value);
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "tp_sdays":
					if($fields[$key]=="") $fields[$key]="null";
					break;
					//default:
					//unset($fields[$key]);
					//break;
			}

		}
		return true;
	}
	/***************************************END CLASS*****************************************************/
}






?>
