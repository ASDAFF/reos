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
* Creates a hidden field in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_hidden extends form_field {
    
    function form_hidden( $form_name, $method,  $field )
    {
        $this->form_name = $form_name;
        $this->method = $method;
        $this->field = $field;
        $this->key = $this->form_name . "_" . $this->field;
    }

    function get_string()
    {
        return "<input type=\"hidden\" id=\"$this->key\" name=\"$this->key\" value=\"".htmlspecialchars($this->value)."\"/>\n";
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
