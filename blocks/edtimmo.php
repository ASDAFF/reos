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
 *Edita los datos de un immo.
 *Returns html into var html_out
 *@package blocks_public
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtimmo.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);
$lovs->getLovs('_LST_INTRO',_IDIOMA);
$lovs->getLovs('_LST_PROPERTIES',_IDIOMA);
$lovs->getLovs('_LST_TP_STATE',_IDIOMA);
$lovs->getLovs('_LST_TP_OFERTA',_IDIOMA);
$lovs->getLovs('_LST_PISCINA',_IDIOMA);

global $id_immo;
global $fid;
global $id_gal;

$id_org_session=$this->auth["id_org"];
//$id_pos_session=$this->auth["id_position"];
$id_pos_session="null";

$dbi= new immo;

if (isset($id_immo)) { $dbi->dtl_immo($id_immo);
$this->add_msg($dbi->txt_error);
extract($dbi->Record);
}
if (isset($id_immo) || isset($fid))
{
	$tit_pag=""._EDT_IMMO."";
	if (isset($id_immo)) {$link[0]["href"] = "pg=edtimmogal&id_immo=$id_immo&id_gal=$id_gal";$link[0]["txt"]=""._PICTURES."";}
	if (isset($tp_servicio) /*&& $tp_servicio==3*/) {$link[1]["href"] = "pg=edthimmo&id_immo=$id_immo";$link[1]["txt"]=""._EDTHIMMO."";}
	//$link[2]["txt"]=""._SEE2PRINT."";  $link[2]["print"]=true;
} else {$tit_pag=_ADD_IMMO;$link=NULL;}

$this->html_out .= $this->pgtitle($tit_pag,true,$link);

$form = new xmlform("form1",LK_HOME_ADM,"GET", _NEXT);

$form->title=""._DTL_PROP."";
$form->num_cols=2;

$form->add_textbox( "ref_immo", ""._REF_IMMO.":", 10, 10 );

$form->add_static_listbox("tp_servicio",""._TP_SERVICIO.":",$form->convert(""._LST_TP_SERVICIO.""));
$form->add_text("vtp_state",""._TP_STATE.":",0,0);
$form->add_static_listbox("tp_propiedad",""._TP_PROPIEDAD.":",$form->convert(""._LST_TP_PROPIEDAD.""));
$form->add_text("vind_oferta",""._IND_OFERTA.":",0,0);
$form->add_textbox( "num_dormitorios", ""._NUM_DORMITORIOS.":", 2, 2 );
$form->add_text("vdt_create",""._DT_CREATE.":",8,10);
$form->add_textbox( "num_wc", ""._NUM_WC.":", 2, 2 );
$form->add_text("vdt_valid",""._DT_VALID.":",8,10);
$form->add_static_listbox("ind_piscina",""._IND_PISCINA.":",$form->convert(""._LST_PISCINA.""));
$form->add_textbox( "precio", ""._PRECIO.":", 10, 7 );
$form->add_textbox( "int_superficie_const", ""._INT_SUPERFICIE_CONST.":", 2, 3 );
$form->add_static_listbox("tp_price",""._TP_PRICE.":",$form->convert(""._LST_TP_PRICE.""));
$form->add_textbox( "int_superficie", ""._INT_SUPERFICIE.":", 3, 4 );
$form->add_textbox( "int_terrace", ""._INT_TERRACE.":", 3, 4 );
$form->add_textbox( "num_parking", ""._NUM_PARKING.":", 2, 2 );
$form->add_static_checkbox("set_intro",""._SET_INTRO.":",$form->convert(""._LST_INTRO.""));
$form->add_static_checkbox("set_properties",""._SET_PROPERTIES.":",$form->convert(""._LST_PROPERTIES.""));


$form->add_textbox( "txt_address1", ""._TXT_ADDRESS1.":", 72, 72 );
/*$result=$dbi->array_pob_zona($id_org_session,true);
 $this->onload.="initDynamicOptionLists();";
 $this->file_script="jscripts/dol.js";
 $form->add_link_2listbox(""._TXT_POBLACION.":",""._TXT_ZONA.":","txt_poblacion","txt_zona",$result,""._SELECT."");
 $form->add_textbox( "add_zona", ""._ADD_ZONA.":", 35, 35 );
 */
$form->add_textbox( "txt_cp", ""._TXT_CP.":", 15, 15 );
$form->add_textarea( "txt_comment", _TXT_POBLACION." :<br/>"._TXT_ZONA." :<br/>"._TXT_COMMENT." :", 50, 2 );


