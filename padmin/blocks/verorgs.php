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
*Lista de lista de organizaciones.
*Visualiza un arbol hecho en Javascript a la izquierda y los datos de la organizaci�n seleccionada.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verorgs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_org.php");

global $sub_id;
global $dlt;
global $list_del;
global $from;

$id_org=$this->auth['id_org'];

$ct=new org;

if (!isset($from)) $from=0;

// Deletes Organization
if (isset($dlt) && $dlt!="") {if (!$ct->delete_org($dlt)) $sub_id=$dlt; $this->add_msg($ct->txt_error);}

// Deletes selected positions
if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$ct->delete_position($dlte);$this->add_msg($ct->txt_error);}
}


$ct->org_array($id_org, $array_orgs);
$this->add_msg($ct->txt_error);
$this->create_tree($id_org, $array_orgs, ""._ORG."", 0,"pg=verorgs&sub_id=");

$this->html_out .= $this->pgtitle(""._ORG_LST."" ,false,null);

$this->html_out .= "<table class=\"catalog\"><tr>";
$this->html_out .= "<td class=\"mncat\">";
//$this->html_out .= "<p class=\"titcentral\">"._DEPTS."</p>";
$this->html_out .= "<script>initializeDocument()</script>";
$this->html_out .= "</td>";
$this->html_out .= "<td class=\"verprd\">";

if (!isset($sub_id)|| $sub_id=="") $sub_id=$id_org;

if (isset($sub_id)) {
    list($name, $parent_id)=$ct->org_name_idparent($sub_id);
    $this->add_msg($ct->txt_error);
    
    $link_add[0]["href"]="pg=edtorg&id_dept=$sub_id";
    $link_add[0]["txt"]=""._EDIT."";
    $link_add[1]["href"]="pg=edtorg&sub_id=$sub_id";
    $link_add[1]["txt"]=""._ADD_DEPT."";
    if ($ct->employees($id_org,0)) {
        $link_add[2]["href"]="pg=edtposition&sub_id=$sub_id";
        $link_add[2]["txt"]=""._ADD_POSITION."";
        }
    if ($parent_id) {
      $link_add[3]["href"]="pg=verorgs&dlt=$sub_id&sub_id=$parent_id";
      $link_add[3]["txt"]=""._DELETE."";
      $link_add[3]["confirm"]=""._DELETE." ? $name";
      }

    $this->html_out .= $this->pgtitle($name ,false,$link_add);
    unset($link_add);

    if ($ct->org_emp($sub_id,$from,20)) $this->print_list($ct->select_array(), $from,20,"","pg=edtposition&sub_id=$sub_id&id_pos=","dlt_emp=","sub_id=$sub_id",$ct->found_rows());
    else $this->add_msg($ct->txt_error);
    }
$this->html_out .= "</td>";
$this->html_out .= "</tr>";
$this->html_out .= "</table>";
?>
