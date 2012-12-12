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

/************************************************************************/
/*                                                                      */
/* Copyright (c) 2005 IT eLazos S.L.                                    */
/*                                                                      */
/************************************************************************/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_visits.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_org.php");

class visits extends org {
var $txt_error;
var $cod_lang;


/********************************************************************************************/
/* Returns the Visit's book
/* @author Jordi Mart�                                                                      */
/********************************************************************************************/
function visits_home( $id_org, $from, $offset ) {

  $join = "".$this->prefix."_visits v LEFT JOIN ".$this->prefix."_country AS c ON v.id_country = c.id_country LEFT JOIN ".$this->prefix."_opinion AS o ON v.id_opinion = o.id_opinion AND o.cod_lang = '".$this->cod_lang."'";
  $where=" id_org=$id_org";
  if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

  if( $this->Query("SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(dt_visits,"._DATETIME_SQL.") fecha_ft FROM $join WHERE $where ORDER BY dt_visits DESC $limit;" ) )
    { if ($this->num_rows()==0) { $this->txt_error=""._NO_VISITS.""; return false;} else $this->txt_error="";
      return true;
    }
    else { $this->txt_error=""._ER_SELET_TBL." VISITS"; return false;}
}

/**
*Returns all countries.
*@author Jordi Mart�
*/
function country_lst(){
if ($this->query("SELECT ID_COUNTRY, COUNTRY_NAME FROM ".$this->prefix."_country"))
     {if ($this->num_rows()==0) return false;
         else {
         $prfs=$this->select_array();
         foreach($prfs as $value) $result[$value["ID_COUNTRY"]]=$value["COUNTRY_NAME"];
         return $result;
         }
     }
    else { $this->txt_error=""._ER_SELET_TBL." COUNTRY"; return false;}
}

/**
*Returns all opinions.
*@author Jordi Mart�
*/
function opinion_lst(){
if ($this->query("SELECT ID_OPINION, NAME_OPIN FROM ".$this->prefix."_opinion WHERE cod_lang = '".$this->cod_lang."'"))
     {if ($this->num_rows()==0) return false;
         else {
         $prfs=$this->select_array();
         foreach($prfs as $value) $result[$value["ID_OPINION"]]=$value["NAME_OPIN"];
         return $result;
         }
     }
    else { $this->txt_error=""._ER_SELET_TBL." OPINION"; return false;}
}

/********************************************************************************************/
/* Adds visit                                                                               */
/* @author Jordi Mart�                                                                      */
/********************************************************************************************/
function add_visit( $id_org, $fields ){

if (!is_array($fields)) return false;
if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

reset($fields);
$st_field="";
$st_value="";
while (list($key,$value)=each($fields)) {
    if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
    $st_field=$st_field.$key;
    $st_value=$st_value.$value;
}

$dvis = date("Y-m-d H:i:s" );
if ($this->query("insert into ".$this->prefix."_visits ( id_org, dt_visits, $st_field ) values ( $id_org, '$dvis', $st_value );"))
    {$this->txt_error=""._INSERTED."";return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
}

/**
*Replaces smilie codes by images in the text of the message
*@author Jordi Mart�
*@access private
*@return string
*/
function prepare_message( $content ){

  $simbols = array( '[:)]', '[;)]', '[:o]', '[:D]', '[:errr:]', '[:(]', '[:x]', '[:o)]', '[:oops:]', '[:star:]', '[xx(]', '[|)]', '[:V:]', '[:^:]', '[}:)]', '[8D]' );

  $i = 1;
  while( $simbol = each($simbols)){
    $img = _TPLDIR._THEME_DIR."/"._DirIMAGES."smiley$i.gif";
    $str_new = "<img src=\"$img\" border=\"0\" />";
    $content = str_replace( $simbol[1], $str_new, $content );
    $i++;
  }
  
  return $content;
}

/**
*Checks perm fields before any add or update to the table prefix_perms
*@author Jordi Mart�
*@access private
*@param array Assoc array[fieldname]=value
*@return boolean
*/
function prepare_fields(&$fields){
reset($fields);
while (list($key,$value)=each($fields))
  {
  switch($key)
    {
    case "txt_name":
      if (strlen($value)<3) { $this->txt_error=""._ERROR_NAME_USER.""; return false;}
      $fields[$key]="'$value'";
      break;
    case "txt_web":
      $value = trim( $value );
      if( $value=="" ) { unset($fields[$key]);break; }
      // borrem "http://" inicial i "/" finals per si la sintaxis �s incorrecte
      $p = strpos( $value, "http" );
      if( $p === false ) {}     /// === nom�s em funciona b� amb 'false'
      else
      {
        // borrem "http"
        $value = substr( $value, $p+4 );
        // borrem ":" i "/" del principi
        $val2 = substr( $value, 0, 1 );
        while( $val2==":" or $val2=="/" ) {
          $value = substr( $value, 1 );
          $val2 = substr( $value, 0, 1 );
        }
        // borrem "/" del final
        $val2 = substr( $value, strlen($value)-1 );
        while( $val2=="/" ) {
          $value = substr( $value, 0, strlen($value)-1 );
          $val2 = substr( $value, strlen($value)-1 );
        }
      }
      // afegir:   http://.../
      $value = "http://$value/";
      $fields[$key]="'$value'";
      break;
    case "txt_email":
      $fields[$key]="'$value'";
      break;
    case "txt_age":
      $fields[$key]="'$value'";
      break;
    case "txt_poblacion":
      $fields[$key]="'$value'";
      break;
    case "id_country":
      break;
    case "txt_como":
      $fields[$key]="'$value'";
      break;
    case "txt_content":
      if (strlen($value)==0) { $this->txt_error=""._ERROR_TXT_CONTENT.""; return false;}
      $fields[$key]="'$value'";
      break;
    case "id_opinion":
      break;
    default:
      unset($fields[$key]);
      break;
    }
  }
return true;
}


//*************************** FIN CLASE ***********************************
}






?>
