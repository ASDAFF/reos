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
 *Class Page Structure
 *@author Josep Marxuach  - Sept 2006
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_struct.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 *Class MySql Database
 **/
require_once(_DirINCLUDES."class_mysql.php");
/**
 *Manage the structure of the page.
 *Gets from the database screens and views
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by Josep Marxuach
 *@package Site
 */
class struct extends DB_Sql {
	/**
	 *Returns the screens for a perm/user profile.
	 *@param integer Perm or user profile id
	 *@author Josep Marxuach
	 *@copyright 2006 by Josep Marxuach
	 */
	function getscreens($perm) {
		if (isset($perm) && $perm!="") {
			$this->Query("SELECT * FROM ".$this->prefix."_perm_scrs, ".$this->prefix."_screens WHERE ".$this->prefix."_perm_scrs.id_screen=".$this->prefix."_screens.id_screen and ".$this->prefix."_perm_scrs.id_perm=$perm ORDER BY NUMORDER");
			$screens=$this->select_array();
			return $screens;
		} else return false;
	}
	/********************************************************************************************/
	/* Returns screen list                                                                      */
	/* @author Jordi Martí                                                                      */
	/********************************************************************************************/
	function screens_list() {
		if ($this->Query("SELECT * FROM ".$this->prefix."_screens ORDER BY NM_SCREEN")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}
	/********************************************************************************************/
	/* Returns the screen detail                                                                */
	/* @author Jordi Martí                                                                     */
	/********************************************************************************************/
	function scr_dtl($scr_id){

		if ($this->query("select * from ".$this->prefix."_screens where id_screen='$scr_id';"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." SCREENS"; return false;}
	}

	/********************************************************************************************/
	/* Updates screen                                                           */
	/* @author Jordi Martí
	 /********************************************************************************************/
	function update_scr($scr_id, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_screens set $st_field where id_screen='$scr_id';"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}

	}

	/********************************************************************************************/
	/* Adds screen                                                           */
	/* @author Jordi Mart�
	 /********************************************************************************************/
	function add_scr( $fields ){

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

		if ($this->query("insert into ".$this->prefix."_screens ( $st_field ) values ( $st_value );"))
		{$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}

	/********************************************************************************************/
	/* Deletes screen
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function del_scr( $id ) {
		if( $this->query( "select * from ".$this->prefix."_scr_views where id_screen='$id';" )){
			if ($this->num_rows()>0) {$this->txt_error=""._SCREEN_HAS_VIEW.""; return false;}
		} else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}

		if ($this->query("delete from ".$this->prefix."_screens where id_screen=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}

	/**
	 * Returns Views of a Screen.
	 *@author Jordi Mart�
	 *@param Integer Id of Screen
	 **/
	function scr_views_list( $scr ) {
		if ($this->Query("SELECT ".$this->prefix."_views.ID_VIEW, NM_VIEW, TXT_OBSRV, NUMORDER FROM ".$this->prefix."_scr_views, ".$this->prefix."_views WHERE ID_SCREEN = $scr AND ".$this->prefix."_scr_views.ID_VIEW=".$this->prefix."_views.ID_VIEW ORDER BY NUMORDER")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}

	/********************************************************************************************/
	/* Adds view x screen
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function add_screen_views( $scr_id, $fields ) {

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

		if ($this->query("insert into ".$this->prefix."_scr_views (id_screen, $st_field) values ($scr_id, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}

	/********************************************************************************************/
	/* Deletes view from screen
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function del_scr_view( $id_sc, $id_vw ) {
		if ($this->query("delete from ".$this->prefix."_scr_views where id_screen=$id_sc and id_view=$id_vw;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}

	/********************************************************************************************/
	/* Returns all views.
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function view_lst(){
		if ($this->query("SELECT ID_VIEW, TXT_OBSRV FROM ".$this->prefix."_views ORDER BY TXT_OBSRV"))
		{if ($this->num_rows()==0) return false;
		else {
			$prfs=$this->select_array();
			foreach($prfs as $value) $result[$value["ID_VIEW"]]=$value["TXT_OBSRV"];
			return $result;
		}
		}
		else { $this->txt_error=""._ER_SELET_TBL." SCRS"; return false;}
	}

	/**
	 *Returns the views for a perm/user profile or for a screen.
	 *if id_screen is not set it uses the perm.
	 *@param integer id screen
	 *@param integer Perm or user profile id
	 *@author Josep Marxuach
	 *@copyright 2006 by Josep Marxuach
	 */
	function getviews($id_screen, $perm) {
		if ($id_screen) {
			$this->Query("SELECT NM_VIEW, ".$this->prefix."_views.APP_FILE, ".$this->prefix."_views.PARAMS, NUMORDER, NM_SCREEN, ".$this->prefix."_screens.ID_SCREEN FROM ".$this->prefix."_scr_views, ".$this->prefix."_views, ".$this->prefix."_screens WHERE ".$this->prefix."_scr_views.ID_SCREEN=".$this->prefix."_screens.ID_SCREEN AND ".$this->prefix."_scr_views.ID_SCREEN=$id_screen AND ".$this->prefix."_scr_views.ID_VIEW=".$this->prefix."_views.ID_VIEW ORDER BY NUMORDER");
			$views=$this->select_array();
		} else {
			$this->Query("SELECT ID_SCREEN FROM ".$this->prefix."_perm_scrs WHERE ".$this->prefix."_perm_scrs.ID_PERM=$perm ORDER BY NUMORDER");
			if ($this->num_rows()==0) return false;
			$this->next_record();
			$id_screen=$this->Record[0];
			$this->Query("SELECT NM_VIEW, ".$this->prefix."_views.APP_FILE, ".$this->prefix."_views.PARAMS, NUMORDER, NM_SCREEN, ".$this->prefix."_screens.ID_SCREEN FROM ".$this->prefix."_scr_views, ".$this->prefix."_views, ".$this->prefix."_screens WHERE ".$this->prefix."_scr_views.ID_SCREEN=".$this->prefix."_screens.ID_SCREEN AND ".$this->prefix."_scr_views.ID_SCREEN=$id_screen AND ".$this->prefix."_scr_views.ID_VIEW=".$this->prefix."_views.ID_VIEW ORDER BY NUMORDER");
			$views=$this->select_array();
		}
		return $views;
	}
	/********************************************************************************************/
	/* Returns view list                                                                      */
	/* @author Jordi Mart�                                                                      */
	/********************************************************************************************/
	function verviews_list( $from, $offset ) {

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		if ($this->Query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->prefix."_views $limit;")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}
	/********************************************************************************************/
	/* Returns the view detail                                                                */
	/* @author Jordi Mart�
	 /********************************************************************************************/
	function view_dtl($view_id){

		if ($this->query("select * from ".$this->prefix."_views where id_view='$view_id';"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." VIEWS"; return false;}
	}

	/********************************************************************************************/
	/* Updates view                                                           */
	/* @author Jordi Mart�
	 /********************************************************************************************/
	function update_view($view_id,$fields) {

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_views set $st_field where id_view='$view_id';"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}

	/********************************************************************************************/
	/* Adds view                                                           */
	/* @author Jordi Mart�
	 /********************************************************************************************/
	function add_view($fields) {

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

		if ($this->query("insert into ".$this->prefix."_views ( $st_field ) values ( $st_value );"))
		{$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}

	/********************************************************************************************/
	/* Deletes view
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function del_view( $id ) {
		if ($this->query("delete from ".$this->prefix."_views where id_view=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}

	/**
	 *Returns perms/profiles.
	 *@author Josep Marxuach
	 *@copyright 2006 by Josep Marxuach
	 */
	function perm_list() {
		if ($this->Query("SELECT * FROM ".$this->prefix."_perms")
		&& $this->num_rows()>0)
		return true;
		else return false;
	}
	/**
	 *Returns Screens of a perm/profiles.
	 *@author Josep Marxuach
	 *@copyright 2006 by Josep Marxuach
	 *@param Integer Id of role
	 */
	function perm_scrs_list($perms) {
		if ($this->Query("SELECT ".$this->prefix."_screens.ID_SCREEN, NM_SCREEN, TXT_OBSRV, NUMORDER FROM ".$this->prefix."_perm_scrs, ".$this->prefix."_screens WHERE ID_PERM = $perms AND ".$this->prefix."_perm_scrs.ID_SCREEN=".$this->prefix."_screens.ID_SCREEN ORDER BY NUMORDER ASC")
		&& $this->num_rows()>0)
		return true;
		else { $this->txt_error=_NOTFOUND;return false;}
	}

	/********************************************************************************************/
	/* Deletes perm
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function delete_perm($id){
		if( $this->query( "select * from ".$this->prefix."_perm_scrs where id_perm='$id';" )){
			if ($this->num_rows()>0) {$this->txt_error=""._PERMS_HAS_SCR.""; return false;}
		} else { $this->txt_error=""._ER_SELET_TBL." PST"; return false;}

		if ($this->query("delete from ".$this->prefix."_perms where id_perm=$id;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}

	/**
	 *Returns all fields of a perm/profile.
	 *@author Josep Marxuach
	 *@param Integer Perm id
	 *@copyright 2006 by IT eLazos S.L.
	 *@return boolean
	 */
	function perm_dtl($perms){

		if ($this->query("select * from ".$this->prefix."_perms where id_perm=$perms;"))
		{if ($this->num_rows()==0) return false; else {$this->next_record();return true;}
		}
		else { $this->txt_error=""._ER_SELET_TBL." PERMS"; return false;}
	}

	/**
	 *Returns all screens.
	 *@author Josep Marxuach
	 *@copyright 2006 by IT eLazos S.L.
	 *@return boolean
	 */
	function screen_lst(){
		if ($this->query("SELECT ID_SCREEN, TXT_OBSRV FROM ".$this->prefix."_screens ORDER BY TXT_OBSRV"))
		{if ($this->num_rows()==0) return false;
		else {
			$prfs=$this->select_array();
			foreach($prfs as $value) $result[$value["ID_SCREEN"]]=$value["TXT_OBSRV"];
			return $result;
		}
		}
		else { $this->txt_error=""._ER_SELET_TBL." SCRS"; return false;}
	}
	/**
	 *Adds a perm using table field array $field to the table prefix_perms
	 *@author Josep Marxuach
	 *@access public
	 *@param array Assoc array[fieldname]=value
	 *@return boolean
	 */
	function add_perm($fields){

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

		if ($this->query("insert into ".$this->prefix."_perms ($st_field) values ($st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/********************************************************************************************/
	/* Updates perm                                                           */
	/* @author Jordi Mart�
	 /********************************************************************************************/
	function update_perm($fid,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_perms set $st_field where id_perm='$fid';"))
		{return true;} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/********************************************************************************************/
	/* Adds screen x perm
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function add_perm_scrs( $perms, $fields ) {

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

		if ($this->query("insert into ".$this->prefix."_perm_scrs (id_perm, $st_field) values ($perms, $st_value);"))
		{$this->txt_error=""._INSERTED.""; return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}

	/********************************************************************************************/
	/* Adds screen x perm
	 /* @author Jordi Mart�
	 /********************************************************************************************/
	function del_perm_scr( $id_pe, $id_sc ) {
		if ($this->query("delete from ".$this->prefix."_perm_scrs where id_perm=$id_pe and id_screen=$id_sc;"))
		{ $this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROD_DELETE.""; return false;}
	}

	/**
	 *Checks perm fields before any add or update to the table prefix_perms
	 *@author Josep Marxuach
	 *@copyright 2006 by IT eLazos S.L.
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
				case "nm_profile":
					if (strlen($value)<5) { $this->txt_error=""._ERROR_NM_PROFILE.""; return false;}
					$fields[$key]="'$value'";
					break;
				case "txt_obsrv":
					if ($value=="") {$this->txt_error=""._ERROR_OBSRV."";return false;}
					else $fields[$key]="'$value'";
					break;
				case "nm_screen":
					if ($value=="") {$this->txt_error=""._ERROR_NM."";return false;}
					else $fields[$key]="'$value'";
					break;
				case "app_file":
					if ($value=="") {$this->txt_error=""._ERROR_APP."";return false;}
					else $fields[$key]="'$value'";
					break;
				case "nm_view":
					if ($value=="") { $this->txt_error=""._ERROR_NM.""; return false;}
					$fields[$key]="'$value'";
					break;
				case "params":
					if( strlen($value)==0 ) $value = 'NULL';
					$fields[$key]="'$value'";
					break;
				case "id_screen":
					break;
				case "numorder":
					if( strlen($value)==0 ) $value = 0;
					$fields[$key]="$value";
					break;
				case "id_view":
					break;
				default:
					unset($fields[$key]);
					break;
			}
		}
		return true;
	}
	//***************************************END CLASS************************************
}
?>
