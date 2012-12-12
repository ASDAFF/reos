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
*Lista de usuarios.
*No tiene formualrio de busqueda.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verusers.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_user.php");

global $list_del;
global $id;
global $from;


$id_org=$this->auth["id_org"];

$link_add[0]["href"]="pg=edtuser&view=Add";
$link_add[0]["txt"]=""._ADD_USER."";

$this->html_out .= $this->pgtitle(""._LSTUSER."",false, $link_add);

if (!isset($from)) $from=0;
$dbi= new user;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->delete_user($dlte,$id_org);$this->add_msg($dbi->txt_error);}
}

if ($dbi->user_list($id_org,$from,20)){
    $fields=$dbi->select_array();
    foreach ($fields as $row=>$value){
      $fields[$row]["name_org"]=$this->html_link("pg=verorgs&sub_id=".$value["id_org"], $value["name_org"]);
      unset ($fields[$row]["id_org"]);
    }

    $this->print_list($fields, $from,20,"","pg=edtuser&from=$from&user_id=","dlt=","",$dbi->found_rows());
    
    }
    else $this->add_msg($dbi->txt_error);

?>
