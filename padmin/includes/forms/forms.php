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
* Creates a html form
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
require_once("form_field.php");
require_once("form_textbox.php");
require_once("form_datebox.php");
require_once("form_textarea.php");
require_once("form_texteditor.php");
require_once("form_password.php");
require_once("form_static_listbox.php");
require_once("form_static_checkbox.php");
require_once("form_hidden.php");
require_once("form_checkbox.php");
require_once("form_static_radiobox.php");
require_once("form_filebox.php");
require_once("form_text.php");
require_once("form_picker.php");
require_once("form_smileys.php");
require_once("form_captcha.php");
/**
* Creates a html form
* @author   IT Elazos SL
* @version  1.0
* @package  Forms
*/
class htmlform {
    var $name;   // Nombre del formulario
    var $action; // Guarda el nombre del script php del formulario. Si no lo inicializas lo coje de PHP_SELF
    var $fields; // Array con los campos necesarios por tipo de control utilizado
    var $method;
    var $script="";
    var $button_text;
    var $title="";
    var $links=NULL;
    var $num_cols=1;
    var $disabled=false;
    var $images="images/";


    function htmlform( $name, $action = "", $method = "get", $button_text="Submit" )
    {
        $this->fields = array();
        $this->name = $name;
        if( $action == "" ) $action = basename($_SERVER["PHP_SELF"]);
        $this->action = $action;
        if ($method=="post" || $method=="POST") $method="post"; else $method="get";
        $this->method = $method;
        $this->button_text=$button_text;
    }
    
    function set_form_title( $title, $links)
    {
     $this->title=$title;
     $this->links=$links;
    }
    function draw_title()
    {
    $out_string="";
    if ($this->title!="" || is_array($this->links)) {
       $out_string .= "<table class=\"form_title\"><tr>\n";
       $out_string .= "<td class=\"form_title\">".$this->title."</td>\n";
       if (is_array($this->links))
          foreach($this->links as $value){
                    $out_string .="<td class=\"form_link\"><a class=\"form_link\" href=\"".$value["href"]."\">".$value["txt"]."</a></td>\n";
                    $out_string .="<td class=\"form_title\"></td>\n";
                    }
                 
       $out_string .= "</tr></table>\n";
      }

    return $out_string;
    }

    function add_text( $field, $title, $hidden=true )
    {
        $this->fields[$field] = new form_text( $this->name, $this->method, $field, $title, $hidden );
    }

    function add_textbox( $field, $title, $size, $maxlength=0 )
    {
        $this->fields[$field] = new form_textbox( $this->name, $this->method, $field, $title, $size, $maxlength );
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }
    
    function add_captcha( $field, $title )
    {
        $this->fields[$field] = new form_captcha( $this->name, $this->method, $field, $title, 6, 6 );
        
    }

    function add_datebox( $field, $title, $size=8, $maxlength=10 )
    {
        $this->fields[$field] = new form_datebox( $this->name, $this->method, $field, $title, $size, $maxlength );
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }
    /**
    * Example : $form->add_picker( "name_picker", "label": ", 4, 4, "picker.php?".$this->url_encrypt("pg=emples&pk=name_picker&pos=3&from=0"));
    * Encrypted parameters :
    * pos : Position of the displayed columns to send as a text to the picker
    * pk : name of the picker. Is the same that name_picker.
    **/
    function add_picker( $field, $title, $size, $maxlength=0, $url, $hidden = false)
    {
        $this->fields[$field] = new form_picker( $this->name, $this->method, $field, $title, $size, $maxlength, $url, $hidden);
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }

    function add_textarea( $field, $title, $cols, $rows )
    {
        $this->fields[$field] = new form_textarea( $this->name, $this->method, $field, $title, $cols, $rows );
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }
    
    function add_texteditor( $field, $title, $cols, $rows )
    {

        $this->fields[$field] = new form_texteditor( $this->name, $this->method, $field, $title, $cols, $rows );
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }

    function add_password( $field, $title, $size, $maxlength=0 )
    {
        $this->fields[$field] = new form_password( $this->name, $this->method, $field, $title, $size, $maxlength );
    }

