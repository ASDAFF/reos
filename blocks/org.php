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
*Returns xml node for list of offices.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/org.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $id_org_session;

$id_dept=$id_org_session;

require_once(_DirINCLUDES."class_mysql.php");

$dbi= new DB_Sql;

if (isset($id_dept)) {
    $dbi->query("select name_org, txt_address1, txt_zona, txt_cp, txt_poblacion, txt_provincia, txt_telf1, txt_telf2, txt_fax, txt_email1, txt_web "
    ." from ".$this->prefix."_org where ".$this->prefix."_org.root_id_org=$id_dept OR ".$this->prefix."_org.id_org=$id_dept ORDER BY ".$this->prefix."_org.parent_id_org;");
    }

$tit_pag=""._OFICINAS."";

$this->html_out .= $this->pgtitle($tit_pag,true,null);
$this->html_out .= "<offices>";
$this->html_out .= $dbi->select_xml(null,false);
$this->html_out .= "</offices>";


?>
