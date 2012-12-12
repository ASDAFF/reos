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
*Show my tickets statistics
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/mytkstats.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."class_support.php");
require_once(_DirINCLUDES."charts/charts.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TK_STATE',_IDIOMA);

global $view;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

$link[2]["txt"]=""._SEE2PRINT."";
$link[2]["print"]=true;  

$this->html_out .= $this->pgtitle(_MY_TK_STATS,false,$link);

$this->add_jscript("jscripts/MochiKit/MochiKit.js");
$this->add_jscript("jscripts/PlotKit/Base.js");
$this->add_jscript("jscripts/PlotKit/Layout.js");
$this->add_jscript("jscripts/PlotKit/Canvas.js");
$this->add_jscript("jscripts/PlotKit/SweetCanvas.js");

$ticket= new support;
$chart=new charts;
eval("\$labels=array("._LST_TK_STATE.");");
unset($labels[5]);unset($labels[4]);unset($labels[3]);
$values=$ticket->statitics_opened($view, $id_position, $id_org);
$this->html_out .="<table><tr><td>";
$this->html_out .=$chart->add_chart("bar",$values,$labels,300, 200);
$this->html_out .="</td><td>";
$this->html_out .=$chart->add_chart("pie",$values,$labels,300, 200);
$this->html_out .="</td></tr></table>";

?>