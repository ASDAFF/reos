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
               ."<htmlform><form class=\"forms\" method=\"$this->method\" action=\"{$this->action}\" name=\"{$this->name}\" enctype=\"multipart/form-data\">\n";

    }

function draw_footer()
    {
        $out_string = "<input type=\"hidden\" name=\"{$this->name}_phpform_sent\" value=\"1\"/>\n"
                     ."</form></htmlform>\n";
        if (!$this->disabled) {
            $out_string .= "<fbutton>\n"
                        .$this->button_text
                        ."</fbutton>";
                       }
        $out_string  .="</formdef>\n";

        return $out_string;
    }
    // Hace un return de todo el formulario
function draw()
    {
        $out_string="";
        $out_string.=$this->script;
        reset($this->fields);
        //print_r($this->fields);
        $num_list_box=0;
        while( $field = each($this->fields) )
        {
        if (isset($this->fields[$field[1]->field]->options))
               {
               if (preg_match("/<script type=\"text\/javascript\">/i",$this->fields[$field[1]->field]->options[0][1])&& $this->fields[$field[1]->field]->value!="")
                   {
                   if ($num_list_box==0) $out_string.= "<script type=\"text/javascript\">levels.forValue(\"".$field_prev[0]."\").setDefaultOptions(\"".$this->fields[$field[1]->field]->value."\");</script>\n";
                       else $out_string.= "<script type=\"text/javascript\">levels.forValue(\"".$field_prev[0]."\").forValue(\"".$field_prev[1]."\").setDefaultOptions(\"".$this->fields[$field[1]->field]->value."\");</script>\n";
                   $field_prev[]=$this->fields[$field[1]->field]->value;
                   $num_list_box++;
                   } else
                   {
                   $field_prev[0]=$this->fields[$field[1]->field]->value;
                   $num_list_box=0;
                   }

               }
        }

        $out_string.=$this->draw_title();
        $out_string.=$this->draw_header();
        $out_string.= "<xmlform>\n";
        $field=array_keys($this->fields);
        reset($field);
        $ind_first=true;
        while( list($pos,$field_name) = each($field) )
        {
           $out_string.="<$field_name title=\"".$this->fields[$field_name]->title."\">";
           $out_string.=$this->fields[$field_name]->draw();
           $out_string.="</$field_name>";
        }
        $out_string.= "</xmlform>\n";
        $out_string.= $this->draw_footer();
        return $out_string;
    }

/***************************************END CLASS*****************************************************/
}
