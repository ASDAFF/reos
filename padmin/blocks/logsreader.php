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
*Visualiza las estadisticas de acceso por meses.
*Accede a la tabla de estad�sticas.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/logsreader.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
global $nm;
global $lst_months;

$this->html_out .= $this->pgtitle($nm,false,NULL);

$fromday=date("Ymd",mktime(0, 0, 0, date("m")-1, 01,   date("Y")-1));

$this->query("select id_day_stats, num_visits, num_robots, num_hits  from ".$this->prefix."_stats_access where id_day_stats>=$fromday order by id_day_stats DESC");
$rows=$this->select_array();

$days_month=0;
$month=0;
$i=-1;

if(isset($rows)) { // if not stats_access rows do nothing

foreach($rows as $row){
$day=strtotime($row["id_day_stats"]);

if ($month!=date("m",$day)){
   if ($month>0) {
   $stats[$i]["avg_day_visits"]=(int)($stats[$i]["num_visits"]/$days_month);
   $stats[$i]["avg_day_robots"]=(int)($stats[$i]["num_robots"]/$days_month);
   $stats[$i]["avg_day_hits"]=(int)($stats[$i]["num_hits"]/$days_month);
   }
   $month=date("m",$day);
   $i++;
   $stats[$i]["id_month"]=date("Y",$day).$month;
   $stats[$i]["month"]=$lst_months[(int)$month-1]." ".date("Y",$day);
   $stats[$i]["num_visits"]=$row["num_visits"];
   $stats[$i]["num_robots"]=$row["num_robots"];
   $stats[$i]["num_hits"]=$row["num_hits"];
   $days_month=1;

   } else {
   $days_month++;
   $stats[$i]["num_visits"]=$stats[$i]["num_visits"]+$row["num_visits"];
   $stats[$i]["num_robots"]=$stats[$i]["num_robots"]+$row["num_robots"];
   $stats[$i]["num_hits"]=$stats[$i]["num_hits"]+$row["num_hits"];
   }
}
$stats[$i]["avg_day_visits"]=(int)($stats[$i]["num_visits"]/$days_month);
$stats[$i]["avg_day_robots"]=(int)($stats[$i]["num_robots"]/$days_month);
$stats[$i]["avg_day_hits"]=(int)($stats[$i]["num_hits"]/$days_month);

//print_r($stats);
$num_rows=count($stats);

$this->print_list($stats,0,$num_rows,null,"pg=logsmonth&nm="._MONTH."&month=",null,null,$num_rows);

//**** 10 top referers
$this->html_out .= $this->pgtitle("Top 10 "._REFERERS,false,NULL);

$this->query("SELECT 1, txt_url, sum(total) as total FROM ".$this->prefix."_stats_referers GROUP BY txt_url order by total desc limit 0,10;");
$rows=$this->select_array();
$num_rows=count($rows);
$this->print_list($rows,0,$num_rows,null,null,null,null,$num_rows);

//**** 10 top search strings
$this->html_out .= $this->pgtitle("Top 10 "._TXT_SEARCH,false,NULL);
$this->query("SELECT 1, txt_search, sum(total) as total FROM ".$this->prefix."_stats_strings GROUP BY txt_search order by total desc limit 0,10;");
$rows=$this->select_array();
$num_rows=count($rows);

$this->print_list($rows,0,$num_rows,null,null,null,null,$num_rows);

}
?>
