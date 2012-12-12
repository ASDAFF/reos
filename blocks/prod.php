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
 * Detalle de un producto
 */
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/prod.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_catalog.php");
global $id_product;

$dbi = new catalog;
$dbi->cod_lang = _IDIOMA;

if (isset($id_product)) { 
	$fields=$dbi->product($id_product);
	$this->add_msg($dbi->txt_error);
}
$this->html_out .= "<prod>\n";
foreach($fields as $name=>$value) {
	if (!is_integer($name)) $this->html_out .= "<$name>$value</$name>\n";}
$this->html_out .= "</prod>\n";
?>