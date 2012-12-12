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
 *Muestra el login de usuario
 *Returns html into var html_out
 *@package blocks_admin
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/loginform.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}


if (isset($this->auth["uname"])) $name=htmlentities($this->auth["uname"]);else $name="";

//global $_POST;
$this->html_out .="<div class=\"imglogin\"><img class=\"imglogin\" src=\""._DirIMAGES."/reos.png\" alt=\"\"/></div>";
$this->html_out .="<div class=\"login\">";


$this->html_out .="<p class=\"titlogin\">"._TITLE_LOGIN."</p>";

$this->html_out .="<p class=\"txtlogin\">"._PLEASE_LOGIN." :</p>";

$this->html_out .="<form class=\"login\" action=\"".$this->self_url()."\" method=\"post\">"
."<table class=\"login\">"
."<tr class=\"login\">"
."<td class=\"login\">"._USER.":</td>"
."<td class=\"login\">"
."<input class=\"logininput\" type=\"text\" name=\"username\" value=\"$name\" size=\"32\" maxlength=\"32\"/>\n"
."</td></tr>\n"
."<tr class=\"login\">\n"
."<td class=\"login\">"._PASSWORD." :</td>\n"
."<td class=\"login\">"
."<input class=\"logininput\" type=\"password\" name=\"password\" size=\"32\" maxlength=\"32\"/>\n"
."</td></tr>\n"
."<tr class=\"login\"><td></td>\n"
."<td class=\"login_button\"><input class=\"button\" type=\"submit\" name=\"submit\" value=\""._LOGIN_NOW."\">\n"
."</td></tr></table>\n";

if ( isset($_POST["username"]) ) {
	//$this->html_out .="<p class=\"msg_admin\">"._LOGIN_FAILED."</p>\n";
	$this->add_msg(""._LOGIN_FAILED."");
}
$this->html_out .="</form>"
."<script language=\"JavaScript\">\n"
."if (document.forms[0][0].value != '') document.forms[0][1].focus()\nelse document.forms[0][0].focus()\n"
."</script>";
$this->html_out .="</div>";
?>
