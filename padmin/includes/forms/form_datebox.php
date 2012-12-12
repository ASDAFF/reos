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
* Date Control.
* Field to enter dates.
* Campo de introducción de Fechas.
**/
require_once("form_field.php");
/**
* Creates a date box field in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_datebox extends form_field {

    function form_datebox( $form_name, $method, $field, $title, $size=8, $maxlength=10 )
    {
        $this->form_name = $form_name;
        $this->method = $method;
        $this->field = $field;
        $this->title = $title;
        $this->size = $size;
        $this->maxlength = $maxlength;
        $this->key = $this->form_name . "_" . $this->field;
                $this->cssclass = "field_datebox";

    }

        function get_string()
        {
        if( strlen($this->onblur) ) $javascript = "onblur=\"{$this->onblur}\"";
        else $javascript="";
        /*if( !empty($this->title) ) $title = $this->title."";
                else $title = "";*/
        if( $this->maxlength > 0 ) $maxlength = "maxlength=\"{$this->maxlength}\"";
        else $maxlength = "";
        if ($this->updatable) $disabled="";else $disabled="disabled=\"disabled\"";
        $return="";
        if ($this->updatable) {
           $return="<LINK REL=\"stylesheet\" HREF=\""._DirSTYLE."datepicker.css\" type=\"text/css\"/>\n";
           $return.="<SCRIPT LANGUAGE=\"JavaScript\" SRC=\""._DirSCRIPTS."datepicker.js\"></SCRIPT>\n";
           }
        $return.="\n<input type=\"text\" class=\"{$this->cssclass}\" name=\"{$this->key}\" size=\"{$this->size}\" $maxlength value=\"".htmlspecialchars($this->value)."\" $javascript {$this->tag_extra} $disabled/>\n";
        if ($this->updatable) $return.="<a href=\"javascript:void(0);\" onclick=\"displayDatePicker('{$this->key}',null,'".strtolower(str_replace("/","",_DATE_FORMAT))."');\"><img src=\""._DirIMAGES."fcalendar.png\" alt=\"\" style=\"vertical-align:top;border:0\"/></a>";
        return $return;
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
