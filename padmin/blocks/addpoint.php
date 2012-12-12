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
 *Add google map point to a property.
 *Returns html into var html_out
 *@package blocks_admin
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/addpoint.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_prefs.php");

$dbi = new prefs;

$dbi->getPrefId("_GMAP_KEY");

global $id_immo;

$id_org=$this->auth["id_org"];

$dbi = new immo;

if (isset($id_immo)) {
	if ($dbi->dtl_point($id_immo))
	extract($dbi->Record);
	else {
		$this->add_msg($dbi->txt_error);
	}
}

$this->html_out .= $this->pgtitle(_ADDPOINT,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._SAVE."");

$form->add_textbox("latbox",_LATTITUD,"20","20");
$form->add_textbox("lonbox",_LONGITUD,"20","20");


$form->add_hidden("data");


$processed = $form->process();
if( !$processed ) {
	$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&id_immo=$id_immo");
	if (isset($txt_x)) $form->fields["latbox"]->value = $txt_x;
	if (isset($txt_y)) $form->fields["lonbox"]->value = $txt_y;
	
} else {
	$dbi->upd_point($form->fields["latbox"]->value, $form->fields["lonbox"]->value, $id_immo);
	$txt_x = $form->fields["latbox"]->value;
	$txt_y = $form->fields["lonbox"]->value;	
}



/**********************************************************************************************/
$script = "
<script
	src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;hl=en&amp;key="._GMAP_KEY."\"
	type=\"text/javascript\"> </script>
<script type=\"text/javascript\">

    //<![CDATA[

    function loadmap() {
      if (GBrowserIsCompatible()) {
      
        // Creates a marker whose info window displays the letter corresponding
        // to the given index.
        function createMarker(point, index, info) {
        
          var marker = new GMarker(point);	
          GEvent.addListener(marker, 'click', function() {
            marker.openInfoWindowHtml(info);
          });
          return marker;
        }  
      
        var map = new GMap2(document.getElementById('map'));
        //map.disableDragging();
        map.addControl(new GLargeMapControl()); 
        map.addControl(new GMapTypeControl());";	
        
if ((isset($txt_x) && is_numeric($txt_x))&&(isset($txt_y) && is_numeric($txt_y))){
	$script.="
        map.setCenter(new GLatLng($txt_x,$txt_y), 13);
		// Setting markers
		var point = new GLatLng($txt_x,$txt_y);        
		map.addOverlay(createMarker(point, 1, 'Latitude, Longitude<br/>' + point.toUrlValue(6)));
		";
} else {	
	$script.="
        map.setCenter(new GLatLng(41.409567,2.155831), 2);
		";	
}       
$script.="  GEvent.addListener(map, 'click', function(overlay,point){        	
			if (overlay){} 
			else {
			map.clearOverlays();
			var html = '';
		 	html += html + 'Latitude, Longitude<br/>' + point.toUrlValue(6);
		 	
		 	var marker = new GMarker(point);
		 	GEvent.addListener(marker, 'click', function() {marker.openInfoWindowHtml(html);});
		 	map.addOverlay(marker);
			document.getElementById('form1_latbox').value=point.y;
		    document.getElementById('form1_lonbox').value=point.x;
			}
		});       
        		
      }
    }
    
    if (window.addEventListener){    
    window.addEventListener('load', function() {loadmap();}, false);
    } else {
     window.attachEvent('onload',function() {loadmap();});    
    }
		
	


    //]]>
    </script>
<div id=\"map\" style=\"width: 500px; height: 300px\"></div>
";

/***********************************************************************************************/
$this->html_out.="<div class=\"comment\"><table><tr><td>";
$this->html_out.=$script;
$this->html_out.="</td><td>";
$this->html_out.=$form->draw();
$this->html_out.="</td></tr></table></div>";
?>
