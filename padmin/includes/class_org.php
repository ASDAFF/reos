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
 * Class Organizations definition file
 * @author IT eLazos SL  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_org.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Utils definition file
 **/
require_once(_DirINCLUDES."class_utils.php");
/**
 * Handles Organizations using table prefix_org. Defines Organization structure and visibilities between team members.
 * @author Josep Marxuach
 * @version 1.0
 * @copyright 2004 by IT eLazos SL
 * @package BusObj
 */
class org extends utils {
	/**
	 * Message after execution of any method
	 * @var string
	 */
	var $txt_error;
	/**
	 * Organization Id
	 * @var Integer
	 */
	var $id_org;
	/**
	 * User Id
	 * @var string
	 */
	var $uid;
	/**
	 * Permissions - Not Used
	 * @var string
	 */
	var $perm;
	/**
	 * Position id
	 * @var Integer
	 */
	var $id_position;

	function org(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_POSITIONS',_IDIOMA);
	}

	/**
	 *Función Team es Recursiva: devuelve un string separado por comas con los id_position de los miembros de id_org
	 *Se utiliza el string en una sentencia SQL
	 *utiliza : org_hijas, org_positions
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Organization Id
	 *@return string Id positions separated by comma ex: 1, 3, 4
	 **/
	function team($id_org){

		//$seleccio="SELECT ".$this->prefix."_position_1.id_position FROM (".$this->prefix."_position INNER JOIN ".$this->prefix."_org ON ".$this->prefix."_position.id_org = ".$this->prefix."_org.parent_id_org) INNER JOIN ".$this->prefix."_position AS ".$this->prefix."_position_1 ON ".$this->prefix."_org.id_org = ".$this->prefix."_position_1.id_org WHERE (((".$this->prefix."_position.id_position)=2))";

		$this->org_hijas($id_org);
		$dept=Null;
		$z=0;
		while ($this->next_record()){
			list($id_org, )=($this->Record);
			$dept[$z]=$id_org;
			$z++;
		}

		$z=0;
		$maxim=count($dept);
		$team_members="";
		while ($z<$maxim){
			$this->org_positions($dept[$z]);
			while ($this->next_record()){
				list($id_position)=($this->Record);
				if ($team_members=="") $team_members="$id_position"; else $team_members="$team_members ,$id_position";
			}

			if ($team_members=="") $team_members=$this->team($dept[$z]);
			else if (""!=($members=$this->team($dept[$z]))) $team_members="$team_members ,".$members;
			$z++;
		}
		return $team_members;
	}
	/**
	 *Devuelve el departamento/organizaci�n a la que pertenece la id_position
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Position Id
	 *@return integer Organization Id : Devuelve el departamento/organizaci�n a la que pertenece la id_position
	 *sino pertenece a ninguna org/dept devuelve false.
	 **/
	function dept($id_position){
		if ($this->query("select id_org from ".$this->prefix."_position where id_position=$id_position;"))
		{ $this->next_record(); list($id_org)=($this->Record); return $id_org;}
		else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}
	}
	/**
	 *Devuelve la lista de usuarios/employees de una id_org seg�n el parametro Assign.
	 *El resultado queda como almacenado en la classe. Podemos acceder con el metodo select_array de la classe DB_Sql
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Organization Id
	 *@param integer Assigned 0 = los empleados sin position, 1 los empleados con position, sino todos
	 *@return boolean True si devuelve algo, false no devuelve nada o error
	 **/
	function employees($id_org,$assigned){

		switch($assigned){
			case 0:
				$fields=$this->prefix."_auth_user.user_id, name_user";
				$clause_from=" LEFT JOIN ".$this->prefix."_position ON ".$this->prefix."_auth_user.user_id=".$this->prefix."_position.user_id ";
				$where=" and ".$this->prefix."_position.user_id IS NULL";
				break;
			case 1:
				$fields="id_position, name_user";
				$clause_from=" LEFT JOIN ".$this->prefix."_position ON ".$this->prefix."_auth_user.user_id=".$this->prefix."_position.user_id ";
				$where=" and ".$this->prefix."_position.user_id IS NOT NULL";
				break;
			default:
				$fields=$this->prefix."_auth_user.user_id, name_user";
				$clause_from="";$where="";
				break;
		}

		if ($this->query("select $fields from ".$this->prefix."_auth_user $clause_from where ".$this->prefix."_auth_user.id_org=$id_org and user_type=1 $where;"))
		{ if ($this->num_rows()==0) return false; else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." EMPL"; return false;}
	}
	/**
	 *Devuelve name_org i id_parent padre de una Orgnizaci�n/departament id
	 *Devuelve el resultado como un array["field_name"]
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Organization Id hija
	 *@return array Array["field_name"] o false no devuelve nada o error
	 **/
	function org_name_idparent($id){
		if ($this->query("select name_org, parent_id_org from ".$this->prefix."_org where id_org=$id;"))
		{ $this->next_record();return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 *Devuelve la informaci�n de una Orgnizaci�n/departament id
	 *Devueldve el resultado como un array["field_name"]
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Organization Id
	 *@return array Array["field_name"] o false no devuelve nada o error
	 **/
	function organization($id){
		if ($this->query("select * from ".$this->prefix."_org where id_org=$id;"))
		{ $this->next_record();return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 *Devuelve solo el nombre de la organizaci�n id
	 *Devueldve el resultado como string
	 *@author Josep Marxuach
	 *@access public
	 *@param integer Organization Id
	 *@return String Organization Name or false no devuelve nada o error
	 **/
	function name_org($id){
		if ($this->query("select name_org from ".$this->prefix."_org where id_org=$id;"))
		{ $this->next_record();list($name)=$this->Record;return $name;}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 * Inserta una organizaci�n departamento con name que pertence al departamento parent_org
	 * Si parent_org es Null se inserta en la raiz del arbol y es una organizaci�n
	 **/
	function add_org($id_org, $parent_org, $fields){

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

		if (!is_numeric($parent_org)) $parent_org="NULL";

		if ($this->query("insert into ".$this->prefix."_org (parent_id_org, root_id_org, $st_field) VALUES($parent_org, $id_org, $st_value);"))
		{return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}

	}
	/**
	 *Inserta una position del user_id, $id_org, y position description
	 **/
	function add_position($user_id, $id_org, $position){
		if ($user_id=="") {$this->txt_error=""._ERROR_INSERT.""; return false;}

		if ($this->query("insert into ".$this->prefix."_position (id_org, user_id, name_position) VALUES($id_org, '$user_id',  '$position');"))
		return true; else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 *Busca organizaciones mediante el Keyword
	 *Keyword es el contenido de una sentencia SQL tipo nam_org='%empres%'
	 **/
	function ver_orgs($keyword){
		$where=Null;
		if (isset($keyword) && $keyword!="" && $keyword!=false) $where=" where $keyword";

		if ($this->query("select id_org, name_org, txt_email1, txt_web, txt_poblacion from ".$this->prefix."_org $where;"))
		{ if ($this->num_rows()==0) {$this->txt_error=""._NO_ORGS.""; return false;}
		else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 *Devuelve name, id_org hijas de la organizaci�n sub_id
	 **/
	function org_hijas($sub_id = NULL){
		if ($sub_id)
		{$where="where parent_id_org=".$sub_id."";
		if ($this->query("select id_org, name_org from ".$this->prefix."_org ".$where.";"))
		{ if ($this->num_rows()==0) { return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
		}
	}

	/**
	 *Actualiza el campo field con el valor value de la organizaci�n id
	 **/
	function update_org($id, $fields){
		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_org set $st_field where id_org=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/**
	 *Elimina la organizaci�n id
	 **/
	function delete_org($id){

		if ($this->org_hijas($id) || $this->org_positions($id)){$this->txt_error=""._ORG_HAS_SUBORG.""; return false;}
		else {
			if ($this->have_assign($id,array ("immos", "accounts", "pob_org", "auth_user", "gallery", "news"),"id_org")) { $this->txt_error=""._PST_ELEMTS."";return false;}
			if ($this->query("delete from ".$this->prefix."_org where id_org=$id;"))
			{ $this->txt_error=""._DELETED.""; return true;}
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		}

	}
	/**
	 *Crea una array del arbol de categorias categorias
	 * Se pasa por parametro el array, el id de la categoria raiz, i  la org
	 * Si queremos todo el arbol id=NULL
	 * Es recursiva
	 **/
	function org_array($id, &$result) {
		$consulta=null;

		$this->org_hijas($id);
		$z=0;
		while ($this->next_record()){
			list($id_org, $name)=($this->Record);
			$consulta[$z][0]=$id_org;
			$consulta[$z][1]=$name;
			$z++;
		}

		$z=0;
		$maxim=count($consulta);
		if ($id==NULL) $id=0;

		while ($z<$maxim){
			$result[$id][$z]["id"]=$consulta[$z][0];
			$id_org=$consulta[$z][0];
			$result[$id][$z]["value"]=$consulta[$z][1];
			$this->org_array($id_org,$result);
			$z++;
		}
	}
	/**
	 *Devuelve la lista de usuarios/empleados del departamento
	 * id = Id_org
	 **/
	function org_emp($id,$from=Null, $offset=Null){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($id)
		{$where="where ".$this->prefix."_position.id_org=$id AND ".$this->prefix."_position.user_id=".$this->prefix."_auth_user.user_id ";
		if ($this->query("select SQL_CALC_FOUND_ROWS id_position, ".$this->prefix."_auth_user.name_user, ELT(name_position,"._LST_POSITIONS.") as name_position from ".$this->prefix."_position, ".$this->prefix."_auth_user ".$where." $limit;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_EMPL.""; return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." EMPL"; return false;}
		}
	}
	/**
	 * Devuelve la lista de positions del departamento
	 * Id_org = Departamento/Organization
	 **/
	function org_positions($id_org){

		if ($this->query("select id_position from ".$this->prefix."_position where id_org=$id_org;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_PSTS.""; return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}

	}
	/**
	 * Devuelve una array con las partes necesarias para crea una setencia sql
	 * que permita saber las positions que forma el equipo de una position
	 * Array indexes : clause_from->setencia join en el from
	 * where->sentencia where de la sentencia
	 **/
	function visibility($table,$view,$id_position,$id_org, $makejoin=True){

		if (!($view=="My" || $view=="All" || $view=="Team")) return false;

		if ($view=="My") {$where=" ".$this->prefix."$table.id_position=$id_position";$join="INNER JOIN";}
		if ($view=="All") { $where=" ".$this->prefix."$table.id_org=$id_org";$join="LEFT JOIN";}
		if ($view=="Team") { $dept=$this->dept($id_position);
		$members=$this->team($dept);
		if ($members!="") $where= " ".$this->prefix."$table.id_position in ($members)";
		else {$this->txt_error=""._NO_MEMBERS."";return false;}
		$join="INNER JOIN";
		}

		if ($makejoin)
		$clause_from="(".$this->prefix."$table $join ".$this->prefix."_position ON ".$this->prefix."$table.id_position = ".$this->prefix."_position.id_position) $join ".$this->prefix."_auth_user ON ".$this->prefix."_position.user_id = ".$this->prefix."_auth_user.user_id";
		else $clause_from="".$this->prefix."$table";

		$visible["clause_from"]=$clause_from;
		$visible["where"]=$where;

		return $visible;
	}
	/**
	 * funci�n : Devuelve  id_user, id_org, position  de la position id
	 **/
	function position($id){

		$clause_from="INNER JOIN ".$this->prefix."_auth_user ON ".$this->prefix."_position.user_id = ".$this->prefix."_auth_user.user_id";

		if ($this->query("select ".$this->prefix."_position.user_id, ".$this->prefix."_position.id_org, ".$this->prefix."_position.name_position, ".$this->prefix."_auth_user.name_user from ".$this->prefix."_position $clause_from where id_position=$id;"))
		{ $this->next_record();return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}
	}
	/**
	 * Actualiza el campo field con el valor value de la position id
	 **/
	function update_position($id, $id_org, $name_position){
		if ($this->query("update ".$this->prefix."_position set id_org=$id_org, name_position='$name_position' where id_position=$id"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Devuelve la lista de todos los departamentos de id_org
	 **/
	function all_orgs($id_org, $str_fields){
		$where="where root_id_org=$id_org";
		if ($this->query("select $str_fields from ".$this->prefix."_org ".$where.";"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_ORGS."";return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." ORG"; return false;}
	}
	/**
	 * Elimina la position id
	 **/
	function delete_position($id){
		//comprueba de que no tenga elementos asignados
		if ($this->have_assign($id,array ("accounts", "news", "immos"),"id_position")) { $this->txt_error=""._PST_ELEMTS."";return false;}
		// ejecuta el delete
		if ($this->query("delete from ".$this->prefix."_position where id_position=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Devuelve true or false seguin
	 **/
	function have_assign($id,$lst_tables,$PK){
		//comprueba de que no tenga elementos asignados

		while (list(,$table)=each ($lst_tables))
		{
			$table=$this->prefix."_$table";
			$this->query("select * from $table where $PK=$id limit 0,1;");
			if ($this->num_rows()>0) return true;

		}
		return false;
	}
	/**
	 *Lista de permisos/profile.
	 *@return Array Lista de C�digo, permiso/perfil
	 **/
	function prfs(){
		if ($this->query("SELECT ID_PERM, NM_PROFILE FROM ".$this->prefix."_perms"))
		{if ($this->num_rows()==0) return false;
		else {
			$prfs=$this->select_array();
			foreach($prfs as $value) $result[$value["ID_PERM"]]=$value["NM_PROFILE"];
			return $result;
		}
		}
		else { $this->txt_error=""._ER_SELET_TBL." PERMS"; return false;}
	}
	/**
	 * Hace las validaciones antes de hacer un update  or insert
	 **/
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "name_org":
					if (strlen($value)==0) {$this->txt_error=""._IN_NAME_ORG."";return false;}
					$fields[$key]="'".addslashes($value)."'";
					break;
				case "txt_poblacion":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'";
					else {$this->txt_error=""._IN_POB_ORG."";return false;}
					break;
				case "txt_address1":
				case "txt_cif":
				case "txt_zona":
				case "txt_provincia":
				case "txt_cp":
				case "txt_telf1":
				case "txt_telf2":
				case "txt_fax":
				case "txt_email1":
				case "txt_email2":
				case "txt_web":
					if($fields[$key]!="") $fields[$key]="'".addslashes($value)."'"; else $fields[$key]="NULL";
					break;
			}

		}
		return true;
	}
	/**
	 * Devuelve  la lista de provincias de la comunidad id
	 **/
	function provincias($active=false){

		$where = " where pro.id_comunidad = com.id_comunidad and  com.id_country = ctr.id_country";

		if ($active!=false) $where.=" and ind_active=$active";

		if ($this->query("select id_prov, concat(country_name,' ',comdad_name,' ',prov_name) as prov_name "
		."from ".$this->prefix."_provincia pro, ".$this->prefix."_comunidad com, ".$this->prefix."_country ctr $where order by prov_name ASC;"))
		{ return true;}
		else { $this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 * Devuelve un array como un arbol de provincias i poblaciones de la comdad id
	 **/
	function array_prov_pob($comdad, $active){
		if ($active!=false) $active=" ".$this->prefix."_poblacion.ind_active=$active"; else $active="";
		if ($comdad!=false) $comdad=" ".$this->prefix."_provincia.id_comunidad=$comdad"; else $comdad="";

		$sql="SELECT ".$this->prefix."_provincia.prov_name,".$this->prefix."_poblacion.name_pob"
		." FROM ".$this->prefix."_provincia"
		." LEFT JOIN ".$this->prefix."_poblacion ON ".$this->prefix."_provincia.id_prov = ".$this->prefix."_poblacion.id_prov";
			
		if ($comdad!="" && $active!="")   $sql.= " where $comdad and $active";
		if ($comdad!="" && $active=="")   $sql.= " where $comdad";
		if ($comdad=="" && $active!="")   $sql.= " where $active";

		$result=array();

		if ($this->query($sql))
		{

			while ($this->next_record())
			{
				list($name_prov, $name_pob)=($this->Record);
				//echo "$name_comdad - $id_prov - $prov_name - $name_pob<br />";
				$result[$name_prov][]=$name_pob;
			}
			return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}

	/**
	 * Devuelve un array como un arbol de poblaciones i zonas de la provincia id
	 **/
	function array_pob_zona($id_org,$first_option=false){

		$sql="SELECT ".$this->prefix."_poblacion.name_pob, ".$this->prefix."_zona.name_zona"
		." FROM (".$this->prefix."_pob_org LEFT JOIN ".$this->prefix."_poblacion ON ".$this->prefix."_pob_org.id_poblacion = ".$this->prefix."_poblacion.id_poblacion)"
		." LEFT JOIN ".$this->prefix."_zona ON ".$this->prefix."_poblacion.id_poblacion = ".$this->prefix."_zona.id_poblacion"
		." WHERE (((".$this->prefix."_pob_org.id_org)=$id_org));";

		$result=array();

		if ($this->query($sql))
		{
			if ($first_option) $result[""][]="";
			while ($this->next_record())
			{
				list($name_pob, $name_zona)=($this->Record);
				//echo "$name_comdad - $id_prov - $prov_name - $name_pob<br />";
				if ($first_option && !array_key_exists($name_pob,$result)) $result[$name_pob][]="";
				$result[$name_pob][]=$name_zona;
			}
			return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 * Devuelve un array como un arbol de provincias,poblaciones, zonas de la comunidad id
	 **/
	function array_prov_pob_zona($comdad, $active){
		if ($active!=false) $active=" and ".$this->prefix."_poblacion.ind_active=$active"; else $active="";

		$sql="SELECT ".$this->prefix."_provincia.prov_name, ".$this->prefix."_poblacion.name_pob, ".$this->prefix."_zona.name_zona"
		." FROM (".$this->prefix."_provincia LEFT JOIN ".$this->prefix."_poblacion ON ".$this->prefix."_provincia.id_prov = ".$this->prefix."_poblacion.id_prov)"
		." LEFT JOIN ".$this->prefix."_zona ON ".$this->prefix."_poblacion.id_poblacion = ".$this->prefix."_zona.id_poblacion"
		." where ".$this->prefix."_provincia.id_comunidad=$comdad $active";

		$result=array();

		if ($this->query($sql))
		{

			while ($this->next_record())
			{
				list($name_prov, $name_pob, $name_zona)=($this->Record);
				//echo "$name_comdad - $id_prov - $prov_name - $name_pob<br />";
				$result[$name_prov][$name_pob][]=$name_zona;
			}
			return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}

	/**
	 * Busca poblacion por nombre, devuelve false si no i el nombre si si
	 **/
	function busca_pob($str_pob){

		$sql="SELECT ".$this->prefix."_poblacion.id_poblacion, ".$this->prefix."_poblacion.name_pob "
		." FROM ".$this->prefix."_poblacion";

		$result="";

		if ($this->query($sql))
		{
			$coef=1000;
			while ($this->next_record())
			{
				list($id_pob, $name_pob)=($this->Record);
				$levens=levenshtein(strtolower($str_pob),strtolower ($name_pob));
				if($levens<$coef) {$coef=$levens;$result["name_pob"]=$name_pob;$result["id_pob"]=$id_pob;}
			}
			if ($coef>2) return false; else return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 * Inserta una position del user_id, $id_org, y position description
	 **/
	function add_zona($pob, $zona){
		if (!$array_prov=$this->busca_pob($pob)) return false;
		$id_pob=$array_prov["id_pob"];
		if ($this->query("insert into ".$this->prefix."_zona (id_poblacion, name_zona, ind_active) VALUES($id_pob, '$zona', 1);"))
		return true; else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Devuelve un array como un arbol de poblaciones i zonas de la provincia id
	 **/
	function ver_pobs($id_org, $id_prov, $keyword, $from, $offset){
		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		
		$where = "";
				
		if ($id_prov != null) $where = $this->prefix."_poblacion.id_prov=$id_prov";		
		
		if (isset($keyword) && $keyword!="" && $keyword!=false) 
		    $where.=" and pob.name_pob like '%$keyword%'";		
		
		$sql="SELECT SQL_CALC_FOUND_ROWS pob.id_poblacion, IF (org.id_org,'"._SELECTED."','"._NOSELECTED."') as state, pob.name_pob, pro.prov_name, com.comdad_name"
		." FROM ".$this->prefix."_poblacion pob left JOIN tb_pob_org org ON pob.id_poblacion = org.id_poblacion, ".$this->prefix."_provincia pro, ".$this->prefix."_comunidad com where pob.id_prov = pro.id_prov and pro.id_comunidad = com.id_comunidad $where $limit;";

		if ($this->query($sql))
		{ if ($this->num_rows()==0)
		{
			$this->txt_error=""._ERROR_NOPOB."";
			return false;
		}else return true;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	/**
	 * Activa o desactiva una poblaci�n por organizacion
	 **/
	function active_pob($id_org, $id_pob){
		if ($this->query("select * from ".$this->prefix."_pob_org where id_org=$id_org and id_poblacion=$id_pob;"))
		{
			if ($this->num_rows()==0) $todo="add";else $todo="delete";
		} else { $this->txt_error=""._ER_SELET_TBL." POB ORG"; return false;}
			
		if ($todo=="add")
		{
			if ($this->query("insert into ".$this->prefix."_pob_org (id_org, id_poblacion) VALUES($id_org, $id_pob);"))
			return true;
			else { $this->txt_error=""._ERROR_INSERT.""; return false;}
		} else
		{
			if ($this->query("delete from ".$this->prefix."_pob_org where id_org=$id_org and id_poblacion=$id_pob;"))
			return true;
			else { $this->txt_error=""._ERROR_DELETE.""; return false;}
		}
	}
/**
 * Returns all active cities.
 * 
 * @param Integer $id_org Organization id
 * @param Integer $id_prov County id
 * @param Boolean $field if true then return city name too
 * @return String set of active id of cities separated by comma.
 */
	function poblaciones($id_org, $id_prov, $field=true){

		$sql="SELECT ".$this->prefix."_poblacion.id_poblacion, ".$this->prefix."_poblacion.name_pob"
		." FROM ".$this->prefix."_poblacion LEFT JOIN ".$this->prefix."_pob_org ON ".$this->prefix."_poblacion.id_poblacion = ".$this->prefix."_pob_org.id_poblacion"
		." WHERE ".$this->prefix."_pob_org.id_org=$id_org AND ".$this->prefix."_poblacion.id_prov=$id_prov";

		if ($this->query($sql))
		{
			$result="";
			if ($field) $field="name_pob";else $field="id_poblacion";
			while ($this->next_record())
			if ($result=="") $result=$this->Record[$field];else $result=$result.", ".$this->Record[$field];

			return $result;
		} else {$this->txt_error=""._ER_SELET_TBL." PROV"; return false;}
	}
	//************************************************* FIN CLASE*****************************
}

?>
