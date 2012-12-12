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
* Creates a picker field in html
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class form_picker extends form_field {
var $pk_url;
var $hd_field; //if true input field is hidden
var $pktext; //Text showed
var $pklink; //href for Text showed

    function form_picker( $form_name, $method, $field, $title, $size, $maxlength, $pk_url, $hidden )
    {
        $this->form_name = $form_name;
        $this->method = $method;
        $this->field = $field;
        $this->title = $title;
        $this->size = $size;
        $this->maxlength = $maxlength;
        $this->key = $this->form_name . "_" . $this->field;
        $this->cssclass = "field_textbox";
        $this->pk_url=$pk_url;
        $this->hd_field=$hidden;
    }

        function get_string()
        {
        if( strlen($this->onblur) ) $javascript = "onblur=\"{$this->onblur}\"";
        else $javascript="";
        /*if( !empty($this->title) ) $title = $this->title."";
                else $title = "";*/
        if( $this->maxlength > 0 ) $maxlength = "maxlength=\"{$this->maxlength}\"";
                              else $maxlength = "";
        if ($this->updatable) {
           $return="<LINK REL=\"stylesheet\" HREF=\"style/picker.css\" type=\"text/css\" />\n";
           $return.="<SCRIPT LANGUAGE=\"javascript\" SRC=\"jscripts/picker.js\"></SCRIPT>\n";
           }
           
        if ($this->hd_field) $type="type=\"hidden\""; else $type="type=\"text\" class=\"{$this->cssclass}\" size=\"{$this->size}\" $maxlength disabled=\"disabled\"";
        
        //$return .= "\n<span name=\"{$this->field}_node\"  id=\"{$this->field}_node\"><input $type name=\"{$this->key}\" value=\"".htmlspecialchars($this->value)."\" $javascript {$this->tag_extra}/><span class=\"{$this->cssclass}\" id=\"{$this->field}_vl\">".htmlspecialchars($this->pktext)."</span></span>\n";
        $return .= "\n<span name=\"{$this->field}_node\"  id=\"{$this->field}_node\">\n"
        ."<input $type name=\"{$this->key}\" value=\"".htmlspecialchars($this->value)."\" $javascript {$this->tag_extra}/>\n"
        ."<input type=\"hidden\" name=\"{$this->key}_vl\" id=\"{$this->field}_vl\" value=\"{$this->pktext}\" />\n";
        
        if (!isset($this->pklink))
        	$return .="<span class=\"{$this->cssclass}\" id=\"{$this->field}_span\">".htmlspecialchars($this->pktext)."</span>\n";
        	else
        	$return .="<span class=\"{$this->cssclass}\" id=\"{$this->field}_span\">".$this->pklink."</span>\n";
        
        $return .="</span>\n";
        if ($this->updatable) $return.="<a href=\"javascript:void(0);\" onclick=\"displayPicker('{$this->field}', '{$this->key}', '{$this->pk_url}');return false;\"><img src=\"images/wpicker.png\" alt=\"\" style=\"vertical-align:middle;border:0\"></a>";
        return $return;
        }

    function process()
    {
        if ($this->method=="get" )
            {
            if( isset( $_GET[$this->key] ) ) {
               $this->value = $_GET[$this->key];
               $this->pktext = $_GET[$this->key."_vl"];
               $this->delmagic();
            }
        } else
           {
                if( isset( $_POST[$this->key] ) ) {
               $this->value = $_POST[$this->key];
               $this->pktext = $_POST[$this->key."_vl"];
                $this->delmagic();
            }
        }
        
    }
}
