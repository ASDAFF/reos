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


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_zip.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
/**
* Creates a compressed Zip/Gzip file or decompress it
* @author   IT Elazos SL
* @version  1.0
* @package  Archive
*/
class zip  {

var $type=array("GZIP"=>false,"ZIP"=>false);// can be "ZIP" or "GZIP"

function zip() {

if (extension_loaded("zlib")) $this->type["GZIP"]=true;
if (extension_loaded("zip")) $this->type["ZIP"]=true;

}

/**
*Decompress a Zip or GZIP file with path - Only selected file extensions
*@author IT ELAZOS SL
*@access public
*@return boolean
*@param string Source folder - Complete path
*@param string Zip file name without extention zip
*@param string Target folder
*@param array File extensions to decompress ex: "jpg","png","gif"
*/
function unzip($dir,$file,$target_folder,$formats,$create_path=True) {

$result=false;
$name=strtolower(basename($file));
$len=strlen($name);
$pos=strpos($name,".")+1;
$ext=substr($name,$pos,$len);
switch ($ext) {
case "zip":
    if ($this->type["ZIP"]) $result=$this->unpackZip($dir,$file,$target_folder,$formats,$create_path);
    break;
case "tgz":
case "tar.gz";
    if ($this->type["GZIP"]) $result=$this->unpackGZip($dir,$file,$target_folder,$formats,$create_path);
    break;
}
return $result;
}
/**
*Decompress a GZip file with path - Only selected file extensions
*@author IT ELAZOS SL
*@access public
*@return boolean
*@param string Source folder - Complete path
*@param string Zip file name without extention tar.gz or tgz
*@param string Target folder
*@param array File extensions to decompress ex: "jpg","png","gif"
*/
function unpackGZip($dir,$file,$target_folder,$formats,$create_path=True) {

include(_DirINCLUDES."class_tar.php");
$tar_object = new Archive_Tar($dir.$file,true);

$f_list=array();
if (($v_list  =  $tar_object->listContent()) != 0)
    for ($i=0; $i<sizeof($v_list); $i++)
    {
     // If is a file
    $name=strtolower(basename($v_list[$i]["filename"]));
    $len=strlen($name);
    $pos=strpos($name,".")+1;
    $ok_format=false;
    foreach($formats as $value) {
        if ($ok_format) break;
        $value=strtolower($value);
        if (substr($name,$pos,$len)==$value) $ok_format=true;
        }
     if ($ok_format) {
        $f_list[]=$v_list[$i]["filename"];
        }
    }

if (sizeof($f_list)>0) {
   if (!$create_path) $remove_path=dirname($f_list[0]);
   $tar_object->extractlist($f_list,$target_folder,$remove_path);
   }

if (!$create_path) {
    $tmp_array=$f_list;
    $f_list=array();
    foreach ($tmp_array as $files) {
       $f_list[]=basename($files);
       }
   }
return $f_list;
}

/**
*Decompress a Zip file with path - Only selected file extensions
*@author IT ELAZOS SL
*@access public
*@return boolean
*@param string Source folder - Complete path
*@param string Zip file name without extention zip
*@param string Target folder
*@param array File extensions to decompress ex: "jpg","png","gif"
*/
function unpackZip($dir,$file,$target_folder,$formats,$create_path=True) {

   if (function_exists("zip_open") && file_exists($dir.$file) && $zip = zip_open($dir.$file)) {
     if ($zip) {
       if (!file_exists($target_folder)) mkdir($target_folder,"0755");
       while ($zip_entry = zip_read($zip)) {
         if (zip_entry_open($zip,$zip_entry,"r")) {
           $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
           $dir_name = dirname(zip_entry_name($zip_entry));
           // Handel folders
           $dir_op = $target_folder."/";
           if ($create_path && $dir_name != ".") {

               foreach ( explode("/",$dir_name) as $k) {
                 $dir_op = $dir_op . $k;
                 if (is_file($dir_op)) unlink($dir_op);
                 if (!is_dir($dir_op)) mkdir($dir_op,"0755");
                 $dir_op = $dir_op . "/" ;
                 }
               }
           // If is a file
           $name=strtolower(basename(zip_entry_name($zip_entry)));
           $len=strlen($name);
           $pos=strpos($name,".")+1;
           $ok_format=false;
           foreach($formats as $value) {
           if ($ok_format) break;
           $value=strtolower($value);
           if (substr($name,$pos,$len)==$value) $ok_format=true;
           }

           if ($ok_format){
           if ($fp=fopen($dir_op.$name,"w"))
               {
               fwrite($fp,$buf);
               $unziped[]=$name;
               fclose($fp);
               }
           }
           zip_entry_close($zip_entry);
       } else
           return false;
       }
       zip_close($zip);
     }
  } else return false;

  return $unziped;
}

//************************************FIN CLASSE
}
?>