    function add_static_listbox( $field, $title, $options, $char_separator = "," )
    {
        $this->fields[$field] = new form_static_listbox( $this->name, $this->method, $field, $title, $options, $char_separator );
        if ($this->disabled) $this->fields[$field]->updatable=false;
    }
    
    function add_link_2listbox( $title1,$title2, $field1,$field2, $result,$txt_blk_opt="" )
    {

       if (!is_array($result) || count($result)==0) return false;
       $this->script  .= "<script type=\"text/javascript\" id=\"".$this->name."_$field1\">\n"
                      ."var levels = new DynamicOptionList(\"".$this->name."_$field1\",\"".$this->name."_$field2\");\n";
       $tmp=Null;
       foreach ($result as $key=>$value)
            {
            foreach ($value as $key2=>$value2)
                 if ($value2!="") $this->script.= "levels.forValue(\"$key\").addOptionsTextValue('$value2','$value2');\n";
                     else $this->script.= "levels.forValue(\"$key\").addOptionsTextValue('$txt_blk_opt','$value2');\n";
            if ($key=="") $keyvalue=$txt_blk_opt; else $keyvalue=$key;
            if ($tmp==NULL) $tmp="$key;$keyvalue";else $tmp=$tmp.",$key;$keyvalue";
            }
        
       $this->script .= "levels.selectFirstOption = true;\n</script>\n";
       $this->fields[$field1] = new form_static_listbox( $this->name, $this->method, $field1, $title1, "$tmp" );
       $this->fields[$field2] = new form_static_listbox( $this->name, $this->method, $field2, $title2, ";<script type=\"text/javascript\">levels.printOptions(\"".$this->name."_$field2\")</script>" );

    }
    
    function add_link_3listbox( $title1,$title2,$title3, $field1,$field2,$field3, $result )
    {
       $this->script  .= "<script type=\"text/javascript\" id=\"".$this->name."_$field1\">\n"
                      ."var levels = new DynamicOptionList(\"".$this->name."_$field1\",\"".$this->name."_$field2\",\"".$this->name."_$field3\");\n";


       $lines_a="";
       $lines_b="";
       $tmp=Null;
       foreach ($result as $key=>$value)
            {
           $tmp2=Null;
               foreach ($value as $key2=>$value2)
                    {
                    $tmp3=Null;
                    if ($tmp2==NULL) $tmp2="\"$key2\"";else $tmp2=$tmp2.",\"$key2\"";
                    foreach ($value2 as $value3)
                         if ($tmp3==NULL) $tmp3="\"$value3\"";else $tmp3=$tmp3.",\"$value3\"";
                    if ($tmp3!="\"\"" ) $lines_b.= "levels.forValue(\"$key\").forValue(\"$key2\").addOptions($tmp3);\n";
                    }
            if ($tmp2!="\"\"" ) $lines_a.= "levels.forValue(\"$key\").addOptions($tmp2);\n";
            if ($tmp==NULL) $tmp="$key;$key";else $tmp=$tmp.",$key;$key";
            }

       $this->script.= $lines_a.$lines_b;
       $this->script .= "levels.selectFirstOption = false;\n</script>\n";
                     //."<script LANGUAGE=\"JavaScript\">writeSource(\"".$this->name."$field1\");</script>\n";

       $this->fields[$field1] = new form_static_listbox( $this->name, $this->method, $field1, $title1, "$tmp" );
       $this->fields[$field2] = new form_static_listbox( $this->name, $this->method, $field2, $title2, ";<script type=\"text/javascript\">levels.printOptions(\"".$this->name."_$field2\")</script>" );
       $this->fields[$field3] = new form_static_listbox( $this->name, $this->method, $field3, $title3, ";<script type=\"text/javascript\">levels.printOptions(\"".$this->name."_$field3\")</script>" );

    }

    function add_hidden( $field )
    {
        $this->fields[$field] = new form_hidden( $this->name, $this->method, $field );
    }

    function add_checkbox( $field, $title, $checked_value, $unchecked_value )
    {
        $this->fields[$field] = new form_checkbox( $this->name, $this->method, $field, $title,  $checked_value, $unchecked_value );
    }

