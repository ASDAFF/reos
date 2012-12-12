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
*Genera la barra con el boton logout de administraci�n.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/barraadm.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_user.php");

$user=new user;

if (!array_key_exists("nm_user",$this->auth))
	$name=$user->name_user($this->auth['uid']);
	else
	$name = $this->auth['nm_user'];
	
$org=$user->name_org($this->auth['id_org']);

$this->html_out .="<table class=\"barra\">\n"
     ."<tr>\n"
     ."<td class=\"barra\">"
     .""._CONNECTED_AS." : $org - $name >> <a href=\"".LK_LOGOUT."\">"._LOGOUT."</a></td>";
$this->html_out .= "</tr>\n"
     ."</table>\n";
     
unset($user);
     
?>
