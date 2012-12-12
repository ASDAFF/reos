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
 * XML List class.
 * Returns a xml table list/grid from an array of columns.
 */
/**
 * Class Utils file.
 * Class Session extends class Utils.
 */
require_once(_DirINCLUDES."class_utils.php");
/**
 * Returns a xml table list/grid from an array of columns.
 *
 */
class xmllist extends utils {
	
	var $html_out = "";

	/**
	 * Returns a xml table list/grid from an array of columns.
	 * The array layout is list[row_num][fieldname]=fieldvalue and List[0][field_name]=fieldname.
	 * Includes page information like found items, current page number, next and previous links.
	 * Returns xml into the class var html_out.
	 * Node structure <list><found/><lst_info><pags><pag href=""><next href=""><prev href="">...
	 * @author Josep Marxuach
	 * @access public
	 * @param array   array[row_num][fieldname]=fieldvalue
	 * @param integer initial row number within the sql query, needed to link with next & previous page
	 * @param integer Number of rows to show
	 * @param string current page name for links (Not used, Pending to delete)
	 * @param string edit page name & parameter for links Ex: "pg=edtpage&id="
	 * @param string Parameter to delete row Ex: "id_to_del="
	 * @param integer Variables to pass through Ex : "view=$view&id_account=$id_account"
	 */
	function xml_list( $list, $from, $num_rows, $pagina, $edt_pagina, $borrar, $vars,$found_rows=Null) {

		$next=$from+$num_rows;
		$previous=$from-$num_rows;
		$total=count($list)-1;
		$col_num=count($list[0])-1;

		if (isset($borrar)) $col_num++;

		//if (isset($this->vars["pg"])) $pagina="pg=".$this->vars["pg"]; else $pagina="";

		$this->html_out .= "<list>";
		if (isset($found_rows)) $this->html_out .= "<found>"._FOUND." $found_rows "._ROWS."</found>";

		$this->html_out .="<lst_info>";
		$pages_lnks="";
		$current=1;
		if($found_rows>$num_rows){
			$pages_lnks .= "<pags>";
			$z=0;
			for ($i=0;$i<$found_rows;$i=$i+$num_rows){
				if ($from>=($i-($num_rows*5)) && $from<=($i+($num_rows*5))){
					//if ($z>0) $pages_lnks .="-";
					if ($from==$i) {$current=($i/$num_rows+1);$pages_lnks .= "<pag>".($i/$num_rows+1)."</pag>";}
					else $pages_lnks .= "<pag href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$i&$vars")."\">".($i/$num_rows+1)."</pag> ";
					$z++;
				}
			}


			if ($from>0) $pages_lnks .= "<prev href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$previous&$vars")."\">"._PREVIOUS."</prev>";
			//if ($from>0 && $found_rows>$next) $pages_lnks .=" - ";
			if ($found_rows>$next) $pages_lnks .= "<next href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$next&$vars")."\">"._NEXT."</next>";
			$pages_lnks .= "</pags>";

			$this->html_out .= $pages_lnks;
		}
		$this->html_out .= "<label>"._PAGS."</label><current>$current</current><total>"._OF." ".ceil($found_rows/$num_rows)."</total>";
		$this->html_out .="</lst_info>";

		$this->html_out .="<list_items>";

		$z=false; // sirve para controla que la 1a columna no se visualize
		$field_keys=array_keys($list[0]);
		$this->html_out .="<cols>";
		while (list($key,$value)=each($field_keys)){
			if ($z) $this->html_out .= "<$value>".constant(strtoupper("_$value"))."</$value>";
			else $z=true;
		}
		$this->html_out .="</cols>";

		$i=0;
		while ($i<=$total)
		{
			$this->html_out .="<row>";

			$ln=$i % 2;


			$z=false;
			$tmp="";
			foreach($list[$i] as $key=>$value)
			if ($z) $tmp .="<$key>".$value."</$key>";
			else {$href=LK_PAG.$this->url_encrypt("$edt_pagina$value");$z=true;}

			$this->html_out .= "<item href=\"$href\">";
			$this->html_out .= "$tmp";

			$this->html_out .= "</item>";
			$i=++$i;
			$this->html_out .="</row>";
		}

		$this->html_out .="</list_items>";
		$this->html_out .= "</list>";
		return $this->html_out;
	}
}
?>