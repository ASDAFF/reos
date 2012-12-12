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
*Edita los datos de un ticket.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtticket.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_support.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TK_PRIORITY',_IDIOMA);
$lovs->getLovs('_LST_TK_STATE',_IDIOMA);

global $id_ticket;
global $fid;
global $perm_peso;
global $lst_idiomas;
global $lst_screens;

$id_org_session=$this->auth["id_org"];
$id_pos_session=$this->auth["id_position"];
$user_id = $this->auth["uid"];

if (isset($id_ticket)|| isset($fid)) $tit_pag=""._EDTTICKET.""; else $tit_pag=""._ADD_TICKET."";

$dbi= new support;

if (isset($id_ticket)) { $dbi->dtl_ticket($id_ticket);
                          $this->add_msg($dbi->txt_error);
                          extract($dbi->Record);
                          $link[2]["txt"]=""._SEE2PRINT."";
                          $link[2]["print"]=true;  
                          $dbi->lst_tk_msg($id_ticket);                         
                                                 
                        } else $link=false;


$idiomas="";
reset($lst_idiomas);
while (list ($key, $val) = each ($lst_idiomas)){
   $idiomas=$idiomas.",$key;$val";
  }
$this->html_out .= $this->pgtitle($tit_pag,true,$link);

if (isset($id_ticket)) {
	$msg = $dbi->select_array();
	foreach($msg as $value) {
		$this->html_out .="<table class=\"lista\"><tr class=\"linea0\">\n";
		if ($value["fg_private"]=="1") $private=_FG_PRIVATE;else $private="";
		$this->html_out .= "<td class=\"lista\" style=\"vertical-align:top;width:150px\">".$value["dt_create"]." - ".$value["time"]."<br/>".$value["username"]."<br/>$private</td>\n";
		$this->html_out .= "<td class=\"lista\" style=\"vertical-align:top\">".$value["txt_msg"]."</td>\n";
		$this->html_out .="</tr></table>\n";
	}
	
}

$this->file_script ="jscripts/tiny_mce/tiny_mce_src.js";
$this->onload ="tinyMCE.init({" 
	."mode : 'exact', "
	."elements : 'txt_msg', "
	."theme : 'advanced', " 
	."plugins : 'table', "
	."theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor', "
	."theme_advanced_buttons3 : 'tablecontrols',"
	."theme_advanced_toolbar_location : 'top', "
	."theme_advanced_toolbar_align : 'left'"
	."});";	
	
	
$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");
$form->num_cols=2;
$form->title=_DTL;

$form->add_textbox("ref_ticket",""._REF_TICKET.":",12,12);

$form->add_static_listbox( "cod_lang", ""._LANGUAGE.":",  $idiomas );

//$form->add_textbox( "nm_account", ""._NAME_ACCOUNT.":", 72, 72 );

$form->add_picker( "id_contact", _NAME_ACCOUNT.":", 0, 0, "picker.php?".$this->url_encrypt("pg=vercontacts&view=All&pk=id_contact&pos=1&from=0"),true  );

$form->add_static_listbox("tp_priority",""._PRIORITY.":",$form->convert(""._LST_TK_PRIORITY.""));

if ($perm_peso>=8) {
  $dbi->employees($id_org_session,1);
  $form->add_static_listbox("id_position",""._ID_POSITION.":",";,".$dbi->array2list($dbi->select_array(),","));
}
$form->add_text("tp_status",_TP_STATE, false);
$form->add_datebox("dt_create",""._DT_CREATE.":",8,10);
//$form->add_static_listbox("tp_status",""._TP_STATE.":",$form->convert(""._LST_TK_STATE.""));

//$form->add_textbox("txt_email",_TXT_EMAIL.":",60,60);

$dbi->lst_ctg();
$form->add_static_listbox("id_tk_ctg",""._NM_TK_CTG.":",$dbi->array2list($dbi->select_array(),","));

$form->add_textbox( "txt_subject", _SUBJECT.":", 90, 90 );

$form->add_checkbox("fg_private",_FG_PRIVATE.":","1","0");
$form->add_texteditor( "txt_msg",_MSG.":", 90, 30 );


$form->add_hidden("data");
$form->fields["cod_lang"]->col=2;
$form->fields["tp_status"]->col=2;
$form->fields["tp_priority"]->col=2;
$form->fields["id_tk_ctg"]->col=2;

$processed = $form->process();

if(!$processed) {

		if (isset($ref_ticket)) $form->fields["ref_ticket"]->value = $ref_ticket;
      		else $form->fields["ref_ticket"]->value=$dbi->GenerateTkRef();

      	if (isset($cod_lang)) $form->fields["cod_lang"]->value = $cod_lang;
            else $form->fields["cod_lang"]->value = ""._IDIOMA."";	

        if (isset($id_tk_ctg)) $form->fields["id_tk_ctg"]->value = $id_tk_ctg; 
           	
        //if (isset($nm_account)) $form->fields["nm_account"]->value = $nm_account;
        
        //if (isset($txt_email)) $form->fields["txt_email"]->value = $txt_email;
        if (isset($txt_subject)) $form->fields["txt_subject"]->value = $txt_subject;
        
        eval("\$tmp=array("._LST_TK_STATE.");");
        
        if (isset($tp_status)) $form->fields["tp_status"]->value = $tmp[int($tp_status-1)];
               else $form->fields["tp_status"]->value = $tmp[0];
        
        if (isset($tp_priority)) $form->fields["tp_priority"]->value = $tp_priority;
               else $form->fields["tp_priority"]->value = 3;
        if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
            else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");
        if (isset($fg_private)) $form->fields["fg_private"]->value = $fg_private; 
        	else $form->fields["fg_private"]->value = 0;   
        	
        if ($perm_peso>=8) {
        if (isset($id_position)) $form->fields["id_position"]->value = $id_position;
            else if (!isset($name)) $form->fields["id_position"]->value = $id_pos_session;
            }
		
        if (isset($id_ticket)) $pagina="&fid=$id_ticket";else  $pagina="";

        $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
        $this->html_out .=$form->draw();

} else {
	    
	    			        
        reset($form->fields);
        while (list($key,$value)=each($form->fields)){
        	  
              if ($key!=="data") {$fields[$key]=$value->value;}
              }

        if (isset($fid)){
                if ($dbi->update_ticket($fid,$user_id,$fields))
                {
                  $this->redirect();
                } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        } else
        {
            
        if ($txt_email=$dbi->add_ticket($id_org_session, $id_pos_session, $user_id,$fields)){
                $msgs=$dbi->GetMsgText();	
                if ($dbi->ctg_dtl($fields["id_tk_ctg"])){                	
                	$ctg=$dbi->Record;
                }
                $this->add_email($ctg["txt_email"],$ctg["txt_sign"],$txt_email, "[#".$fields["ref_ticket"]."] ".$fields["txt_subject"],"---startline---".$msgs[$fields["cod_lang"]]["open_text"]);	
                
                $this->redirect();
                
                //$this->add_msg($dbi->txt_error);
                } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
}
$this->add_msg($dbi->Error); //Add native DB error
//variables para modulos posteriores
if (isset($fid)) $id_ticket=$fid;
?>
