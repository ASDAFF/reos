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
* Inicializacion de constantes web administración.
* antes de poner en marcha la generación de página.
* En este modulo hay que poner todo aquello que sea independiente del
* resultado de la pagina
*@package Init_Admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/constants.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

/**
*Language cookie's name
**/
define("_CkLANG","mxidioma");
define("_CkSTATE","state"); // Name of the page state Cookie
define("_NAVFILE","nav2"); // xml file of the layout page and navigation rules
define("_NAVPRT","navprt"); // xml file of the layout page and navigation rules for printing

define("_DirHTMLS","content/"); // Dir html file blocks
define("_DirINCLUDES","includes/"); // Directorio de los includes
define("_DirLANGS","langs/"); // Directorio donde est�n los ficheros de idiomas
define("_DirSCRIPTS","jscripts/");
define("_DirSTYLE","style/"); // Dir CSS file
define("_DirIMAGES","images/"); // Dir site images
define("_DirGALLERIES","galleries/"); // Dir image galeries
define("_DirNEWS","imgnews/"); // Dir News images
define("_DirBLOCKS","blocks/"); // Dir php blocks
define("_DirTMP","tmp/");
define("_DirADMIN","padmin/");
define("_TPLDIR","../tpl/"); // template directory and main xsl file of the template page
define("_DirPICKERS",_DirBLOCKS."pickers/");
/**
*Llama al siguiente fichero de inicializaci�n com�n a la App publica y administraci�n
**/
include(_DirINCLUDES."load.php");


?>
