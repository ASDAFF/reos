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
 * Generates php files with language application labels.
 * Returns html into var html_out
 * @package blocks_admin
 */

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/genlang.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $lst_idiomas;

$err=false;

$php_lang_file="lang";

if ($this->query("select distinct cod_lang from ".$this->prefix."_labels;"))
$cod_langs=$this->select_array();else {$err=true;$this->add_msg(_ER_SELET_TBL." labels");}

if (!$err) {
	foreach ($cod_langs as $tmp) {

		$lg = $tmp["cod_lang"];

		if ($this->query("select * from ".$this->prefix."_labels where cod_lang='$lg';"))
		$labels=$this->select_array();else {$err=true;$this->add_msg(_ER_SELET_TBL." labels");}

		if ($handle = fopen(realpath(_DirLANGS)."/".$php_lang_file."_".$lg."_adm.php", "w")){

			//**************File header**************************************
			$str="<?php
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
\n\n//File generated from a txt file at : ".date("r")."\n\n";
			fwrite($handle,$str);

			foreach ($labels as $value) {
				$tmp = addcslashes($value["txt_label"],"\"");
				$tmp = str_replace("\\\".","\".",$tmp);
				$tmp = str_replace(".\\\"",".\"",$tmp);
				$str="define(\"".$value["id_name"]."\",\"".$tmp."\");\n";
				fwrite($handle,$str);
			}
			$str="?>";
			fwrite($handle,$str);
			fclose($handle);
		}else {$err=true;$this->add_msg(_ERROR_OPEN_FILE." $lg");} //end IF file open
	}//end FOR
}// end if no error


$this->html_out .= $this->pgtitle(_GEN_LANG,true, null);
$this->html_out.="<div class=\"comment\">";
if ($err) $this->html_out.=_ERROR_PROCESS; else $this->html_out.=_PROCESS_OK;
$this->html_out.="</div>";
?>
