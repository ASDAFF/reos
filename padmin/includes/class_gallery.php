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
*Class Gallery definition file
*@author Josep Marxuach  - May 2004
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_gallery.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

/**
*Class org definition file
**/
require_once(_DirINCLUDES."class_utils.php");

/**
*Handles Galleries - using table prefix_gallery and folder gal/thumbnails + gal/images
*@author Josep Marxuach
*@version 1.0
*@copyright 2004 by Josep Marxuach
*@package BusObj
*/

class gallery extends utils {
/**
*Folder name of the image gallery ex : "folder/"
*@var string */
var $path_images=_DirGALLERIES;
/**
*Temporary Folder for image resizing : "folder/"
*@var string */
var $path_tmp=_DirTMP;
/**
*Image width size in pixels
*@var integer
*/
var $image_size; // pixel
/**
*Thumbnail image width in pixels
*@var integer
*/
var $thumbnail_size; // pixel portada
/**
*Message after execution of any method
*@var string
*/
var $txt_error;


function gallery($img_size, $thumb_size){
	
	$this->image_size=$img_size; // pixels
	$this->thumbnail_size=$thumb_size; //pixels
	
}

/**
*Delete gallery $id from the table prefix_gallery and delete folder based on table field dir_gal
*@author Josep Marxuach
*@access public
*@param integer Gallery id
*@return boolean
*/
function delete_gallery($id){

if (!$this->gallery_dtl($id)) return false;

$dir_gal=$this->Record["dir_gal"];

if ($this->query("update ".$this->prefix."_immos set id_gal=NULL where id_gal=$id;") 
	&& $this->query("delete from ".$this->prefix."_gallery where id_gal=$id;"))
    {
    if ($this->query("select id_gal from ".$this->prefix."_gallery where dir_gal='$dir_gal';"))
       {
       if ($this->num_rows()==0) $this->deldir($this->path_images."$dir_gal");
       }
    $this->txt_error=""._DELETED."";
    return true;}
    else { $this->txt_error=""._ERROR_DELETE.""; return false;}
}
/**
*Add gallery of the organization $id_org using table field array $field to the table prefix_gallery
*@author Josep Marxuach
*@access public
*@param integer organization id
*@param array Assoc array[fieldname]=value
*@param boolean True=creates folders, false=doesn't create folders
*@return boolean
*/
function add_gallery($id_org, $fields, $folder=false){

if (!is_array($fields)) return false;

if (!$folder) {
   $folder=substr(md5(uniqid("")), 0, 6);  
   if (!mkdir ($this->path_images."$folder", 0755) ||
       !mkdir ($this->path_images."$folder/thumbnails", 0755) ||
       !mkdir ($this->path_images."$folder/images", 0755))
              {$this->txt_error=""._ERROR_DIR_GAL."";return false;}
  $fields["dir_gal"]=$folder;
   } else $fields["dir_gal"]=$folder;

if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

reset($fields);
$st_field="";
$st_value="";
while (list($key,$value)=each($fields))
    {
    if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
    $st_field=$st_field.$key;
    $st_value=$st_value.$value;
    }

if ($this->query("insert into ".$this->prefix."_gallery (id_org, $st_field) values ($id_org, $st_value);"))
        {$this->txt_error=""._INSERTED.""; return $folder;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
}

/**
*list prefix_gallery table rows based on organization, language, type general=1, propiedad=2
*@author Josep Marxuach
*@access public
*@return boolean
*@param integer organization id
*@param string[2] language code
*@param integer type 1=public 2=propiedad
*@param string Name of the folder
*@param string Fields lists to display as in sql statement
*/
function list_gallery($id_org, $idioma, $keyword=false, $from=null, $offset=null,$type=1,$dir_gal=NULL,$select=NULL){

if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
if (isset($idioma)) $w_idioma ="and cod_lang='$idioma'";else $w_idioma="";
if (isset($dir_gal)) $w_dir_gal ="and dir_gal='$dir_gal'";else $w_dir_gal="";
if (isset($select)) $select=$select;
               else $select="id_gal, CONCAT(dir_gal,'&#47;thumbnails&#47;',img_front) as img_front, name_gal, txt_desc";

$where="";
if (isset($keyword) && $keyword!="" && $keyword!=false) $where=" and (name_gal like '%$keyword%' or txt_desc like '%$keyword%')";

if ($this->query("select SQL_CALC_FOUND_ROWS $select from ".$this->prefix."_gallery where id_org=$id_org $w_idioma $w_dir_gal and tp_gal=$type $where ORDER BY id_gal DESC $limit ;"))
    { if ($this->num_rows()==0) { $this->txt_error=""._NO_GALS.""; return false;} else $this->txt_error="";
      return true;
    }
    else { $this->txt_error=""._ER_SELET_TBL." GAL"; return false;}
}
/**
*Add image to folders /images and /thumbnails within the folder gallery
*@author Josep Marxuach
*@access public
*@param string folder name
*@param string image filename
*@return boolean
*/
function add_img($dir_gal, $image){

if (!is_dir($this->path_images.$dir_gal)) mkdir($this->path_images.$dir_gal,"0755");
if (!is_dir($this->path_images.$dir_gal."/thumbnails")) mkdir($this->path_images.$dir_gal."/thumbnails","0755");
if (!is_dir($this->path_images.$dir_gal."/images")) mkdir($this->path_images.$dir_gal."/images","0755");

if (!isset($image) || $image=="") {$this->txt_error=_IMG_FILE_ERROR;return false;}
if(preg_match("/[^A-Za-z0-9._]/", $image)) {unlink($this->path_tmp.$image);$this->txt_error=_IMG_FILE_ERROR;return false;}

if ($this->img_resize($this->path_tmp.$image, $this->path_images.$dir_gal."/images/".$image, $this->image_size)
    &&
    $this->img_resize($this->path_tmp.$image, $this->path_images.$dir_gal."/thumbnails/".$image, $this->thumbnail_size)
    )
    {unlink($this->path_tmp.$image);return true;}
    else {unlink($this->path_tmp.$image);$this->txt_error=_IMG_FILE_ERROR;return false;}
}
/**
*delete image from folders /images and /thumbnails within the folder gallery
*@author Josep Marxuach
*@access public
*@param string folder name
*@param string image filename
*@return boolean
*/
function borrar_img($dir, $imatge1){

if (isset($imatge1)&& $imatge1!="" && file_exists(""._DirGALLERIES.$dir."/thumbnails/".$imatge1))
    if (!unlink(""._DirGALLERIES.$dir."/thumbnails/".$imatge1))
       {$this->txt_error=""._ERROR_DIMG."";return false;}
if (isset($imatge1)&& $imatge1!="" && file_exists(""._DirGALLERIES.$dir."/images/".$imatge1))
    if (!unlink(""._DirGALLERIES.$dir."/images/".$imatge1))
       {$this->txt_error=""._ERROR_DIMG."";return false;}
return true;
}
/**
*Get gallery details from the table prefix_gallery
*@author Josep Marxuach
*@access public
*@return boolean
*@param integer Gallery id
*/
function gallery_dtl($id_gal){

if ($this->query("select id_gal, dir_gal, name_gal, txt_desc, img_front, cod_lang from ".$this->prefix."_gallery where id_gal=$id_gal;"))
    { if ($this->num_rows()==0) {$this->txt_error=""._NO_GALS.""; return false;}
      $this->next_record();
      return true;
    }
    else { $this->txt_error=""._ER_SELET_TBL." GAL"; return false;}
}
/**
*Update gallery fields of the table prefix_gallery
*@author Josep Marxuach
*@access public
*@param integer Gallery id
*@param array Assoc array[fieldname]=value
*@return boolean
*/
function update_gallery($id, $fields){

if (!is_array($fields)) return false;
if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

reset($fields);
$st_field="";
while (list($key,$value)=each($fields))
    {
    if ($st_field!="") $st_field=$st_field.", ";
    $st_field=$st_field.$key."=".$value;
    }

if ($this->query("update ".$this->prefix."_gallery set $st_field where id_gal=$id"))
     { $this->txt_error=""._UPDATED.""; return true;}
     else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
}
/**
*Check gallery fields before any add or update to the table prefix_gallery
*@author Josep Marxuach
*@access public
*@param array Assoc array[fieldname]=value
*@return boolean
*/
function prepare_fields(&$fields){
reset($fields);
while (list($key,$value)=each($fields))
   {
    switch($key)
         {
         case "name_gal":
         if (strlen($value)==0) {$this->txt_error=""._IN_NAME_GAL."";return false;}
         $fields[$key]="'".addslashes($value)."'";
         break;
         case "txt_desc":
         $fields[$key]="'".addslashes($value)."'";
         break;
         case "dir_gal":
         if (isset($value) && $value!="") $fields[$key]="'$value'";
            else {$this->txt_error=""._ERROR_DIR_GAL."";return false;}
         break;
         case "img_front":
         if (isset($value) && $value!="") $fields[$key]="'$value'";
            else $fields[$key]="NULL";
         break;
         case "cod_lang":
         if (strlen($value)==0) $fields[$key]="null"; else
             $fields[$key]="'$value'";
         break;
         case "tp_gal":
         break;
         Default;
         unset($fields[$key]);
         break;
         }

   }
return true;
}

/******************************** END CLASS ***********************************************/
}


?>
