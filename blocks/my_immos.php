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
*Returns xml node for list of immos with search form.
*Search form is shown if var show is Null or 1. if show=0 no search form.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/my_immos.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_immo.php");
if (!class_exists("xmlform")) require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."lists/adverts.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_TP_STATE',_IDIOMA);


global $id_org_session;
global $keywords;
global $nm;
global $from;

if (!isset($from)) $from=0;

$immo= new immo;

$id_account=$this->auth["uid"];

if (!isset($nm)) $nm=_MY_IMMOS;

$link[0]["href"]="pg=edtimmo&id_account=$id_account&view=add&nm="._ADD_IMMO;
$link[0]["txt"]=_ADD_IMMO;

$this->html_out .= $this->pgtitle($nm,true,$link);

$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->add_static_listbox("tp_state",""._TP_STATE.":",";"._ANY.",".$form->convert(""._LST_TP_STATE.""));
$form->add_static_listbox("tp_servicio",""._TP_SERVICIO.":",";"._ANY.",".$form->convert(""._LST_TP_SERVICIO.""));
$form->add_static_listbox("tp_propiedad",""._TP_PROPIEDAD.":",";"._ANY.",".$form->convert(""._LST_TP_PROPIEDAD.""));
$form->add_static_listbox("order_by",""._ORDERBY.":","precio;"._PRECIO.",ref_immo;"._REF_IMMO.",txt_poblacion;"._TXT_POBLACION.",tp_propiedad;"._TP_PROPIEDAD."");
$form->add_hidden("data");

if (!$form->process()){
 if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields))
                             $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=my_immos&nm="._MY_IMMOS."&from=0");
  
} else {

   reset($form->fields);
   $keyword="";
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data" && $value->value!="") {
            $fields[$key]=$value->value;
            if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
            }
       }
}

$this->html_out.="<my_immos>";
$this->html_out.=$form->draw();

if (isset($fields)) {
  if ($immo->ver_immos($fields,$id_org_session,NULL, "All",$from,10,NULL,$id_account)){
  	 $founds = $immo->found_rows();	
  	 $adv = new adverts;
     $this->html_out.= $adv->print_advert($immo->select_array(), $from,10,"","pg=immo&id_immo=",null,"pg=my_immos&keywords=$keywords&nm=$nm", $founds);
   } else $this->add_msg($immo->txt_error);
 }
$this->html_out.="</my_immos>";

?>
