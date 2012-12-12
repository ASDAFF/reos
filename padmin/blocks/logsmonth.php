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
*Visualiza las estadisticas de acceso diarias de un mes.
*Accede a la tabla de estad�sticas.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/logsmonth.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
global $nm;
global $month;
global $lst_months;

list($year,$month)=sscanf($month,"%4d%2d");

$this->html_out .= $this->pgtitle($lst_months[(int)$month-1]." $year",true,NULL);

$fromday=date("Ymd",mktime(0, 0, 0, $month, 01, $year));
$month_tmp=$month;
if ((int)$month==12) {$year=(int)$year+1;$month_tmp=0;}
$endday=date("Ymd",mktime(0, 0, 0, $month_tmp+1, 01, $year));

$this->query("select id_day_stats, DATE_FORMAT(id_day_stats,"._DATE_SQL.") as date, num_visits, num_robots, num_hits  from ".$this->prefix."_stats_access where id_day_stats>=$fromday and id_day_stats<$endday order by id_day_stats DESC");
$rows=$this->select_array();

$num_rows=count($rows);

//$this->print_list($rows,0,$num_rows,null,"pg=logsday&nm="._DAY."&id_day_stats=",null,null,$num_rows);
$this->print_list($rows,0,$num_rows,null,null,null,null,$num_rows);

// referers
$this->query("select int_month, txt_url, total  from ".$this->prefix."_stats_referers where int_month=$month order by total DESC;");
$rows=$this->select_array();
$num_rows=count($rows);
$this->html_out .= $this->pgtitle(_REFERERS." ".$lst_months[(int)$month-1]." $year",false,NULL);
$this->print_list($rows,0,$num_rows,null,null,null,null,$num_rows);

// search strings
$this->query("select int_month, txt_search, total  from ".$this->prefix."_stats_strings where int_month=$month order by total DESC;");
$rows=$this->select_array();
$num_rows=count($rows);
$this->html_out .= $this->pgtitle(_TXT_SEARCH." ".$lst_months[(int)$month-1]." $year",false,NULL);
$this->print_list($rows,0,$num_rows,null,null,null,null,$num_rows);


?>
