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
 * Execute diferent jobs using table Jobs.
 **/
/**
 * Before any execution checks if script is executed from command line an gets parameters.
 **/
error_reporting(E_ALL); // Deshabilitar todo reporte de errores

if (isset($argv)) { // Si tenemos Register Globals en On esta variable siempre est� definida
	parse_str($argv[1],$params);
	//print_r($argv);echo "\n";
	//print_r($params);echo "\n";
	foreach ($params as $name=>$value) {
		switch($name){
			case "host":
				$_SERVER["HTTP_HOST"]=$value;
				break;
			case "jobs":
				$_GET["jobs"]=$value;
				break;
		} //end switch
	} // end for
}

set_time_limit(60*10);

extract($_GET);

/**
 * Links of the public site.
 **/
require_once("includes/lks_1.php");  // Lista de Hyperlinks
/**
 * Constants of the public site.
 **/
require_once("includes/constants.php");
/**
 * Labels in default language or if lang is set in URL lang=code
 **/
if (isset($lang))
require_once(_DirLANGS."lang_".$lang."_adm.php");
else require_once(_DirLANGS."lang_"._DEFAULT_LANG."_adm.php");

/**
 * Global vars of public site.
 **/
require_once(_DirINCLUDES."globals.php");



if (!isset($jobs)) echo "Error parameters : host=Url of the site without http://, jobs=Job_Name separated by commas, Optional : Lang = Laguage Code (en, es, fr) if not set then default language <br/>You can create any other variable to send to the job.";
else {
	$jobs=explode(",",$jobs);
	foreach($jobs as $job){
		include(_DirADMIN."jobs/$job.php");
	}
}
?>
