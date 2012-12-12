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
*Returns xml node for mailing list registration confirmation.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/confirm.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

//require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_account.php");


global $id_org_session;
global $usr;
global $psw;
global $op;

if (!isset($op)) $op=0;
$tit_pag=""._MAIL_SUBS."";
$dbi= new account;


$this->html_out .= $this->pgtitle($tit_pag,true,Null);

$this->html_out .= "<confirm title=\"$tit_pag\">";

if ($dbi->confirm($id_org_session,$usr,$psw)) {
    $this->html_out .= _USR_CONFIRM."";
    if ($op==1) $this->html_out .="<login>"._CUSTZONE."</login>";
    }
    else $this->html_out .= ""._USR_NOCONFIRM."";
$this->html_out .= "</confirm>";

?>
