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
*Edita los datos de perfil de compra de un cliente/account.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtperfil.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_account.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_IND_ACTIVE',_IDIOMA);
$lovs->getLovs('_LST_NUMBERS_OP',_IDIOMA, TRUE);
$lovs->getLovs('_LST_PISCINA',_IDIOMA);
$lovs->getLovs('_LST_PRICE',_IDIOMA, TRUE, "#");

global $id_account;
global $view;
global $fid;

if (isset($id_account)|| isset($fid)) $tit_pag=""._VER_PERFIL.""; else $tit_pag=""._ADD_PERFIL."";

$dbi= new account;

if (isset($id_account)) { $dbi->dtl_accperfil($id_account);
                          $this->add_msg($dbi->txt_error);
                          extract($dbi->Record);
                          $link[0]["href"]="pg=verpobs&id_account=$id_account";
                          $link[0]["txt"]=""._ASIGN_POB."";
                          $pobs=$dbi->poblaciones($id_account);
                        } else $link=false;

$this->html_out .= $this->pgtitle($tit_pag,true,$link);

$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");
$form->title=""._DTL_ACCPERFIL."";
$form->add_text( "name_acc", ""._NAME_ACCOUNT.":", 50, 50 );
$form->add_datebox("dt_create",""._DT_CREATE.":",8,10);
$form->add_static_listbox("ind_active",""._IND_ACTIVE.":",$form->convert(""._LST_IND_ACTIVE.""));
$form->add_static_checkbox("tp_servicio",""._TP_SERVICIO.":",$form->convert(""._LST_TP_SERVICIO.""));
$form->add_static_checkbox("tp_propiedad",""._TP_PROPIEDAD.":",$form->convert(""._LST_TP_PROPIEDAD.""));
$form->add_static_listbox( "num_dormitorios", ""._NUM_DORMITORIOS.":", ""._LST_NUMBERS_OP."" );
$form->add_static_listbox( "num_wc", ""._NUM_WC.":", ""._LST_NUMBERS_OP."" );
$form->add_static_listbox("ind_piscina",""._IND_PISCINA.":",$form->convert(""._LST_PISCINA.""));
$form->add_static_listbox( "precio_compra", _PRECIO_COMPRA.":", _LST_PRICE, "#" );
$form->add_textbox( "precio_alquiler", ""._PRECIO_ALQUILER.":", 10, 10 );
//$form->add_text( "pobs", ""._PREF_POBS.":", 50, 50 );
$form->add_text( "pobs", _PREF_POBS.":", 50, 50 );

$form->add_textarea( "txt_comment", ""._TXT_COMMENT.":", 50, 2 );

$form->add_hidden("data");

$processed = $form->process();

if(!$processed) {

        if (isset($name_account)) $form->fields["name_acc"]->value = $name_account;
        if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
            else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");
        if (isset($ind_active)) $form->fields["ind_active"]->value = $ind_active;
              else $form->fields["ind_active"]->value = 1;
        if (isset($precio_compra)) $form->fields["precio_compra"]->value = $precio_compra;
        if (isset($precio_alquiler)) $form->fields["precio_alquiler"]->value = number_format($precio_alquiler,0,",",".");

        if (isset($pobs)) $form->fields["pobs"]->value = $pobs;
        if (isset($tp_propiedad)) { eval("\$tmp=array(".$tp_propiedad.");");
            $form->fields["tp_propiedad"]->value = $tmp;unset($tmp);}
        if (isset($tp_servicio)) { eval("\$tmp=array(".$tp_servicio.");");
            $form->fields["tp_servicio"]->value = $tmp;unset($tmp);}
        if (isset($num_dormitorios)) $form->fields["num_dormitorios"]->value = $num_dormitorios;
        if (isset($num_wc)) $form->fields["num_wc"]->value = $num_wc;
        if (isset($ind_piscina)) $form->fields["ind_piscina"]->value = $ind_piscina;
        if (isset($txt_comment)) $form->fields["txt_comment"]->value = $txt_comment;

        if (isset($id_account)) $pagina="&fid=$id_account";else  $pagina="";
        if (isset($view)) $pagina.="&view=$view";
        $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
        $this->html_out .=$form->draw();

} else
{

        reset($form->fields);
        while (list($key,$value)=each($form->fields)){
              if ($key!=="data") {$fields[$key]=$value->value;}
              }
        if (!isset($view)){
             if ($dbi->update_account($fid,$fields,"_acc_perfil"))
                {
                  $this->redirect();
                } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        } else
        {
        if ($dbi->add_profile($fid, $fields))
                {
                $this->redirect();
                //$this->add_msg($dbi->txt_error);
                } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
}

//variables para modulos posteriores
if (isset($fid)) $id_account=$fid;

?>
