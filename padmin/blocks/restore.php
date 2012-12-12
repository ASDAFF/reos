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
 *Recupera los datos de una backup de la base de datos.
 *No esta implementada !!!
 *Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/restore.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

//---- Including the class.
require_once(_DirINCLUDES."class_backup.php");

$backup = new mysql_backup(_DB_HOST,_DB_NAME,_DB_USER,_DB_PWD,"tmp/backup.sql",false);

//---- calling the backup method finally creats a file with the name specified in $output
//     and stors everythig so you can copy the file anywhere you want. This file will be
//     restored with another method of this class named "restore" that is described in
//     example-backup.php
$backup->restore();
?>