$form->add_hidden("data");
$form->fields["vtp_state"]->col=2;
$form->fields["vind_oferta"]->col=2;
$form->fields["vdt_valid"]->col=2;
$form->fields["vdt_create"]->col=2;
$form->fields["precio"]->col=2;
$form->fields["tp_price"]->col=2;

$processed = $form->process();

eval("\$tmp=array("._LST_TP_STATE.");");

if (isset($tp_state)) $form->fields["vtp_state"]->value = $tmp[($tp_state-1)];
else $form->fields["vtp_state"]->value = $tmp[4];

eval("\$tmp=array("._LST_TP_OFERTA.");");
if (isset($ind_oferta)) $form->fields["vind_oferta"]->value = $tmp[($ind_oferta-1)];
else $form->fields["vind_oferta"]->value = $tmp[0];
unset($tmp);


if(!$processed) {

	if (isset($ref_immo)) $form->fields["ref_immo"]->value = $ref_immo;
	else $form->fields["ref_immo"]->value="P-".strtoupper(substr(md5(uniqid("")), 0, 4));

	if (isset($dt_create)) $form->fields["vdt_create"]->value=$dt_create;
	else $form->fields["vdt_create"]->value=date(""._DATE_FORMAT."");
	if (isset($dt_valid)) $form->fields["vdt_valid"]->value=$dt_valid;else $form->fields["vdt_valid"]->value=_UNTITLED;

	//else $form->fields["dt_valid"]->value=date(""._DATE_FORMAT."",strtotime(date("Ymd")) + (12 * 30 * 24 * 60 * 60));
	if (isset($tp_servicio)) $form->fields["tp_servicio"]->value = $tp_servicio;
	if (isset($tp_propiedad)) $form->fields["tp_propiedad"]->value = $tp_propiedad;
	if (isset($num_dormitorios)) $form->fields["num_dormitorios"]->value = $num_dormitorios;
	else  $form->fields["num_dormitorios"]->value = 0;
	if (isset($num_wc)) $form->fields["num_wc"]->value = $num_wc;
	else  $form->fields["num_wc"]->value = 0;
	if (isset($num_parking)) $form->fields["num_parking"]->value = $num_parking;
	else $form->fields["num_parking"]->value = 0;
	if (isset($int_superficie)) $form->fields["int_superficie"]->value = $int_superficie;
	else $form->fields["int_superficie"]->value = 0;
	if (isset($int_terrace)) $form->fields["int_terrace"]->value = $int_terrace;
	else $form->fields["int_terrace"]->value = 0;
	if (isset($int_superficie_const)) $form->fields["int_superficie_const"]->value = $int_superficie_const;
	else $form->fields["int_superficie_const"]->value = 0;
	if (isset($ind_piscina)) $form->fields["ind_piscina"]->value = $ind_piscina;
	else $form->fields["ind_piscina"]->value = 1;
	if (isset($precio)) $form->fields["precio"]->value = number_format($precio,0,",",".");
	if (isset($tp_price)) $form->fields["tp_price"]->value = $tp_price;
	if (isset($txt_address1)) $form->fields["txt_address1"]->value = $txt_address1;
	//if (isset($txt_poblacion)) $form->fields["txt_poblacion"]->value = $txt_poblacion;
	//if (isset($txt_zona)) $form->fields["txt_zona"]->value = $txt_zona;
	if (isset($txt_cp)) $form->fields["txt_cp"]->value = $txt_cp;
	if (isset($txt_comment)) $form->fields["txt_comment"]->value = $txt_comment;


	if (isset($set_intro)) { eval("\$tmp=array(".$set_intro.");");
	$form->fields["set_intro"]->value = $tmp;unset($tmp);}

	if (isset($set_properties)) { eval("\$tmp=array(".$set_properties.");");
	$form->fields["set_properties"]->value = $tmp;unset($tmp);}


	if (isset($id_immo)) $pagina="&fid=$id_immo";else  $pagina="";

	$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
	//print_r($form->fields);
	$this->html_out .=$form->draw();

}else {

	//if ($form->fields["add_zona"]->value!="")
	// $dbi->add_zona($form->fields["txt_poblacion"]->value,$form->fields["add_zona"]->value);

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}
	$fields["id_account"]=$this->auth["uid"];
	$fields["tp_state"]=5;
	 

	if (isset($fid)){
		if ($dbi->update_immo($fid,$fields)) {
			$this->redirect("id_immo=$fid");
		} else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	} else {
		if ($dbi->add_immo($id_org_session, $id_pos_session, $fields)){
			$this->redirect("id_immo=".$dbi->last_insert_id());
			//$this->add_msg($dbi->txt_error);
		} else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	}
}

//if (isset($id_gal)) include(_DirBLOCKS."thumb.php");

?>
