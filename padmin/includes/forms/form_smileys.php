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


require_once("form_field.php");
/**
* Creates smileys icons in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_smileys extends form_field {

    function form_smileys( $form_name, $method, $field, $title, $dest_field )
    {
        $this->form_name = $form_name;
        $this->method = $method;
        $this->field = $field;
        $this->title = $title;
        $this->key = $this->form_name . "_" . $this->field;
        $this->dest_field = $dest_field;
    }

        function get_string()
        {
          $nomf = $this->form_name;
          $dest = $nomf."_".$this->dest_field;
          $javas = "<script language=\"JavaScript\">\nfunction AddSmileyIcon( iconCode ){\ndocument.$nomf.$dest.value += iconCode + \" \";\ndocument.$nomf.$dest.focus();}\n</script>\n";

          $dir_img = _TPLDIR._THEME_DIR."/"._DirIMAGES;
          $simbols = array( "'[:)]'", "'[;)]'", "'[:o]'", "'[:D]'", "'[:errr:]'", "'[:(]'", "'[:x]'", "'[:o)]'", "'[:oops:]'", "'[:star:]'", "'[xx(]'", "'[|)]'", "'[:V:]'", "'[:^:]'", "'[}:)]'", "'[8D]'" );
          $icons = "";
          for( $i = 1; $i <= 16; $i++ ) {
            $img = $dir_img."smiley$i.gif";
            $icons .= "<a href=\"javascript:AddSmileyIcon(".$simbols[$i-1].")\"><img src=\"$img\" alt=\"\" /></a> ";
          }
          
          return $javas.$icons;
        }

    function process()
    {
             if ($this->method=="get" )
            {
            if( isset( $_GET[$this->key] ) ) {
               $this->value = $_GET[$this->key];
                $this->delmagic();
            }
        } else
           {
                if( isset( $_POST[$this->key] ) ) {
               $this->value = $_POST[$this->key];
                $this->delmagic();
            }
        }
    }
}
?>
