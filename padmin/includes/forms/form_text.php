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
* Creates a static text field in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_text extends form_field {
var $hidden;

    function form_text( $form_name, $method,  $field, $title, $hidden )
    {
        $this->form_name = $form_name;
        $this->method = $method;
        $this->field = $field;
        $this->title = $title;
        $this->key = $this->form_name . "_" . $this->field;
        $this->cssclass = "field_text";
        $this->hidden=$hidden;
    }

    function get_string()
    {
        if ($this->hidden) $string="<input type=\"hidden\" name=\"$this->key\" value=\"".htmlspecialchars($this->value)."\" />\n";
           else $string="";
        //$string.="<div class=\"$this->cssclass\" name=\"$this->key\" >".($this->value)."</div>\n";
        $string.="<div class=\"$this->cssclass\">".($this->value)."</div>\n";
        return $string;
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
