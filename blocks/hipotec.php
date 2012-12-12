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
*Returns xml node for mortgage calculation.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/hipotec.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
//require_once(_DirINCLUDES."class_account.php");

global $nm;
global $precio;

$tit_pag="$nm";
//$dbi= new account;


$this->html_out .= $this->pgtitle($tit_pag,true,Null);


$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._CALC."");
$form->num_cols=2;
$form->add_textbox( "valor", ""._PRE_VIVIE." "._CURRENCY, 12, 12 );
$form->add_textbox( "entrada", ""._IMP_ENTR." "._CURRENCY, 12, 12 );
$form->add_textbox( "prestamo", ""._IMP_SOL." "._CURRENCY, 12, 12 );
$form->add_textbox( "interes", ""._INT_BANC." %", 4, 4 );
$form->add_textbox( "anos", ""._YEARS."", 2, 2 );
$form->add_text( "cuota", ""._PAGO_MEN."");


//$form->fields["txt_telf1"]->col = 2;

$form->add_hidden("data");
$processed = $form->process();
if ($form->fields["entrada"]->value=="") $form->fields["entrada"]->value=0;
$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&nm=".$nm);

//$form->fields["imp"]->col=2;

if(!$processed) {

        if (isset($precio)) $form->fields["valor"]->value=number_format($precio,0,",",".");
        $form->fields["interes"]->value = "3,0";
        $form->fields["anos"]->value = 30;
        $this->html_out .= "<hipotec>";
        $this->html_out .=$form->draw();
        $this->html_out .= "</hipotec>";

} else
{

$P=str_replace(".","",$form->fields["prestamo"]->value);
$valor=str_replace(".","",$form->fields["valor"]->value);
$entrada=str_replace(".","",$form->fields["entrada"]->value);
$interes=str_replace(",",".",$form->fields["interes"]->value);


if ($form->fields["valor"]->value!="" && $form->fields["entrada"]->value!="")
    $P=$valor-$entrada ;

$form->fields["prestamo"]->value=number_format($P,0,",",".");
$form->fields["entrada"]->value=number_format($entrada,0,",",".");
$form->fields["valor"]->value=number_format($valor,0,",",".");

$form->fields["interes"]->value=number_format($interes,2,",",".");

$I=$interes/100/12;
$A=$form->fields["anos"]->value*12;

$cuota =$P/((pow(1+$I,$A)-1)/($I*pow(1+$I,$A)));

$form->fields["cuota"]->value=number_format($cuota,0,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY;

$this->html_out .= "<hipotec>";

$this->html_out .=$form->draw();

//$this->html_out .= "<gst>\n";

//$this->html_out .= "<title>"._GST_HIPOT."</title>\n";
//$this->html_out .= "<item><label>"._COM_APER."</label><value>".number_format(($P*0.01),0,",",".")." "._CURRENCY."</value></item>\n";
//$this->html_out .= "<item><label>"._IMP_ACTOS."</label><value>".number_format(($P*0.0106),0,",",".")." "._CURRENCY."</value></item>\n";
//$this->html_out .= "<item><label>"._GAST_GEST."</label><value>500 "._CURRENCY."</value></item>\n";
//$this->html_out .= "<item><label>"._SEGUROS."</label><value>300 "._CURRENCY."</value></item>\n";
//$this->html_out .= "<total><label>"._TOTAL."</label><value>".number_format((($P*0.01)+($P*0.0106)+500+300),0,",",".")." "._CURRENCY."</value></total>\n";
//$this->html_out .= "</gst>\n";

//$this->html_out .= "<gst>\n";

//$this->html_out .= "<title>"._GST_COMPRA."</title>\n";
//$this->html_out .= "<item><label>"._IMP_COMPRA."</label><value>".number_format(($valor*0.07),0,",",".")." "._CURRENCY."</value></item>\n";
//$this->html_out .= "<item><label>"._GAST_GEST."</label><value>".(500)." "._CURRENCY."</value></item>\n";
//$this->html_out .= "<total><label>"._TOTAL."</label><value>".number_format((($valor*0.07)+500),0,",",".")." "._CURRENCY."</value></total>\n";
//$this->html_out .= "</gst>\n";

$this->html_out .= "<total>\n<label>"._TOT_GST."</label><value>".number_format(((($valor*0.07)+500)+(($P*0.01)+($P*0.0106)+500+300)),0,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY."</value></total>\n";

$this->html_out .= "</hipotec>\n";



}



?>
