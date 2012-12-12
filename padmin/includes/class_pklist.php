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
* Create a picker list control.
*
*@package View
**/
class pklist extends utils {
/**
*Returns a html table list from an array of columns.
*The array layout is list[row_num][fieldname]=fieldvalue and List[0][field_name]=fieldname.
*Returns html into the class var html_out.
*@author Josep Marxuach
*@access public
*@param array   array[row_num][fieldname]=fieldvalue
*@param integer initial row number within the sql query, needed to link with next & previous page
*@param integer Number of rows to show for each page or offset
*@param string current page name for links. Here vars that want to pass allways.
*@param integer Variables to pass through Ex : "view=$view&id_account=$id_account"
*@param string Name of the target object. Is the name of the form_picker
*@param Integer Position of the displayed columns to send as a text to the picker
*@param integer Total Number of rows found
*/
function print_list( $list, $from, $num_rows, $pagina, $vars, $pkfield, $pos, $found_rows=Null) {
$out = "";
$next=$from+$num_rows;
$previous=$from-$num_rows;
$total=count($list)-1;
$col_num=count($list[0])-1;

$out .= "<script>function update(pkvalue, pktext){parent.UpdateField('$pkfield',pkvalue,pktext);}</script>";

$out .= "<input type=\"hidden\" name=\"pkfield\"/>";
$out .= "<table class=\"lista\">";
$out .= "<tr><td class=\"list_found\" colspan=\"$col_num\">"._FOUND." $found_rows "._ROWS."</td></tr>";
if ($found_rows>0) {

if($found_rows>$num_rows){
$out .= "<tr><td class=\"list_pages\" colspan=\"$col_num\">"._PAGS." ";
$z=0;
   for ($i=0;$i<$found_rows;$i=$i+$num_rows){
     if ($from>=($i-($num_rows*5)) && $from<=($i+($num_rows*5))){
        if ($z>0) $out .="-";
        if ($from==$i) $out .= "<span class=\"list_page\">".($i/$num_rows+1)."</span>";
        else $out .= " <a href=\"".LK_PK."".$this->url_encrypt("$pagina&from=$i&pk=$pkfield&pos=$pos&$vars")."\">".($i/$num_rows+1)."</a> ";
        $z++;
        }
     }
$out .= " "._OF." ".ceil($found_rows/$num_rows)." ";

if ($from>0) $out .= " <a href=\"".LK_PK."".$this->url_encrypt("$pagina&from=$previous&pk=$pkfield&pos=$pos&$vars")."\">"._PREVIOUS."</a>";
if ($from>0 && $found_rows>$next) $out .=" - ";
if ($found_rows>$next) $out .= "<a href=\"".LK_PK."".$this->url_encrypt("$pagina&from=$next&pk=$pkfield&pos=$pos&$vars")."\">"._NEXT."</a>";
$out .= "</td></tr>";
}

// Cabecera
$out .= "<tr class=\"colheader\">";
$z=false; // sirve para controla que la 1a columna no se visualize
$field_keys=array_keys($list[0]);
while (list($key,$value)=each($field_keys)){
      if ($z) $out .= "<td class=\"colheader\">".constant(strtoupper("_$value"))."</td>";
          else $z=true;
      }

$out .= "</tr>";

// Contenido
$i=0;
while ($i<=$total)
       {
       $ln=$i % 2;
       $out .= "<tr class=\"linea$ln\" onmouseover=\"this.className='linea_over'\" onmouseout=\"this.className='linea$ln'\">";
       $z=0; // sirve para controla que la 1a columna no se visualize y el link a editar
       while (list($key,$value)=each($list[$i])) {
       $class="lista";
       if (strstr($key,"precio") && isset($value)) {if (($value-(int)$value)!=0) $dec=2;else $dec=0;
                                                    $value=number_format($value,$dec,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY."";$class="lista_r";}
       if ($z==1) {
          $out .= "<td class=\"lista\"><a href=\"javascript:void(0);\" onclick=\"update('".$list[$i][$field_keys[0]]."','".$list[$i][$field_keys[$pos]]."');return false;\">".$list[$i][$field_keys[1]]."</a></td>";
          }
          
       if ($z>1) {
            if(!strpos($value,"href=")) $value = wordwrap($value, 110, "<br />\n");
            $out .= "<td class=\"$class\">$value</td>";}
       $z++;
       }
       //if (isset($borrar)) $out .= "<td class=\"lista\"><a href=\"".LK_PK."".$this->url_encrypt("$pagina&$borrar".$list[$i][$field_keys[0]]."&from=$from&$vars")."\" onclick=\"return confirm('"._DELETE." - ".addslashes($list[$i][$field_keys[1]])."?')\">"._DELETE."</a></td>";
       $out .= "</tr>";
       $i=++$i;
       }
}
$out .= "</table>";
return $out;
}


} // END CLASS
?>
