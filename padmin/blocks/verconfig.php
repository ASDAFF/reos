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
*Visualiza todos los ficheros de configuraci�n del dise�o gr�fico de la aplicaci�n p�blica.
*Puedes actualizar ficheros XSL,Imagenes, CSS
*@package blocks_admin
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verconfig.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");

global $nm;

if (!$xslt=$this->getxslfile(_NAVFILE)) $xslt=NULL;
if (!$xslt_prt=$this->getxslfile(_NAVPRT)) $xslt_prt=NULL;



if (!isset($nm)) $nm=_VERCONFIG;

$id_org_session=$this->auth["id_org"];

$tmpdir = ""._DirHOME._DirADMIN._DirTMP."";

$this->html_out .= $this->pgtitle($nm,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._UPDATE."");
//$form->title=$name;
$form->add_filebox( "navfile", ""._XMLNAV.":", 1, 300000, $tmpdir );
$form->add_filebox( "xslfile", ""._XSLFILE.":", 1, 300000, $tmpdir );

$form->add_filebox( "navprt", ""._XMLPRT.":", 1, 300000, $tmpdir );
$form->add_filebox( "xslprt", ""._XSLPRT.":", 1, 300000, $tmpdir );
$form->add_hidden("data");

$processed = $form->process();
if( !$processed ) {
  $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."");
  $this->html_out.=$form->draw();
} else {
        $xslfile=$form->fields["xslfile"]->image_uploaded;
        $navfile=$form->fields["navfile"]->image_uploaded;
        $navprt=$form->fields["navprt"]->image_uploaded;
        $xslprt=$form->fields["xslprt"]->image_uploaded;
        
        if (isset($navfile) && $navfile!=""){
            if (!copy(_DirTMP.$navfile,_TPLDIR._THEME_DIR."/"._NAVFILE.".xml"))
              {$this->add_msg("Error Uploading File !");} else
              {
              if (file_exists(_DirTMP.$navfile)) unlink(_DirTMP.$navfile);
              if (!$xslt_new=$this->getxslfile(_NAVFILE)) $xslt_new=NULL;
              if ($xslt!=$xslt_new) {unlink(_TPLDIR._THEME_DIR."/$xslt.xsl");$xslt=$xslt_new;}
              }
           }
        if (isset($navprt) && $navprt!=""){
           if (!copy(_DirTMP.$navprt,_TPLDIR._THEME_DIR."/"._NAVPRT.".xml"))
           {$this->add_msg("Error Uploading File !");} else
              {
              if (file_exists(_DirTMP.$navprt)) unlink(_DirTMP.$navprt);
              if (!$xslt_prt_new=$this->getxslfile(_NAVPRT)) $xslt_prt_new=NULL;
              if ($xslt_prt!=$xslt_prt_new) {unlink(_TPLDIR._THEME_DIR."/$xslt_prt.xsl");$xslt_prt=$xslt_prt_new;}
              }
           }
        if (isset($xslfile) && $xslfile!=""){
           if (!copy(_DirTMP.$xslfile,_TPLDIR._THEME_DIR."/$xslt.xsl"))
           {$this->add_msg("Error Uploading File !");} elseif (file_exists(_DirTMP.$xslfile)) unlink(_DirTMP.$xslfile);
           }
        if (isset($xslprt) && $xslprt!=""){
            if (!copy(_DirTMP.$xslprt,_TPLDIR._THEME_DIR."/$xslt_prt.xsl"))
           {$this->add_msg("Error Uploading File !");} elseif (file_exists(_DirTMP.$xslprt)) unlink(_DirTMP.$xslprt);
           }
        $this->html_out.=$form->draw();
       }


$filename = _TPLDIR._THEME_DIR."/result.xml";
if (file_exists($filename)) {
   $filetime=filemtime($filename);
   $xml_time="<span style=\"font: bold arial 12px;color:blue;\">".date (_DATE_FORMAT, $filetime)."</span> - <span style=\"font: bold arial 12px;color:red;\">".date ("H:i:s.", $filetime)."</span>";
   }
   
