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
 * Devuelve clientes en formato xml.
 * Sirve para la sincronizaci�n con outlook.
 * Outlook coje los datos de clientes haciendo una llamada a la web recogiendo los datos en formato xml.
 * Returns xml into var html_out
 * @package blocks_admin
 */


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/xcontact.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_account.php");

global $keywords;
global $view;
global $from;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

$account= new account;

if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                       while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                      }
                      
if ($account->ver_accs($fields,$id_org,$id_position, $view,$from,null,false,true)){
    $this->html_out .=$account->select_xml();
    }



?>

