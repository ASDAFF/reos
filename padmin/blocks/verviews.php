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
*Lista de screens.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verviews.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_struct.php");

global $from;
global $list_del;

$dbi= new struct;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->del_view($dlte);$this->add_msg($dbi->txt_error);}
}

$link_add[0]["href"]="pg=edtview&view=Add";
$link_add[0]["txt"]=""._ADD_VIEW."";

$this->html_out .= $this->pgtitle(_VERVIEWS,false, $link_add);

if (!isset($from)) $from=0;

$dbi->verviews_list( $from, 20 );
$fields = $dbi->select_array();
if( is_array( $fields))
    $this->print_list( $fields, $from, 20, "", "pg=edtview&view_id=", "dlt=", "", $dbi->found_rows());
else $this->add_msg($dbi->txt_error);

?>
