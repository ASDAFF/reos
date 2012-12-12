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
*Genera la cabecera de la pag. administraci�n con el boton logout.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/hbarra.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."class_user.php");

$user=new user;

$name=$user->name_user($this->auth['uid']);
$org=$user->name_org($this->auth['id_org']);

$this->html_out .= "<table class=\"head\">\n"
    ."<tr>\n"
    //."<td class=\"head\"><img class=\"head\" src=\""._DirIMAGES._IMGPG."\" alt=\"\"/></td>\n"
    //."<td class=\"head\"><a class=\"head\" href=\"".LK_HOME."\"><img class=\"head\" src=\""._DirIMAGES._IMGHOME."\" alt=\"\"/></a></td>\n"
    ."<td class=\"head\">"._LABEL_ORG." - "._ADMIN."</td>\n"
    ."<td class=\"headr\">"._CONNECTED_AS." : $org - $name >> <a href=\"".LK_LOGOUT."\">"._LOGOUT."</a></td>"
    //."<td class=\"headr\"><img class=\"head\" src=\"../images/aguila.gif\" alt=\"\"/></td>\n"
    ."</tr>\n"
    ."</table>\n";

unset($user);
?>
