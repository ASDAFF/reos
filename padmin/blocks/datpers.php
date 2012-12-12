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
*Muestra los datos de un usuario.
*se creo por temas de navegaci�n ya que llama directamente a edtuser.
*Sirve para diferenciar la edici�n desde verusers de desde incio.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/datpers.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
global $user_id;
$user_id = $this->auth["uid"];
include(_DirBLOCKS."edtuser.php");

?>
