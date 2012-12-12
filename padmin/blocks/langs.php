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
*Muestra los idiomas para canviar la visualizaci�n en el idioma seleccionado.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/langs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $lst_idiomas;

$this->html_out .="<table class=\"title\" style=\"width:100px;margin: 0.2cm 0.2cm 0.2cm 0.2cm\" ><tr><td class=\"title_label\">"._LANGS."</td></tr><tr><td style=\"padding: 0.2cm 0.2cm 0.2cm 0.2cm\">\n";

$i=0;
foreach ($lst_idiomas as $key=>$value) {
$this->html_out .="<a href=\"".LK_PAG."idma=$key\">$value</a><br/>";
$i++;
}

$this->html_out .="<td><tr></table>";


     

     
?>
