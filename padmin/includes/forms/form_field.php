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
* Handles fields of a form
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/

class form_field {
    var $form_name;
    var $field;
    var $title;
    var $size;
    var $maxlength;
    var $col=1;
    var $type;
    var $value;
    var $key;
    var $cssclass;
    var $updatable = true;
    var $method;

    // Javascript support
    var $onblur;
    var $tag_extra;

    /*function draw() {
    $out_string = "<tr>";
    $out_string.= "<td class=\"field_title\">\n";
    if ($this->cssclass != "field_textarea") $out_string.= $this->title;
    $out_string.= "</td>\n";
    $out_string.= "<td class=\"field_value\">\n";
    $out_string.= $this->get_string();
    $out_string.= "</td></tr>\n";
    return $out_string;
    } */
    
    function draw() {
    $out_string = $this->get_string();
    return $out_string;
    }

    function process() {}

    function get_string() { return ""; }

    function delmagic()
    {
        // this function removes backslashes ig magic_quotes_gpc is on
        if( get_magic_quotes_gpc() ) $this->value = stripslashes( $this->value );
    }
}
?>
