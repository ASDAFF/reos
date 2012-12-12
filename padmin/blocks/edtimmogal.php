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
*Edita la galeria de un immo.
*Si la galeria no existe crear una nueva galeria.
*Mediante el navigator file se llama a edtgal.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtimmogal.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $id_gal;
global $id_immo;


if (isset($id_immo) && (!isset($id_gal) || $id_gal=="")) {
    $id_org_session=$this->auth["id_org"];
    require_once(_DirINCLUDES."class_gallery.php");
    require_once(_DirINCLUDES."class_prefs.php");
	
	$prefs = new prefs;
	
	$prefs->getPrefId("_IMAGE_SIZE");	
	$prefs->getPrefId("_THUMB_SIZE");
    $dbi= new gallery(_IMAGE_SIZE,_THUMB_SIZE);
    $fields["cod_lang"]= _IDIOMA;
    $fields["name_gal"]=_EDTIMMOGAL;
    $fields["tp_gal"]=2;
    if ($dbi->add_gallery($id_org_session, $fields))
            {
             require_once(_DirINCLUDES."class_immo.php");
             $dbi_immo= new immo;
             $immo_field["id_gal"]=$dbi->last_insert_id();
             $id_gal=$immo_field["id_gal"];
             $dbi_immo->update_immo($id_immo,$immo_field);
             unset($immo_field);unset($dbi_immo);
            }
     unset($fields);
     }
?>
