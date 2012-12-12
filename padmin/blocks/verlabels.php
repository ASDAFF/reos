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
*Lista de literales del programa.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verlabels.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

if ($this->query("select SQL_CALC_FOUND_ROWS id_name , id_name as txt_title, cod_lang, txt_label value from ".$this->prefix."_labels where cod_lang = '"._IDIOMA."' ORDER BY txt_label;"))
    {
    $found=$this->found_rows();
    $link_add[0]["href"]="pg=edtlabel&view=Add";
    $link_add[0]["txt"]=""._ADD_VAL."";
    $link_add[1]["href"]="pg=genlang&nm="._GEN_LANG;
    $link_add[1]["txt"]=_GEN_LANG;
                      

    $this->html_out .= $this->pgtitle(_VERLABELS,true, $link_add);

    $this->print_list($this->select_array(), 0,10000,"","pg=edtlabel&id_name=",null,"",$found);
    }
    else $this->add_msg(""._ERROR_DATS."");

?>
