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
*Class xmlform definition file
*@author Josep Marxuach  - January 2005
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_account.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
/**
*Class htmlform definition file
**/
require_once("forms.php");

/**
*Create a xml form object
*@author Josep Marxuach
*@version 1.0
*@copyright 2004 by Josep Marxuach
*@package Forms
*/
class xmlform extends htmlform {

/**
*Create an element formdef containing the html form
*@author Josep Marxuach
*@access private
*@return string Returns the header of the
*/
function draw_header()
    {

        return  "<formdef name=\"{$this->name}\">"
               ."<htmlform><form class=\"forms\" method=\"$this->method\" action=\"{$this->action}\" id=\"{$this->name}\" enctype=\"multipart/form-data\">\n";

    }

    function draw_footer()
    {
        $out_string = "<div><input type=\"hidden\" name=\"{$this->name}_phpform_sent\" value=\"1\"/></div>\n"
                     ."</form></htmlform>\n";
        if (!$this->disabled) {
            $out_string .= "<fbutton>\n"
                        .$this->button_text
                        ."</fbutton>";
                       }
        $out_string  .="</formdef>\n";

        return $out_string;
    }

/***************************************END CLASS*****************************************************/
}
