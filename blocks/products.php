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
if (preg_match("/products.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $id_org_session;
global $ctg;

require_once(_DirINCLUDES."class_catalog.php");
require_once(_DirINCLUDES."class_prefs.php");

$prefs = new prefs;

$prefs->getPrefId("_IMAGE_SIZE");	

$cat = new catalog;
$cat->cod_lang = _IDIOMA;

$id = 0;
$cat->ctg_array( $id, $id_org_session, $ctg_array );
$this->add_msg( $cat->txt_error );

if( is_array($ctg_array) && array_key_exists($id,$ctg_array))
    $maxim = count( $ctg_array[$id] );
else $maxim=0;

$this->html_out .= "<categs title=\""._CATALOG."\" >\n";
$z=0;
while ($z<$maxim){
    $param = "pg=products&ctg=".$ctg_array[$id][$z]["id"];
    $this->html_out .= "<cat href=\"".$this->getPermalink($ctg_array[$id][$z]["value"])."".$this->url_encrypt($param)."\">".$ctg_array[$id][$z]["value"]."</cat>\n";
    $z++;
}
$this->html_out .= "</categs>\n";


//********************  products *********************************************
if( isset($ctg) && $ctg!="" ) {

  $cat->products_home( $ctg, 1, $this->auth["uid"] );
  $result = $cat->select_array();

  if (is_array($result)){
    $this->html_out .= "<products title=\""._VERPRICEPRD." / "._USER.": '".$this->auth["uname"]."' \" >\n";
    foreach($result as $prod) {
        $this->html_out .= "<prod>\n";
        $this->html_out .= "<cat>".$prod["name_category"]."</cat>\n";
        $this->html_out .= "<cod>".$prod["cod_product"]."</cod>\n";
        $this->html_out .= "<des>".$prod["name_product"]."</des>\n";
        $this->html_out .= "<pre>".$prod["precio"]."</pre>\n";

        $dir_gal = $prod["dir_gal"];
        if( isset($dir_gal) && $dir_gal!="" ) {
            // ex:  padmin/galeria/9bfa86/thumbnails
            $dir_thumbnails = _DirGALLERIES."$dir_gal/thumbnails";
            $dir_fotos = _DirGALLERIES."$dir_gal/images";
            $file = $prod["img_front"];
            $this->html_out .= "<a href=\"$dir_fotos/$file\" size=\""._IMAGE_SIZE."\" img=\"$dir_thumbnails/$file\"/>";
        }
        else
            $this->html_out .= "<a>"._NO_IMG_PORT."</a>\n";
        
        $this->html_out .= "</prod>\n";
    }
    $this->html_out .= "</products>\n";
  }
}

?>
