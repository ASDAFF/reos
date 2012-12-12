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
 * Define first view of google maps
 * Returns html into var html_out
 * @package blocks_admin
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/mapinit.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_prefs.php");

$dbi = new prefs;

$dbi->getPrefId("_GMAP_KEY");

$this->html_out .= $this->pgtitle(_MAPINIT,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._SAVE."");

$form->add_textbox("latbox",_LATTITUD,"20","20");
$form->add_textbox("lonbox",_LONGITUD,"20","20");
$form->add_textbox("level","Level","2","2");

$form->add_hidden("data");


$processed = $form->process();
if( !$processed ) {

	
	$dbi->getPrefId("_GMAP_LAT");
	$dbi->getPrefId("_GMAP_LNG");
	$dbi->getPrefId("_GMAP_LEVEL");
	$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]);
	$form->fields["latbox"]->value = _GMAP_LAT;
	$form->fields["lonbox"]->value = _GMAP_LNG;
	$form->fields["level"]->value = _GMAP_LEVEL;
	
} else {	
	$dbi->setPrefId("_GMAP_LAT",$form->fields["latbox"]->value);
	$dbi->setPrefId("_GMAP_LNG",$form->fields["lonbox"]->value);
	$dbi->setPrefId("_GMAP_LEVEL",$form->fields["level"]->value);
    $dbi->getPrefId("_GMAP_LAT");
	$dbi->getPrefId("_GMAP_LNG");
	$dbi->getPrefId("_GMAP_LEVEL");

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
      
 
      
        var map = new GMap2(document.getElementById('map'));
        //map.disableDragging();
        map.addControl(new GLargeMapControl()); 
        map.addControl(new GMapTypeControl());
        
        
        GEvent.addListener(map, 'moveend', function() {		 
		  document.getElementById('form1_latbox').value=map.getCenter().lat();
		    document.getElementById('form1_lonbox').value=map.getCenter().lng();
		    document.getElementById('form1_level').value=map.getBoundsZoomLevel(map.getBounds());		 
		});   
        
        
        ";	
        
if (defined("_GMAP_LAT")){
	$script.="
        map.setCenter(new GLatLng("._GMAP_LAT.","._GMAP_LNG."), "._GMAP_LEVEL.");";
} else {	
	$script.="
        map.setCenter(new GLatLng(41.409567,2.155831), 1);
		";	
}       
$script.="         
        		
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
