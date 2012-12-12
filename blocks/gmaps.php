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
 * Google map view
 * Returns html into var html_out
 * @package blocks_admin
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/gmaps.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
//echo $_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"];
require_once(_DirINCLUDES."class_prefs.php");

$dbi = new prefs;

$dbi->getPrefId("_GMAP_KEY");
$dbi->getPrefId("_GMAP_LAT");	
$dbi->getPrefId("_GMAP_LNG");
$dbi->getPrefId("_GMAP_LEVEL");


$Lat = _GMAP_LAT;
$Lng = _GMAP_LNG;
$Level = _GMAP_LEVEL;

$script = "
<script
	src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;hl=en&amp;key="._GMAP_KEY."\"
	type=\"text/javascript\"> </script>
<script
	src=\""._DirSCRIPTS."gmap.js\"
	type=\"text/javascript\"> </script>  
	
<script type=\"text/javascript\">
    <![CDATA[
    if (window.addEventListener){    
    window.addEventListener('load', function() {loadmap('$Lat','$Lng',$Level,'".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]."?');}, false);
    } else {
     window.attachEvent('onload',function() {loadmap('$Lat','$Lng',$Level,'".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]."?');});    
    }
    
    ]]>
    </script>

    <div id=\"map\" class=\"gmap\"></div>
";

$this->html_out.="<gmap>".$script."</gmap>";
?>
