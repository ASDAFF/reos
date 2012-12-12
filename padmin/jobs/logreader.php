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
* Apache Access log reader.
* If you use variable file=complete File path
* If you use variable pfile=path and file name withou -month-year.gz (This is set by script).
**/
/**
*Class MySql Database.
**/
require_once(_DirINCLUDES."class_mysql.php");
/**
*Class Apache access log reader.
**/
require_once(_DirINCLUDES."class_apachelogsreader.php");
/*
global $HTTP_GET_VARS;

if (array_key_exists("pfile",$HTTP_GET_VARS))
    $pfile=$HTTP_GET_VARS["pfile"]."-".date("M-Y").".gz";
    elseif (array_key_exists("file",$HTTP_GET_VARS))
           $pfile=$HTTP_GET_VARS["file"];
*/
           
$yesterday=mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
$pfile=_DirLOGS."-".date("M-Y",$yesterday).".gz";;

$log = new logreader($yesterday);

if (isset($pfile) && $log->readlog("$pfile")) {

  $log->getvisits();

  $dbi=new DB_Sql;

  foreach($log->visitors as $stat){
    $dbi->query("insert into ".$dbi->prefix."_stats_access (id_day_stats, num_visits, num_robots, num_hits) values (".$stat["date"].",".$stat["visits"].",".$stat["robots"].",".$stat["hits"].");");
    
    //foreach($stat["ips"] as $visit) {
    //$dbi->query("insert into ".$dbi->prefix."_visits (id_visit_ip, id_day_stats, time_visit, txt_referer) values ('".$visit["ip"]."',".$stat["date"].",'".$visit["time"]."','".$visit["refer"]."');");
    //}
  }
  $log->getrefers();

  foreach($log->refers as $refers) {
    $dbi->query("select * from ".$dbi->prefix."_stats_referers where int_month=".$refers["month"]." and txt_url='".$refers["url"]."';");
    if ($dbi->num_rows()==0) {
       $dbi->query("insert into ".$dbi->prefix."_stats_referers (int_month, txt_url, total) values (".$refers["month"].",'".$refers["url"]."',".$refers["total"].");");
       } else {
       $dbi->next_record();
       $total=$dbi->Record["total"]+$refers["total"];
       $dbi->query("update ".$dbi->prefix."_stats_referers set total=$total where int_month=".$refers["month"]." and txt_url='".$refers["url"]."';");
       }
    }
  foreach($log->search_str as $refers) {
    $dbi->query("select * from ".$dbi->prefix."_stats_strings where int_month=".$refers["month"]." and txt_search='".$refers["search"]."';");
    if ($dbi->num_rows()==0) {
       $dbi->query("insert into ".$dbi->prefix."_stats_strings (int_month, txt_search, total) values (".$refers["month"].",'".$refers["search"]."',".$refers["total"].");");
       } else {
       $dbi->next_record();
       $total=$dbi->Record["total"]+$refers["total"];
       $dbi->query("update ".$dbi->prefix."_stats_strings set total=$total where int_month=".$refers["month"]." and txt_search='".$refers["search"]."';");
       }
    }

} else {
  if (file_exists($pfile)) echo "Log file : $pfile exists but has wrong format";
      else echo "File ".$pfile." does not exists";
}

?>
