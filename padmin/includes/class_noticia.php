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
if (preg_match("/class_noticia.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_org.php");
/**
 * News class.
 *
 */
class noticia extends org {
	var $txt_error;
	var $path_images="imgnews/";
	var $image_size_front=_IMG_NOT_FRONT; // pixel portada
	var $image_size=_IMG_NOT_INT; // pixel portada
	/**
	 * News Class constructor
	 *
	 * @return noticia
	 */
	function noticia(){
		if (!defined("_IMG_NOT_FRONT")) $this->image_size_front=150;
		if (!defined("_IMG_NOT_INT")) $this->image_size=300;
	}

	/**
	 * Gets list of news paged
	 *
	 * @param Array $fields Array of fields used to select news array[field_name]=value
	 * @param Integer $id_org Organization Id
	 * @param Integer $id_position User Position Id
	 * @param String $view My, Team or All view
	 * @param Integer $from
	 * @param Integer $offset
	 * @return True on success or false
	 */
	function ver_noticias($fields, $id_org, $id_position, $view, $from,$offset){
		//echo "$idioma, $id_org, $user_id, $perm, $flag_home, $keyword";

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		$visible=$this->visibility("_news",$view,$id_position,$id_org);
		if (isset($visible["where"])) $where=$visible["where"]." ";

		if (is_array($fields)) {
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="txt_title") {$operator=" like ";$value="'%".addslashes($value)."%'";}
				if ($key=="txt_resum") {$operator=" like ";$value="'%".addslashes($value)."%'";}
				if ($key=="txt_idioma") {$value="'$value'";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		//***********************
		if ($id_position!=null) $select="SQL_CALC_FOUND_ROWS id_news, DATE_FORMAT(dt_news,"._DATE_SQL.") fecha_ft, ELT(flag_home,"._LST_NEWS_POS.") as new_pos ,txt_title, ".$this->prefix."_auth_user.username";
		else  $select="SQL_CALC_FOUND_ROWS id_news, DATE_FORMAT(dt_news,"._DATE_SQL.") fecha_ft, txt_title, txt_resum,".$this->prefix."_auth_user.username";

		//echo $where;
		if ($this->query("SELECT ".$select." from ".$visible["clause_from"]." where $where order by dt_news DESC $limit;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_NOTS.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." NEWS"; return false;}
	}
	/**
	 * Gets news to show at home page
	 *
	 * @param String $idioma Language Code en, es, de...
	 * @param Integer $id_org Organization Id
	 * @param String $flag_home Indicates if new must be shown at home page, 1 home, 2 lateral, etc.. 
	 * @param unknown_type $limit
	 * @return True on success or false
	 */
	function noticias_home($idioma, $id_org,$flag_home=1,$limit=NULL){

		if (isset($limit)) $limit="limit 0, $limit";else $limit="";

		$where=" txt_idioma='$idioma' and flag_home='$flag_home' and id_org=$id_org";

		if ($this->query("select id_news, DATE_FORMAT(dt_news,'%d-%m-%Y') fecha_ft, txt_title, txt_resum, txt_content, cod_posimg, txt_imatge, txt_imatge1, txt_imatge2, txt_imatge3 from ".$this->prefix."_news where $where order by dt_news DESC $limit;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_NOTS.""; return false;} else $this->txt_error="";
		return true;
		}
		else { $this->txt_error=""._ER_SELET_TBL." NEWS"; return false;}

	}
	/**
	 * Get new details
	 *
	 * @param Integer $id New Id
	 * @return True on success or false
	 */
	function dtl_noticia($id){
		if ($this->query("select id_news, DATE_FORMAT(dt_news,'%d-%m-%Y | %T') fecha_ft, txt_title, txt_resum, txt_content, flag_home, txt_idioma, txt_imatge, cod_posimg, txt_imatge1, txt_imatge2, txt_imatge3 from ".$this->prefix."_news where id_news=$id;"))
		return true;
		else {$this->txt_error=""._ERROR_NOT.""; return false;}
	}
	/**
	 * Updates a new.
	 *
	 * @param Integer $id New Id
	 * @param String $titol New Title
	 * @param String $resum New Breifing
	 * @param String $texto New Content
	 * @param Integer $home Position flag, home page, lateral or none
	 * @param String(2) $idioma Language Code en, es, de...
	 * @return True on success or false
	 */
	function update_noticia($id, $titol, $resum, $texto, $home, $idioma){
		if (!isset($titol) || $titol=="") { $this->txt_error=""._IN_NOT_TITLE.""; return false;}
		if (!isset($resum) || $resum=="") { $this->txt_error=""._IN_NOT_RESUM.""; return false;}
		$titol = preg_replace('/&(?!amp;)/','&amp;',$titol);
		$resum = preg_replace('/&(?!amp;)/','&amp;',$resum);	
		
		if ($this->query("update ".$this->prefix."_news set txt_title='$titol', txt_resum='$resum', txt_content='$texto', flag_home='$home', txt_idioma='$idioma' where id_news=$id;"))
		{ $this->txt_error=""._UPDATED.""; return true;}
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Adds a new
	 *
	 * @param String $titol Title of the new
	 * @param String $resum Briefing of the new
	 * @param String $texto Content of the new
	 * @param Integer $home New position within html page (Front, left block, none)
	 * @param String(2) $idioma Language (en, es, de,...)
	 * @param Integer $id_org Organization Id
	 * @param Integer $id_position User Position Id
	 * @return True on success or false
	 */
	function insert_noticia($titol, $resum, $texto, $home, $idioma, $id_org, $id_position){
		if (!isset($titol) || $titol=="") { $this->txt_error=""._IN_NOT_TITLE.""; return false;}
		if (!isset($resum) || $resum=="") { $this->txt_error=""._IN_NOT_RESUM.""; return false;}
		$fecha= gmdate('Y-m-d H:i:s', time() + (3600)); // Hora GMT
        $titol = preg_replace('/&(?!amp;)/','&amp;',$titol);
		$resum = preg_replace('/&(?!amp;)/','&amp;',$resum);	
		if ($this->query("insert into ".$this->prefix."_news (dt_news, txt_title, txt_resum, txt_content, flag_home, txt_idioma, id_org, id_position) values ( '$fecha', '$titol', '$resum', '$texto', '$home', '$idioma', $id_org, $id_position)"))
		{ $this->txt_error=""._INSERTED.""; return true;}
		else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}

	/**
	 * Deletes a New
	 *
	 * @param Integer $id New Id
	 * @return True on success or false
	 */
	function delete_noticia($id){
		if ($this->query("delete from ".$this->prefix."_news where id_news=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}
	/**
	 * Updates Images news
	 *
	 * @param Integer $id New Id
	 * @param String $imatge File Name image 0
	 * @param Integer $posimg Not used
	 * @param String $imatge1 File Name image 1
	 * @param String $imatge2 File Name image 2
	 * @param String $imatge3 File Name image 3
	 * @return True on success or false
	 */
	function update_imatges($id, $imatge, $posimg, $imatge1, $imatge2, $imatge3){
		//echo "$id, $titol, $resum, $texto, $home, $idioma";
		//echo "<br/>update ".$this->prefix."_news set txt_title='$titol', txt_resum='$resum', txt_content='$texto', flag_home='$home', txt_idioma='$idioma' where id_news=$id";
		$set_sentence="";
		if ($imatge!=NULL) $imatge=" txt_imatge='$imatge' ";
		if ($imatge1!=NULL) $imatge1=" txt_imatge1='$imatge1' ";
		if ($imatge2!=NULL) $imatge2=" txt_imatge2='$imatge2' ";
		if ($imatge3!=NULL) $imatge3=" txt_imatge3='$imatge3' ";

		if ($imatge) $set_sentence=$imatge;
		if ($imatge1) {if ($set_sentence=="") $set_sentence=$imatge1; else $set_sentence=$set_sentence.",".$imatge1;}
		if ($imatge2) {if ($set_sentence=="") $set_sentence=$imatge2; else $set_sentence=$set_sentence.",".$imatge2;}
		if ($imatge3) {if ($set_sentence=="") $set_sentence=$imatge3; else $set_sentence=$set_sentence.",".$imatge3;}
		if ($set_sentence!="") {
			if ($this->query("update ".$this->prefix."_news set $set_sentence where id_news=$id;"))
			{ $this->txt_error=""._UPDATED.""; return true;}
			else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
		} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Deletes on new image
	 *
	 * @param Integer $id New Id
	 * @param Integer $num_imatge Image number 1, 2, 3 or 4
	 * @return True on success or false
	 */
	function borrar_imagen($id, $num_imatge){

		if ($this->query("select txt_imatge, txt_imatge1, txt_imatge2, txt_imatge3 from ".$this->prefix."_news where id_news=$id;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_NOTS.""; return false;}
		$this->next_record(); list($imatge1,$imatge2,$imatge3,$imatge4)=($this->Record);
		} else { $this->txt_error=""._ER_SELET_TBL." NEWS"; return false;}

		switch ($num_imatge) {
			case 1: $sentence=" txt_imatge=Null ";
			if (isset($imatge1) && file_exists($this->path_images.$imatge1)) unlink($this->path_images.$imatge1);
			break;
			case 2: $sentence=" txt_imatge1=Null ";
			if (isset($imatge2) && file_exists($this->path_images.$imatge2)) unlink($this->path_images.$imatge2);
			break;
			case 3: $sentence=" txt_imatge2=Null ";
			if (isset($imatge3) && file_exists($this->path_images.$imatge3)) unlink($this->path_images.$imatge3);
			break;
			case 4: $sentence=" txt_imatge3=Null ";
			if (isset($imatge4) && file_exists($this->path_images.$imatge4)) unlink($this->path_images.$imatge4);
			break;
			default: {$this->txt_error="Error al eliminar imagen"; return false;}
		}

		if ($this->query("update ".$this->prefix."_news set $sentence where id_news=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	//*************************** END CLASS ***********************************
}
?>
