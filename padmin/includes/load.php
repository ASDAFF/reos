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

//* Loads configuration file, db user/pass
if (file_exists(_TPLDIR."/config.php"))
include(_TPLDIR."/config.php");
else
header ("Location: install/setup-error.html");

$id_org_session=1;
require_once(_DirINCLUDES."class_prefs.php");
define("_DirHOME",realpath(""));
$prefs = new prefs();
$prefs->getPrefs();
//loading languages array
$tmplst= preg_split('/,/', _ACTIVE_LANGS);
foreach($tmplst as $ItemValue){
	$CodeValue= preg_split('/=/', $ItemValue);
	$lst_idiomas[trim($CodeValue[0])] = trim($CodeValue[1]); 
}
?>