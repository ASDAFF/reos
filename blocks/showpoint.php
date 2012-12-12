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
 * Show google map point to a property.
 * Returns html into var html_out
 * @package blocks
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/showpoint.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_prefs.php");

$dbi = new prefs;

$dbi->getPrefId("_GMAP_KEY");

global $id_immo;

$dbi = new immo;

if (isset($id_immo)) {
	if ($dbi->dtl_point($id_immo))
	extract($dbi->Record);
	else {
		$this->add_msg($dbi->txt_error);
	}
}

//$this->html_out .= $this->pgtitle(_POINT,true,null);

/**********************************************************************************************/
// //<![CDATA[
if (isset($txt_x)){
$script = "
<script
	src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;hl=en&amp;key="._GMAP_KEY."\"
	type=\"text/javascript\"> </script>
<script type=\"text/javascript\">

    

    function loadmap() {
      if (GBrowserIsCompatible()) {
      
        // Creates a marker whose info window displays the letter corresponding
        // to the given index.
        function createMarker(point, index, info) {        
          var marker = new GMarker(point);          
          return marker;
        }  
      
        var map = new GMap2(document.getElementById('map'));
        //map.disableDragging();
        map.addControl(new GLargeMapControl()); 
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng($txt_x,$txt_y), 13);
		// Setting markers
		var point = new GLatLng($txt_x,$txt_y);        
		map.addOverlay(createMarker(point, 1, 'Latitude, Longitude<br/>' + point.toUrlValue(6)));
		 }
    	}
		if (window.addEventListener){    
		    window.addEventListener('load', function() {loadmap();}, false);
		    } else {
		     window.attachEvent('onload',function() {loadmap();});    
		    }
   
    </script>\n
<div id=\"map\" style=\"width: 500px; height: 300px; margin:0.2cm 0.2cm 0.2cm 0.2cm\"></div>\n";
$this->html_out.="<showpoint>";
$this->html_out.=$script;
$this->html_out.="</showpoint>";
}
// //]]>
?>