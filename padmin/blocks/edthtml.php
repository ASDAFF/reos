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
*Edita el contenido de un fichero xml/html.
*El fichero se usa como contenido para la web publica.
*El fichero debe estar en el directorio tpl/template_name/html
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edthtml.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");

global $file;
global $lst_idiomas;
global $charset;


$this->html_out .= $this->pgtitle(_EDTHTML." - ".$file,true,Null);

$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");
$form->num_cols=0;
$form->add_textarea( "txt", "texto:", 70, 25 );


$form->add_hidden("data");

$form->fields["data"]->value = $this->txt_encrypt("pg=edthtml&file=$file");

$processed = $form->process();

if($processed ) {

reset($form->fields);

while (list($key,$value)=each($form->fields)){
   if ($key!=="data") {$fields[$key]=$value->value;}
   }

if (is_writable(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file) && ($fp = fopen(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file, "w"))) {
        fwrite($fp, $fields["txt"]);
        fclose($fp);
        }

if (file_exists(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file))
       $form->fields["txt"]->value = file_get_contents(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file);

$this->html_out .=$form->draw();

} else {

if (file_exists(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file))
       $form->fields["txt"]->value = file_get_contents(_TPLDIR._THEME_DIR."/"._DirHTMLS.$file);

$this->html_out .=$form->draw();
}



?>
