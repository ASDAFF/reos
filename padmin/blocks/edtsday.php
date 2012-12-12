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
*Edita el intervalo de dias de una temporada de alquiler de vacaciones.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtsday.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_himmo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SDAYS',_IDIOMA);

global $id_sdays;
global $id_seasons;
global $name_seasons;
global $fid;


$dbi= new himmo;

if (isset($id_sdays)) {
    if ($dbi->dtl_sday($id_sdays)) extract($dbi->Record);
    $this->add_msg($dbi->txt_error);
    }

$this->html_out .= $this->pgtitle($name_seasons,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_static_listbox("tp_sdays",""._TP_SDAYS.":",$form->convert(""._LST_TP_SDAYS.""));
$form->add_datebox( "dt_start", ""._DT_START.":", 8, 10 );
$form->add_datebox( "dt_end", ""._DT_END.":", 8, 10 );
$form->add_textbox( "precio", ""._PRECIO.":", 6,6 );

$form->add_hidden("data");

$processed = $form->process();

if (!$processed) {

if (isset($tp_sdays)) $form->fields["tp_sdays"]->value = $tp_sdays;
if (isset($dt_start)) $form->fields["dt_start"]->value = $dt_start;
if (isset($dt_end)) $form->fields["dt_end"]->value = $dt_end;
if (isset($precio)) $form->fields["precio"]->value = number_format($precio,0,",",".");

if (isset($id_sdays)) $pagina="fid=$id_sdays&id_seasons=$id_seasons&name_seasons=$name_seasons";
                       else $pagina="id_seasons=$id_seasons&name_seasons=$name_seasons";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtsday&$pagina");
$this->html_out .=$form->draw();
}
else
{
// Processar el form
reset($form->fields);
while (list($key,$value)=each($form->fields)){
    if ($key!=="data") {$fields[$key]=$value->value;}
    }
    
if (isset($fid)){
   if ($dbi->update_vals($fid,$fields,"sdays"))
      {
      $this->redirect();
      } else { $this->add_msg($dbi->txt_error); $this->html_out .=$form->draw();}
}else{
   if ($dbi->add_vals($id_seasons, $fields,"sdays"))
      {
       $this->redirect();
      } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
 }
}

if ($dbi->days_season($id_seasons)){
       $this->print_list($dbi->select_array(), 0,20,"","pg=edtsday&id_sdays=",null,null,$dbi->found_rows());
       }
        else $this->add_msg($dbi->txt_error);


?>
