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
* Creates a static_radiobox in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_static_radiobox extends form_field {
        // array of value, text
        var $options = array();

    function form_static_radiobox( $form_name, $method, $field, $title, $options )
    {
        $this->form_name = $form_name;
        $this->method=$method;
        $this->field = $field;
        $this->title = $title;
        if( is_array($options) ) $this->options = $options;
                else {
                        $tok = strtok ($options, ",");
                        while( $tok )
                        {
                                $pos = strpos($tok, ";");
                                if ($pos === false) {
                                        $this->options[] = array( $tok, $tok );
                                } else {
                                        $this->options[] = array( substr($tok, 0, $pos), substr($tok, $pos + 1) );
                                }
                                $tok = strtok (",");
                        }
                }
        $this->key = $this->form_name . "_" . $this->field;
                $this->cssclass = "field_radiobox";
    }

        function get_string()
    {
        if( strlen($this->onblur) ) $javascript = "onblur=\"{$this->onblur}\"";
        else $javascript="";
        //if( !empty($this->title) ) $ret = $this->title."";
          //      else $ret = "";
        $ret="";
        reset( $this->options );
        while( $tok = each($this->options) )
        {
                        $checked = ($tok[1][0] == $this->value)?"checked":"";
            $ret .= "<input type=\"radio\" class=\"{$this->cssclass}\" name=\"$this->key\" value=\"{$tok[1][0]}\" $checked $javascript  {$this->tag_extra}/>{$tok[1][1]}\n<br />";
        }
                return $ret;
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
