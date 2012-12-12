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
* Class Apache access log reader.
**/
/**
* Reads an apache access log file.
*
* @version      1.2
* @author       IT eLazos SL
* @package Statistics
**/
class logreader
{
var $logfile;
var $phpformat;
var $log;
var $visitors;
var $refers;
var $search_str;  // Array[1..n] will contain search engines strings
var $search_var; // name of the variable used by most search engines
var $day;        // Timestamp, If you just want to read one day within the log file, if null reads all days.
/**
*Contructor of logreader class.
*Sets vars : phpformat, log.
*@author Josep Marxuach
*@access private
*/
function logreader($day=NULL){
  $this->phpformat="%s %s %s %11s:%8s %s %s %s %s %s %s %s %s %s %s %s %s %s";
  $this->log=array();
  $this->search_var="q";
  $this->day=$day;
}
/**
*Reads apache access log file.
*Puts all file into array log.
*@author Josep Marxuach
*@access public
*@return boolean True if read is ok, false if any reading error.
*@param string Log file name with path.
*/
function readlog($file){
  $this->logfile=$file;
  $name=strtolower(basename($file));
  $len=strlen($name);
  $pos=strrpos($name,".")+1;
  $ext=substr($name,$pos,$len);
  switch ($ext) {
  case "log":
    if (!file_exists($this->logfile) || !$hFile = fopen($this->logfile,"r")) return false;
    $i=0;
    while ($line = fgets($hFile)) {
       $this->read_line($line,$i);
       $i++;
       }// end while
    fclose($hFile);
    return true;
    break;
  case "gz":
    if (!file_exists($this->logfile) || !$hFile = gzopen($this->logfile,"r")) return false;
    $i=0;
    while ($line = gzgets($hFile,4096)) {
       $this->read_line($line,$i);
       $i++;
       }// end while
   gzclose($hFile);
   return true;
   break;
   }

} // End function readlog
/**
*Take a log line and split into array vars $this->log.
*If is set class var day, only gets day logs.
*@author Josep Marxuach
*@access private
*/
function read_line($line,$i){
$line = preg_replace("/[\135|\133|\"|\;|\(|\)]/","",$line);
$line_vars=sscanf($line, $this->phpformat);
$lineday=strtotime(str_replace("/"," ",$line_vars[3]));
if ($this->day==NULL || $this->day==$lineday) {
   $this->log[$i] = $line_vars;
   $this->log[$i][3] = date("Ymd",$lineday);
   }
}

/**
*Gets visits information.
*Put visits information into class array visitors from class array log.
*@author Josep Marxuach
*@access public
*@return boolean false if any error, true if success.
*/
function getvisits(){
$ip="";
$this->visitors=array();
$date=NULL;
$i=0;
$x=-1;
foreach($this->log as $key => $hit){
  if ($hit[9]=="200"
      && ((strpos($hit[7],"index.php"))||(strpos($hit[7],"GET / ")) ||(strpos($hit[7],"robots.txt")))
      && !strpos($hit[7],"padmin")) {
  if ($date!=$hit[3]){
  if ($date!=NULL) {$this->visitors[$x]["visits"]=count($this->visitors[$x]["ips"]);/*unset($this->visitors[$x]["ips"]);*/}
  $x++;
  $date=$hit[3];
  $i=0;
  $this->visitors[$x]["ips"][$i]["ip"]=$hit[0];
  $this->visitors[$x]["ips"][$i]["refer"]=$hit[11];
  $this->visitors[$x]["ips"][$i]["time"]=$hit[4];
  $this->visitors[$x]["date"]=$date;
  $this->visitors[$x]["hits"]=1;
   if (1==strpos($hit[7],"robots.txt")) $this->visitors[$x]["robots"]=1;else $this->visitors[$x]["robots"]=0;

  } else
  {
  $exists=false;
  foreach($this->visitors[$x]["ips"] as $ips) {
          if ($hit[0]==$ips["ip"]) {$exists=true;break;}
          //if ($referer["host"]==$ips["refer"]) {$exists=true;break;}
          }
  if (!$exists)
     {$i++;$this->visitors[$x]["ips"][$i]["ip"]=$hit[0];
     $this->visitors[$x]["ips"][$i]["refer"]=$hit[11];
     $this->visitors[$x]["ips"][$i]["time"]=$hit[4];
     if (1==strpos($hit[7],"robots.txt")) $this->visitors[$x]["robots"]++;
     }
  $this->visitors[$x]["hits"]++;
  }
  $this->visitors[$x]["visits"]=count($this->visitors[$x]["ips"]);
  }
}
//unset($this->visitors[$date]["ips"]);
//print_r($this->visitors);die();
return true;
}//end function getvisits
/**
*Gets referers information.
*Put referers information into class array refer from class array visitors.
*@author Josep Marxuach
*@access public
*@return boolean false if any error, true if success.
**/
function getrefers(){
$this->refers=array();
$this->search_str=array();
$i=-1;
$z=-1;
foreach($this->visitors as $visits){

  foreach($visits["ips"] as $ips){
  if (strlen($ips["refer"])>4) $referer=parse_url($ips["refer"]);else $referer["host"]="";
      if (!array_key_exists("host",$referer)) $referer["host"]="";
      // check if domain exists on referers array
      $exists=false;
      foreach($this->refers as $key=>$ref) if ($ref["month"]==substr($visits["date"],4,2) && $ref["url"]==$referer["host"]) {$exists=true;break;}
      if (!$exists) {
      $i++;
      $this->refers[$i]["url"]=$referer["host"];
      $this->refers[$i]["month"]=substr($visits["date"],4,2);
      $this->refers[$i]["total"]=1;
      } else {
      $this->refers[$key]["total"]++;
      }
      // check if search string exists on search_str array

      if (array_key_exists("query",$referer)) {
          parse_str($referer["query"], $qvalues);
          if (array_key_exists($this->search_var,$qvalues)) {
              //$this->search_str[]=$qvalues[$this->search_var];
              $exists=false;
              foreach($this->search_str as $key=>$ref) if ($ref["month"]==substr($visits["date"],4,2) && $ref["search"]==$qvalues[$this->search_var]) {$exists=true;break;}
              if (!$exists) {
              $z++;
              $this->search_str[$z]["search"]=$qvalues[$this->search_var];
              $this->search_str[$z]["month"]=substr($visits["date"],4,2);
              $this->search_str[$z]["total"]=1;
              } else {
              $this->search_str[$key]["total"]++;
              }
          } // end if search_var exists
     } // end if query string
  } // end foreach visits
}
return true;
}// end function getrefers

} // ************ END CLASS **********************************

?>
