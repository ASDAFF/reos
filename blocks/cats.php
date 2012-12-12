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
 * Visualiza la categoria principal y va mostrando las categorias para cada enlace.
 * Cuando llega al final muestra los productos de la categoria que no tenga más subcategorias.
 */
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/products.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_catalog.php");

global $id_org_session;
global $ctg;

$cat = new catalog;
$cat->cod_lang = _IDIOMA;

// ***** Categories
if (!isset($ctg)) $ctg=null;
if ($cat->categories( $ctg, $id_org_session ))
$this->add_msg( $cat->txt_error );

$Catlist =	$cat->select_array();
$link="";
if (is_array($Catlist)){
	$result = array(0);
	if ($ctg!=null) {
		$result[][0]=$ctg;$result[sizeof($result) - 1][1]=$cat->name_category($ctg);
		$cat->ParentCat($ctg,$result);

	}
	$link = "<links>\n";
	asort($result);
	foreach($result as $item){
		if ($item[1]=="") $val=_HOMEPG; else $val=$item[1];
			$link.="<item href=\"".$this->getPermalink($val)."".$this->url_encrypt("pg=cats&ctg=".$item[0])."\">".$val."</item>\n";
		}
	$link.="</links>\n";
	
	$this->html_out .= "<categs title=\""._CATALOG."\" >\n";
	$this->html_out .=	$link;
	$this->html_out .= $this->xml_list($Catlist, 0, 1000, "pg=cats","pg=cats&ctg=", null, null,count($Catlist));
	$this->html_out .= "</categs>\n";
} else {
	$cat->products_cat($ctg,0,1000);
	$Catlist =	$cat->select_array();
	$this->html_out .= "<prods title=\""._CATALOG."\" >\n";
	$this->html_out .= $this->xml_list($Catlist, 0, 1000, "pg=cats","pg=prod&id_product=", null, null,count($Catlist));
	$this->html_out .= "</prods>\n";
}
?>
