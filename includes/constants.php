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
* Constants Initialization.
*@package Init_Public
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/constants.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

/**
*Language Cookie Name
**/
define("_CkLANG","mxidioma1");
/**
*Cookie state Name
**/
define("_CkSTATE","state1");
/**
*xml file of the layout page and navigation rules
**/
define("_NAVFILE","nav2");
/**
*xml file of the layout page and navigation rules
**/
define("_NAVMEMBERS","members");
/**
*xml file of the layout page and navigation rules for printing
**/
define("_NAVPRT","navprt");
/**
*Admin site folder
**/
define("_DirADMIN","padmin/");
/**
*Temp folder
**/
define("_DirTMP",_DirADMIN."tmp/");
/**
*Content folder - xml files in
**/
define("_DirHTMLS","content/");
/**
*Includes
**/
define("_DirINCLUDES",_DirADMIN."includes/");
/**
*Directorio donde est�n los ficheros de idiomas
**/
define("_DirLANGS",_DirADMIN."langs/");
/**
*Dir de javascripts y vbscripts
**/
define("_DirSCRIPTS",_DirADMIN."jscripts/");
/**
* Dir image galeries
**/
define("_DirGALLERIES",_DirADMIN."galleries/");
/**
* Dir News images
**/
define("_DirNEWS","imgnews/");
/**
*Dir php blocks
**/
define("_DirBLOCKS","blocks/");
/**
*Template directory and main xsl file of the template page
**/
define("_TPLDIR","tpl/");
//*********************************************************************************
require_once(_DirINCLUDES."load.php");
//*********************************************************************************
/**
*Dir CSS file
**/
define("_DirSTYLE",_TPLDIR._THEME_DIR."/style/");
/**
*Dir site images
**/
define("_DirIMAGES",_TPLDIR._THEME_DIR."/images/");

?>
