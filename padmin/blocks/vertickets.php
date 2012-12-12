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
*Lista de tickets con formulario de busqueda.
*Sirve para "Mis, Mi equipo, todos".
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/vertickets.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_support.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."charts/charts.php");

global $dlte;
global $keywords;
global $from;
global $view;
global $id_account;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

if (!isset($from)) $from=0;

$ticket= new support;

if (isset($dlte) && $dlte!="") {$ticket->delete_ticket($dlte);$this->add_msg($ticket->txt_error);}

if ($view=="My") $tit_pag=""._MY_TICKETS."";
if ($view=="Team") $tit_pag=""._TEAM_TICKETS."";
if ($view=="All") $tit_pag=""._ALL_TICKETS."";

$link_add[0]["href"]="pg=edtticket";
$link_add[0]["txt"]=""._ADD_TICKET."";

$this->html_out .= $this->pgtitle($tit_pag,false,$link_add);

$fields=null;$order_by=null;

if (!isset($id_account)){
$lnk_acc="";
} else {
$lnk_acc="&id_account=$id_account";
$view="All";
$fields=NULL;
$order_by="id_ticket ASC";
}

if ($ticket->ver_tickets($fields,$id_org,$id_position, $view,$from,20,$order_by,$id_account)){		
     $this->html_out .= $this->print_list($ticket->select_array(), $from,20,"","pg=edtmsg&id_ticket=",null,"view=$view&keywords=$keywords$lnk_acc", $ticket->found_rows());
    } else $this->add_msg($ticket->txt_error);

    
?>
