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
 * List of values.
 * Returns html into var html_out
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/versets.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $table;
global $nm;
global $list_del;

if ($this->query("select SQL_CALC_FOUND_ROWS txt_code, txt_code cod, cod_lang, txt_value value, num_order from ".$this->prefix."_lists where id_name='$table' and cod_lang='"._IDIOMA."' ORDER BY num_order;"))
{
	$found=$this->found_rows();
	if ($found<64) { $link_add[0]["href"]="pg=edtlst&id_name=$table&view=Add";
	$link_add[0]["txt"]=""._ADD_VAL."";
	} else $link_add=NULL;

	$this->html_out .= $this->pgtitle($nm,true, $link_add);

	$this->print_list($this->select_array(), 0,64,"","pg=edtlst&id_name=$table&txt_code=",NULL,"table=$table&nm=$nm",$found);

} else $this->add_msg(""._ERROR_DATS."");

?>
