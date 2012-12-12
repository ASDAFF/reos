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
*Returns xml node for search immos by criterias.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/isearch.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}


require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_ORDER',_IDIOMA, TRUE);
$lovs->getLovs('_LST_PRICE',_IDIOMA, TRUE,"#");

global $keywords;
global $id_org_session;
global $nm;

$out="";

$immo= new immo;

 $form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
 //$form->num_cols=2;
 $form->add_textbox("ref_immo",""._REF_IMMO.":",10,10);

 $form->add_static_listbox("order_by",""._ORDERBY.":","precio;"._PRECIO.",ref_immo;"._REF_IMMO.",txt_poblacion;"._TXT_POBLACION.",tp_propiedad;"._TP_PROPIEDAD."");
 $form->add_static_listbox("tp_propiedad",""._TP_PROPIEDAD.":",";"._ANY.",".$form->convert(""._LST_TP_PROPIEDAD.""));
 $form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");

 $form->add_static_listbox("tp_servicio",""._TP_SERVICIO.":",";"._ANY.",".$form->convert(""._LST_TP_SERVICIO.""));

$result=$immo->array_pob_zona($id_org_session,true);
// $result=$immo->array_prov_pob_zona(19, false);

$this->onload.="initDynamicOptionLists();"; $this->file_script="jscripts/dol.js";

$form->add_link_2listbox(""._TXT_POBLACION.":",""._TXT_ZONA.":","txt_poblacion","txt_zona",$result,""._ANY."");
// $form->add_link_3listbox(_PROV_NAME.":",_TXT_POBLACION.":",_TXT_ZONA.":","txt_provincia","txt_poblacion","txt_zona",$result,_ANY);
 $form->add_static_listbox("precio_min",_PRICE_MIN.":",_LST_PRICE,"#");

 $form->add_static_listbox("precio_max",_PRICE_MAX.":",_LST_PRICE,"#");

 $form->add_hidden("data");
 $form->fields["order_by"]->col=2;
 $form->fields["in_order"]->col=2;

if (!$form->process()){
 if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields))
                         if ($key!="tp_state" && $key!="dt_create") $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=verimmo&nm="._RESULTS."&show=0&from=0");
 }

$out.=$form->draw();

$this->html_out .= "<immosearch>\n"."<title>"._FIND_BY_CRITERIA."</title>".$out."</immosearch>";


?>
