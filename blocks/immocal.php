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
*Returns xml node for the calendar of an immo holiday rental.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/cal.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $id_immo;

require_once(_DirINCLUDES."class_calendar.php");
require_once(_DirINCLUDES."class_himmo.php");

$calendar= new Calendar;
$calendar->cols=3;
$himmo=new himmo;
$today=getdate();

$himmo->seasons($id_immo);
$calendar->seasons=$himmo->select_array();

if (count($calendar->seasons)>0) {
    //$himmo->bookings($id_immo,(($today["year"]*100)+$today["mon"])*100+$today["mday"],3);     // only tp_state 3 confirmed
    $himmo->bookings($id_immo,(($today["year"]*100)+$today["mon"])*100+1,3);
    $calendar->busydays=$himmo->select_array();
    $calendar->setStartMonth($today["mon"]);
    $calendar->endMonth=(int) substr($calendar->seasons[count($calendar->seasons)-1]["dt_end"],4,2);
    $this->html_out.="<immocal>";
    $this->html_out.=$calendar->getYearView($today["year"],1);
    //$this->html_out.=$calendar->getMonthView(3,2005);
    $this->html_out.="</immocal>";
    }

?>