$filename = _TPLDIR._THEME_DIR."/"._NAVFILE.".xml";
if (file_exists($filename)) {
   $filetime=filemtime($filename);
   $nav_time="<span style=\"font: bold arial 12px;color:blue;\">".date (_DATE_FORMAT, $filetime)."</span> - <span style=\"font: bold arial 12px;color:red;\">".date ("H:i:s.", $filetime)."</span>";
}
$filename = _TPLDIR._THEME_DIR."/$xslt.xsl";
if (file_exists($filename)) {
   $filetime=filemtime($filename);
   $xsl_time="<span style=\"font: bold arial 12px;color:blue;\">".date (_DATE_FORMAT, $filetime)."</span> - <span style=\"font: bold arial 12px;color:red;\">".date ("H:i:s.", $filetime)."</span>";
}

$filename = _TPLDIR._THEME_DIR."/"._NAVPRT.".xml";
if (file_exists($filename)) {
   $filetime=filemtime($filename);
   $prt_time="<span style=\"font: bold arial 12px;color:blue;\">".date (_DATE_FORMAT, $filetime)."</span> - <span style=\"font: bold arial 12px;color:red;\">".date ("H:i:s.", $filetime)."</span>";
}
$filename = _TPLDIR._THEME_DIR."/$xslt_prt.xsl";
if (file_exists($filename)) {
   $filetime=filemtime($filename);
   $xslprt_time="<span style=\"font: bold arial 12px;color:blue;\">".date (_DATE_FORMAT, $filetime)."</span> - <span style=\"font: bold arial 12px;color:red;\">".date ("H:i:s.", $filetime)."</span>";
}


$this->html_out .="<table class=\"lista\">";
$this->html_out .="<tr class=\"colheader\"><td colspan=\"3\"  class=\"colheader\">"._OUTAPP."</td></tr>";

$this->html_out .="<tr><td class=\"lista\">";

$this->html_out.="<a href=\""._TPLDIR._THEME_DIR."/result.xml\" target=\"_blank\">"._XMLOUTPUT."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_XMLOUTPUT."</td><td class=\"lista\">$xml_time";

$this->html_out .="</td></tr><tr class=\"colheader\"><td colspan=\"3\" class=\"colheader\">"._GENERAL_LAYOUT."";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\""._TPLDIR._THEME_DIR."/"._NAVFILE.".xml\" target=\"_blank\">"._XMLNAV."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_XMLNAV."</td><td class=\"lista\">$nav_time";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\""._TPLDIR._THEME_DIR."/$xslt.xsl\" target=\"_blank\">"._XSLFILE."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_XSLFILE."</td><td class=\"lista\">$xsl_time";

$this->html_out .="</td></tr><tr class=\"colheader\"><td colspan=\"3\" class=\"colheader\">"._PRT_LAYOUT."";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\""._TPLDIR._THEME_DIR."/"._NAVPRT.".xml\" target=\"_blank\">"._XMLPRT."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_XMLPRT."</td><td class=\"lista\">$prt_time";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\""._TPLDIR._THEME_DIR."/$xslt_prt.xsl\" target=\"_blank\">"._XSLPRT."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_XSLPRT."</td><td class=\"lista\">$xslprt_time";

$this->html_out .="</td></tr><tr class=\"colheader\"><td colspan=\"3\"  class=\"colheader\">"._ADDT_FILES."";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\"".LK_PAG.$this->url_encrypt("pg=cssconfig&dir_css="._TPLDIR._THEME_DIR."/"._DirSTYLE."")."\" >"._CSSCONFIG."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_CSSFILE."</td><td class=\"lista\">";

$this->html_out .="</td></tr><tr><td class=\"lista\">";

$this->html_out.="<a href=\"".LK_PAG.$this->url_encrypt("pg=imgconfig&dir_gal="._TPLDIR._THEME_DIR."/"._DirIMAGES."")."\" >"._IMAG."</a>";
$this->html_out .="</td><td class=\"lista\" width=\"450px\">"._CMT_IMAG."</td><td class=\"lista\">";

$this->html_out .="</td></tr></table>";


?>