    function add_static_radiobox( $field, $title, $options )
    {
        $this->fields[$field] = new form_static_radiobox( $this->name, $this->method, $field, $title, $options );
    }

    function add_static_checkbox( $field, $title, $options )
    {
        $this->fields[$field] = new form_static_checkbox( $this->name, $this->method, $field, $title, $options );
    }
    
    function add_filebox( $field, $title, $size, $maxsize, $uploadfolder )
    {
        $this->fields[$field] = new form_filebox( $this->name, $field, $title, $size, $maxsize, $uploadfolder );
    }

    function add_smileys( $field, $title, $dest_field )
    {
        $this->fields[$field] = new form_smileys( $this->name, $this->method, $field, $title, $dest_field );
    }

    function draw_header()
    {

        return  "<table class=\"forms_border\"><tr><td class=\"forms_border\">"
               ."<form class=\"forms\" method=\"$this->method\" action=\"{$this->action}\" name=\"{$this->name}\" enctype=\"multipart/form-data\">\n";

    }

    function draw_footer()
    {
        $out_string = "<input type=\"hidden\" name=\"{$this->name}_phpform_sent\" value=\"1\"/>\n"
                     ."</form>\n</td></tr>\n";
        if (!$this->disabled) {
            $out_string .= "<tr><td class=\"form_button\">\n"
                     ."<a class=\"boton\" href=\"javascript:document.".$this->name.".submit();\"><img class=\"boton\" src=\"".$this->images."form_butt.gif\" alt=\"\"/>\n"
                     .$this->button_text."</a>"
                     ."</td></tr>";
                     }
        $out_string  .="</table>\n";
                     
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
        $out_string.= "<table class=\"forms\">\n";
        $field=array_keys($this->fields);
        reset($field);
        $ind_first=true;
        while( list($pos,$field_name) = each($field) )
        {
           if ($this->num_cols>0) {
           if ($this->fields[$field_name]->col==1){
              if ($ind_first) $ind_first=false;else $out_string.="</tr>";
              $out_string.="<tr><td class=\"field_title\">";}
              else $out_string.="<td class=\"field_title\">";
           $out_string.= $this->fields[$field_name]->title."</td>";
           $colspan="";
           if ($this->num_cols>1) {
               if ($this->fields[$field_name]->col==1 && array_key_exists($pos+1,$field) && $this->fields[$field[$pos+1]]->col==1)
                 $colspan="colspan=\"3\"";
              }
           $out_string.="<td class=\"field_value\" $colspan>";
           $out_string.=$this->fields[$field_name]->draw()."</td>";
           } else
           {
           if ($ind_first) $ind_first=false;else $out_string.="</tr>";
           $out_string.="<tr><td class=\"field_value\">".$this->fields[$field_name]->title."<br />";
           $out_string.=$this->fields[$field_name]->draw()."</td>";
           }
        }
        $out_string.= "</tr></table>\n";
        $out_string.= $this->draw_footer();
        return $out_string;
    }
    // Controla si el form se ha vuelto a llamar mediante la variable hidden phpform_sent despues del GET
    function process()
    {
        if ($this->method=="get") {
        if( !isset( $_GET["{$this->name}_phpform_sent"] ) ) return false;
        } else  {if( !isset( $_POST["{$this->name}_phpform_sent"] ) ) return false;}

        reset($this->fields);
        while( $field = each($this->fields) )
        {
            $this->fields[$field[1]->field]->process();
        }
        return true;
    }
    // Inicializa todo
    function clear()
    {
        reset($this->fields);
        while( $field = each($this->fields) )
        {
            $this->fields[$field[1]->field]->value = "";
        }
    }
    /**
    * Convierte un array en una lista para listbox
    *@param mixed Array de codigo->valor o lista de valores separados por comas.
    **/
    function convert($options)
    {
    $isAr=False;
    if (!is_array($options)) {$options=explode(",",str_replace("'","",$options));$isAr=true;}
    
    $result="";
    foreach($options as $key=>$value)
            {
            if ($isAr) $key=$key+1;
            if ($result=="") $result="$key;$value";else $result.=",$key;$value";
            }
    return $result;
    }
}
?>
