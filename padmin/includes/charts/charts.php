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

class charts {
	/**
	 * Generates a jscript for a chart in Plotkit
	 *
	 * @param string $type Can be "pie", "bar"
	 * @param array $values
	 * @param pixels $width
	 * @param pixels $height
	 */
	function add_chart($type, $values, $labels, $width, $height){

	$name = strtoupper(substr(md5(uniqid("")), 0, 9));	
	$result = "";
	$labs = "";
	$vals ="";
	$cont=0;
	foreach($labels as $key=>$kvals){
		if ($cont>0) $labs.=",";
		$labs.="{v:$cont, label:\"$kvals\"}";		
		$cont++;			
	}
	$cont=0;
	foreach($values as $key=>$kvals){
		if ($cont>0) $vals.=",";
		$vals.="[$cont,$kvals]";		
		$cont++;			
	}	
	$result.="<script>var options = {\"xTicks\": [$labs]};\n"  ;
    
	$result.="function drawGraph() {\n"
    ."var layout = new PlotKit.Layout(\"$type\", options);\n"
    ."layout.addDataset(\"sqrt\", [$vals]);"
    ."layout.evaluate();"
    ."var canvas = MochiKit.DOM.getElement(\"$name\");"
    ."var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);"
    ."plotter.render();"
	."}\n"
	."MochiKit.DOM.addLoadEvent(drawGraph);</script>\n";
		
	$result.="<div class=\"comment\"><canvas id=\"$name\" width=\"$width\" height=\"$height\"></canvas></div>\n";
	return $result;	
	}
	
}
?>