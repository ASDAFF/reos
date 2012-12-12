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
 *Returns xml node for one immo.
 *Creates an advert from properties, intro, etc...
 *@package blocks_public
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/immo.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."lists/adverts.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_OBSERV',_IDIOMA);
$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);
$lovs->getLovs('_LST_INTRO',_IDIOMA);
$lovs->getLovs('_LST_ACTIVITIES',_IDIOMA);
$lovs->getLovs('_LST_PROPERTIES',_IDIOMA);
$lovs->getLovs('_LST_PROPERORD',_IDIOMA);
$lovs->getLovs('_LST_EQUIP',_IDIOMA);
$lovs->getLovs('_LST_SERVICES',_IDIOMA);
$lovs->getLovs('_LST_PISCINA',_IDIOMA);

global $id_immo;
global $dir_gal;
global $ind_link;
global $ind_href;
global $fname;

if (array_key_exists("uid",$this->auth))
$id_account=$this->auth["uid"];


$dbi= new DB_Sql;

if (isset($id_immo)) {
	$dbi->query("select ".$this->prefix."_immos.*, dir_gal,tp_servicio as tp_serv, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ELT(ind_piscina,"._LST_PISCINA.") as ind_piscina,ELT(tp_price,"._LST_TP_PRICE.") as tp_price, img_front as imgfrt, txt_address1, txt_comment from ".$this->prefix."_immos"
	." LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_immos.id_gal=".$this->prefix."_gallery.id_gal where ".$this->prefix."_immos.id_immo=$id_immo;");
	$dbi->next_record();
	$dir_gal=$dbi->Record["dir_gal"];
	$dir_gal=_DirGALLERIES."$dir_gal";


	$tit_pag=""._IMMO."";
	if(!isset($id_account)) {
		if (isset($dbi->Record["precio"]) && ($dbi->Record["tp_serv"]==1 || $dbi->Record["tp_serv"]==4)) {
			$link[0]["href"] = "pg=hipotec&precio=".$dbi->Record["precio"]."&ref_immo=".$dbi->Record["ref_immo"]."&nm="._CALC_INM."";
			$link[0]["txt"]=""._CALC_INM."";
		}
		
		if ($dbi->Record["tp_serv"]==3){
			$link[1]["href"] = "pg=reservation&id_immo=".$dbi->Record["id_immo"]."&nm="._RESERVATION;
			$link[1]["txt"]=_RESERVATION;
			
		}
		
		$link[2]["href"] = "pg=sinfo&ref_immo=".$dbi->Record["ref_immo"]."&nm="._TOY_INTERES."";
		$link[2]["txt"]=""._TOY_INTERES."";

		$link[3]["txt"]=""._SEE2PRINT."";
		$link[3]["print"]=true;
	} else {
		$link[4]["href"] = "pg=edtimmo&id_immo=".$dbi->Record["id_immo"]."&nm="._EDT_IMMO;
		$link[4]["txt"]=_EDT_IMMO;
	}

	//$link[1]["href"] = "pg=immo&print=1&id_immo=$id_immo";$link[1]["txt"]=""._SEE2PRINT."";$link[1]["popup"]="print.php?";
	$this->html_out .= $this->pgtitle($tit_pag,true,$link);

	eval("\$lst_properties=array("._LST_PROPERTIES.");");
	eval("\$lst_intro=array("._LST_INTRO.");");
	eval("\$lst_properord=array("._LST_PROPERORD.");");
	$this->html_out .="<immo>";

	$adv = new adverts;
	$this->html_out .=$adv->create_advert($dbi->Record,null,$lst_properties,$lst_intro,$lst_properord);



	if (isset($dbi->Record["int_capacity"])) {
		$this->html_out .="<capacity title=\""._INT_CAPACITY."\">";
		$this->html_out .=$dbi->Record["int_capacity"];
		$this->html_out .="</capacity>";
	}

	if (isset($dbi->Record["set_equip"])) {
		$this->html_out .="<equip title=\""._SET_EQUIP."\">";
		$this->html_out .=$adv->get_immo_set(""._LST_EQUIP."",$dbi->Record["set_equip"]);
		$this->html_out .="</equip>";
	}
	if (isset($dbi->Record["set_services"])){
		$this->html_out .="<services title=\""._SET_SERVICES."\">";
		$this->html_out .=$adv->get_immo_set(""._LST_SERVICES."",$dbi->Record["set_services"]);
		$this->html_out .="</services>";
	}

	if (isset($dbi->Record["set_activities"])) {
		$this->html_out .="<activities title=\""._SET_ACTIVITIES."\">";
		$this->html_out .=$adv->get_immo_set(""._LST_ACTIVITIES."",$dbi->Record["set_activities"]);
		$this->html_out .="</activities>";
	}
	if (isset($dbi->Record["set_observ"])) {
		$this->html_out .="<observ title=\""._SET_OBSERV."\">";
		$this->html_out .=$adv->get_immo_set(""._LST_OBSERV."",$dbi->Record["set_observ"]);
		$this->html_out .="</observ>";
	}
	 
	if (isset($dbi->Record["txt_address1"])) {
		$this->html_out .="<txt_address title=\""._TXT_ADDRESS1."\">";
		$this->html_out .=$dbi->Record["txt_address1"];
		$this->html_out .="</txt_address>";
	}
	
	if (isset($dbi->Record["txt_cp"])) {
		$this->html_out .="<txt_cp title=\""._TXT_CP."\">";
		$this->html_out .=$dbi->Record["txt_cp"];
		$this->html_out .="</txt_cp>";
	}
	
	if (isset($dbi->Record["txt_zona"])) {
		$this->html_out .="<txt_zone title=\""._TXT_ZONA."\">";
		$this->html_out .=$dbi->Record["txt_zona"];
		$this->html_out .="</txt_zone>";
	}
	 
	if (isset($dbi->Record["txt_comment"])) {
		$this->html_out .="<txt_comment title=\""._TXT_COMMENT."\">";
		$this->html_out .=$dbi->Record["txt_comment"];
		$this->html_out .="</txt_comment>";
	}

	$this->html_out .="</immo>";
	if (!isset($fname)) $fname=$dbi->Record["imgfrt"];
}
/** Setting page title **/
$this->pagetitle = $dbi->Record["tp_propiedad"]." ".$dbi->Record["tp_servicio"]." ".$dbi->Record["txt_poblacion"]." ".$dbi->Record["txt_zona"];
?>