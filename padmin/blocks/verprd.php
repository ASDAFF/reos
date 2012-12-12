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
*Lista de productos con arbol de categorias.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verprd.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $ctg;
global $dlt;
global $dlt_ctg;
global $from;

$id_org=$this->auth["id_org"];
if (!isset($from)) $from=0;

require_once(_DirINCLUDES."class_catalog.php");

$ct=new catalog;
$ct->cod_lang=_IDIOMA;

$this->html_out .= $this->pgtitle(_PRODS,false,null);

//$this->html_out .= "<p class=\"titcentral\">"._PRODS."</p>";

if (isset($dlt)) {$ct->delete_product($dlt);$this->add_msg($ct->txt_error);}
if (isset($dlt_ctg)) {if (!$ct->delete_category($dlt_ctg)) $ctg=$dlt_ctg;$this->add_msg($ct->txt_error); }

// cuadro del arbre de categories

$ct->ctg_array(Null,$id_org, $ctg_array);
$this->add_msg($ct->txt_error);
$this->create_tree(0, $ctg_array, ""._CATALOG."", 0,"pg=verprd&ctg=");



$this->html_out .= "<table class=\"catalog\"><tr>";
$this->html_out .= "<td class=\"mncat\">";
$this->html_out .= "<p class=\"titcentral\">"._CTGS."</p>";
$this->html_out .= "<script>initializeDocument()</script>";
$this->html_out .= "</td>";
// FI del cuadro del arbre de categories

$this->html_out .= "<td class=\"verprd\">";

if (isset($ctg)&& $ctg!="") {
    list($name, $id_parent )=$ct->category($ctg);
    $this->add_msg($ct->txt_error);
    } else {$name=""._CATALOG."";$id_parent=NULL;$ctg=NULL;}

$this->html_out .= "<p><span class=\"titcentral\">$name</span>";
if ($ctg!=NULL) $this->html_out .= " - <a href=\"".LK_PAG."".$this->url_encrypt("pg=edtctg&id=$ctg")."\">"._EDIT."</a>";
$this->html_out .= " - <a href=\"".LK_PAG."".$this->url_encrypt("pg=edtctg&id_parent_category=$ctg")."\">"._ADD_CTG."</a>";

if ($ctg!=NULL) {
   $this->html_out .= " - <a href=\"".LK_PAG."".$this->url_encrypt("pg=edtprd&id_category=$ctg")."\">"._ADD_PROD."</a>"
                ." - <a href=\"".LK_PAG."".$this->url_encrypt("pg=verprd&ctg=$id_parent&dlt_ctg=$ctg")."\" onclick=\"return confirm('"._BORRAR." ? $name')\">"._BORRAR."</a></p>";

   if ($ct->products_cat($ctg,$from,20)) $this->print_list($ct->select_array(), $from,20,"ctg=$ctg","pg=edtprd&id=","dlt=","",$ct->found_rows());

   $this->add_msg($ct->txt_error);
   }

$this->html_out .= "</td>";

$this->html_out .= "</tr>";
$this->html_out .= "</table>";

?>
