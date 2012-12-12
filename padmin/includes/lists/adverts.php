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
 * Creates XML output for Real Estate Adverts.
 */
/**
 * Class Utils file.
 * Class Session extends class Utils.
 */
require_once(_DirINCLUDES."class_utils.php");
/**
 * Creates XML output for Real Estate Adverts.
 *
 */
class adverts extends utils {

	var $html_out = "";

	function adverts(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_PROPERORD',_IDIOMA);
		$lovs->getLovs('_LST_INTRO',_IDIOMA);
		$lovs->getLovs('_LST_PROPERTIES',_IDIOMA);
		$lovs->getLovs('_LST_PISCINA',_IDIOMA);
	}

	/**
	 * Returns a string with the advertissing text.
	 * Crea un anuncio que se compone de frases separadas por comas.
	 * @author Josep Marxuach
	 * @access public
	 * @param array   array[fieldname]=value
	 * @param string  name of the linked page
	 * @param array   array[]=properties_names
	 * @param array   array[]=intro_names
	 * @param array   array[]=properties indexes in order to be displayed
	 */
	function create_advert($fields,$edt_pagina,$lst_properties,$lst_intro,$lst_properord){

		if ($edt_pagina) $href="href=\"".$this->getPermalink($fields["tp_propiedad"]." ".$fields["tp_servicio"]." ".$fields["txt_poblacion"])."".$this->url_encrypt("$edt_pagina".$fields["id_immo"]."&nm=".$fields["ref_immo"]." - ".$fields["txt_poblacion"]." - ".$fields["txt_zona"])."\"";else $href="";
		$out="<advert $href>";
		if (isset($fields["img_front"])) $out .="<img src=\""._DirGALLERIES.$fields["dir_gal"]."/thumbnails/".$fields["img_front"]."\"/>";

		$out.="<refe>".$fields["ref_immo"]."</refe>";
		if (isset($fields["txt_poblacion"])) $out .="<city>".$fields["txt_poblacion"]."</city>";
		if (isset($fields["txt_zona"])) $out .="<zone>".$fields["txt_zona"]."</zone>";
		if (isset($fields["tp_propiedad"])) $out .="<transaction>".$fields["tp_propiedad"]." "; // ._TO
		if (isset($fields["tp_servicio"])) $out .= $fields["tp_servicio"]."</transaction>";
		$out.="<text>";
		if (isset($fields["set_intro"])) $out .=$this->get_immo_set($lst_intro,$fields["set_intro"]).", ";

		if (isset($fields["num_dormitorios"]) && $fields["num_dormitorios"]>0)
		{ if ($fields["num_dormitorios"]>1) $out .="".$fields["num_dormitorios"]." "._NUM_DORMITORIOS.", ";
		else $out .="".$fields["num_dormitorios"]." "._NUM_DORMITORIO.", ";
		}
		if (isset($fields["num_wc"]) && $fields["num_wc"]>0)
		{if ($fields["num_wc"]>1) $out .="".$fields["num_wc"]." "._NUM_WCS.", ";
		else $out .="".$fields["num_wc"]." "._NUM_WC.", ";
		}
		if (isset($fields["int_superficie_const"]) && $fields["int_superficie_const"]>0) $out .=$fields["int_superficie_const"]." "._M2.", ";
		if (isset($fields["int_superficie"]) && $fields["int_superficie"]>0 ) $out .=$fields["int_superficie"]." "._M2_TERRENO.", ";
		if (isset($fields["int_terrace"]) && $fields["int_terrace"]>0 ) $out .=$fields["int_terrace"]." "._M2_TERRACE.", ";
		if (isset($fields["num_parking"]) && $fields["num_parking"]>0)
		{ if ($fields["num_parking"]>1) $out .=$fields["num_parking"]." "._NUM_PARKINGS.", ";
		else $out .=$fields["num_parking"]." "._NUM_PARKING.", ";
		}
		if (isset($fields["ind_piscina"]) && $fields["ind_piscina"]>1 ) $out .=$this->get_immo_set(""._LST_PISCINA."",$fields["ind_piscina"]).", ";
		if (isset($fields["set_properties"])) $out .=$this->get_immo_set($lst_properties,$fields["set_properties"],$lst_properord);
		$out .=".</text>";
		$out.="<price>";
		if (isset($fields["precio"])) {
			if (isset($fields["id_seasons"])) $desde=_PRICE_FROM;else $desde=_PRECIO;
			$out .= "<label>$desde : </label><valprc>".number_format($fields["precio"],0,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY."</valprc>";
			$out .= "<type>".$fields["tp_price"]."</type>";
		}
		else $out .= "<label>"._PRECIO." : "._ACONSULTAR."</label>";

		$out .="</price>";

		$out .="</advert>";
		return $out;
	}
	/**
	 * Returns XML adverts.
	 * Includes pagination.
	 * Uses constants _LST_PROPERTIES, _LST_INTRO and others. See Contructor.
	 * Array layout is list[row_num][fieldname]=fieldvalue and List[0][field_name]=fieldname.
	 * Returns xml into the class var html_out.
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
	function print_advert( $list, $from, $num_rows, $pagina, $edt_pagina, $borrar, $vars,$found_rows=Null) {

		eval("\$lst_properties=array("._LST_PROPERTIES.");");
		eval("\$lst_properord=array("._LST_PROPERORD.");");


		eval("\$lst_intro=array("._LST_INTRO.");");

		$next=$from+$num_rows;
		$previous=$from-$num_rows;
		$total=count($list)-1;
		$col_num=count($list[0])-1;

		if (isset($borrar)) $col_num++;

		if (isset($this->vars["pg"])) $pagina="pg=".$this->vars["pg"]; else $pagina="";

		$this->html_out .= "<adverts>";
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

		$i=0;
		while ($i<=$total)
		{
			$ln=$i % 2;
			//$this->html_out .= "<tr class=\"linea_advert$ln\"><td class=\"lista_advert\">";

			$this->html_out .= $this->create_advert($list[$i],$edt_pagina,$lst_properties,$lst_intro,$lst_properord);

			//$this->html_out .= "</td></tr>";
			$i=++$i;
		}

		$this->html_out .= "</adverts>";
		
		return $this->html_out;

	}
	/**
	 * Returns a list of sentences separated by commas.
	 * It is the real estate description.
	 * @author Josep Marxuach
	 * @access public
	 * @param array   array[]=property_name
	 * @param string  Indexes of the property_names separated by commas
	 * @param string  array of with all indexes in order to display
	 */
	function get_immo_set($lst_properties, $set_properties, $lst_properord=null){
		eval("\$set_properties=array(".$set_properties.");");
		if (!is_array($lst_properties)) {eval("\$lst_properties=array(".$lst_properties.");");
		eval("\$lst_properord=array("._LST_PROPERORD.");");
		}
		if (isset($lst_properord)) {
			foreach($set_properties as $value){
				$out_order[]=$lst_properord[$value-1];
				$out_array[]=$lst_properties[$value-1];
			}
			array_multisort($out_order,$out_array);
			//print_r($out_order);print_r($out_array);
			$out="";
			foreach($out_array as $value) if ($out=="") $out.=$value; else $out.=", ".$value;
		} else {
			$out="";
			foreach($set_properties as $value){
				if ($out=="") $out.=$lst_properties[$value-1]; else $out.=", ".$lst_properties[$value-1];
			}
		}

		return $out;
	}

} // end class
?>