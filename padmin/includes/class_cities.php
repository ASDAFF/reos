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
if (preg_match("/class_cities.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Database file
 * Utils extends DB_Sql class.
 **/
require_once(_DirINCLUDES."class_mysql.php");
/**
 * Creates, updates, deletes and make active countries, states, cities
 *
 */
class cities extends DB_Sql{
	/**
	 * Error message after any class method execution
	 *
	 * @var String
	 */
	var $txt_error;
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function CountryList(){

		if ($this->query("select SQL_CALC_FOUND_ROWS id_country, country_name name from ".$this->prefix."_country order by country_name asc")){
			if ($this->num_rows()==0) return false; else return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." COUNTRY"; return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_country
	 * @return unknown
	 */
	function ComdadList($id_country){

		if ($this->query("select SQL_CALC_FOUND_ROWS id_comunidad, comdad_name name from ".$this->prefix."_comunidad where id_country = $id_country")){
			if ($this->num_rows()==0) return false; else return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." COMDAD"; return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_comunidad
	 * @return unknown
	 */
	function ProvList($id_comunidad){

		if ($this->query("select SQL_CALC_FOUND_ROWS id_prov, prov_name name from ".$this->prefix."_provincia where id_comunidad = $id_comunidad")){
			if ($this->num_rows()==0) return false; else return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." PROV"; return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_prov
	 * @return unknown
	 */
	function CityList($id_prov){

		if ($this->query("select SQL_CALC_FOUND_ROWS id_poblacion, name_pob name from ".$this->prefix."_poblacion where id_prov = $id_prov")){
			if ($this->num_rows()==0) return false; else return true;
		} else {
			$this->txt_error=""._ER_SELET_TBL." POB"; return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_prov
	 * @param unknown_type $active
	 * @return unknown
	 */
	function ActiveAllProvCities($id_org, $id_prov, $active){

		if ($active==1){
			if ($this->query("insert into ".$this->prefix."_pob_org (id_org, id_poblacion) select $id_org as id_org, id_poblacion from ".$this->prefix."_poblacion where id_prov = $id_prov")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB"; return false;
			}
		} else {
			if ($this->query("delete from ".$this->prefix."_pob_org where id_org = $id_org and id_poblacion in (select id_poblacion from ".$this->prefix."_poblacion where id_prov = $id_prov)")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB"; return false;
			}

		}

	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_org
	 * @param unknown_type $id_country
	 * @param unknown_type $active
	 * @return unknown
	 */
	function ActiveAllCountryCities($id_org, $id_country, $active){

		if ($active==1){
			if ($this->query("insert into ".$this->prefix."_pob_org (id_org, id_poblacion) "
							."select $id_org as id_org, pob.id_poblacion "
			                ."from ".$this->prefix."_poblacion pob, ".$this->prefix."_provincia prov, ".$this->prefix."_comunidad comdad "
							."where prov.id_prov = pob.id_prov and prov.id_comunidad= comdad.id_comunidad and comdad.id_country = $id_country")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB,PROV,COMDAD"; return false;
			}
		} else {
			if ($this->query("delete from ".$this->prefix."_pob_org where id_org = $id_org and id_poblacion in (select pob.id_poblacion "
							."from ".$this->prefix."_poblacion pob, ".$this->prefix."_provincia prov, ".$this->prefix."_comunidad comdad "
							."where prov.id_prov = pob.id_prov and prov.id_comunidad= comdad.id_comunidad and comdad.id_country = $id_country)")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB,PROV,COMDAD"; return false;
			}

		}

	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_org
	 * @param unknown_type $id_comunidad
	 * @param unknown_type $active
	 * @return unknown
	 */
	function ActiveAllComdadCities($id_org, $id_comunidad, $active){

		if ($active==1){
			if ($this->query("insert into ".$this->prefix."_pob_org (id_org, id_poblacion) "
							."select $id_org as id_org, pob.id_poblacion "
			                ."from ".$this->prefix."_poblacion pob, ".$this->prefix."_provincia prov "
							."where prov.id_prov = pob.id_prov and prov.id_comunidad= $id_comunidad")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB,PROV"; return false;
			}
		} else {
			if ($this->query("delete from ".$this->prefix."_pob_org where id_org = $id_org and id_poblacion in (select pob.id_poblacion "
							."from ".$this->prefix."_poblacion pob, ".$this->prefix."_provincia prov "
							."where prov.id_prov = pob.id_prov and prov.id_comunidad = $id_comunidad)")){
				return true;
			} else {
				$this->txt_error=""._ER_SELET_TBL." POB,PROV"; return false;
			}

		}

	}
	
	

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_country
	 * @return unknown
	 */
	function GetCountryName($id_country){
		if ($this->query("select country_name from ".$this->prefix."_country where id_country=$id_country;"))
		{ $this->next_record();return $this->Record["country_name"];}
		else { $this->txt_error=""._ER_SELET_TBL." COUNTRY"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_poblacion
	 * @return unknown
	 */
	function GetCityName($id_poblacion){
		if ($this->query("select name_pob from ".$this->prefix."_poblacion where id_poblacion=$id_poblacion;"))
		{ $this->next_record();return $this->Record["name_pob"];}
		else { $this->txt_error=""._ER_SELET_TBL." POB"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_comunidad
	 * @return unknown
	 */
	function GetComdadName($id_comunidad){
		if ($this->query("select comdad_name from ".$this->prefix."_comunidad where id_comunidad=$id_comunidad;"))
		{ $this->next_record();return $this->Record["comdad_name"];}
		else { $this->txt_error=""._ER_SELET_TBL." COMDAD"; return false;}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_prov
	 * @return unknown
	 */
	function GetProvName($id_prov){
		if ($this->query("select prov_name from ".$this->prefix."_provincia where id_prov=$id_prov;"))
		{ $this->next_record();return $this->Record["prov_name"];}
		else { $this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $field
	 * @param unknown_type $value
	 * @return True
	 */
	function update($field, $value){

		if ($this->query("update ".$this->prefix."_$field set $st_field where user_id='$user_id';")){
			return true;
		} else {
			$this->txt_error=""._ERROR_UPDATE.""; return false;
		}
	}


}
?>