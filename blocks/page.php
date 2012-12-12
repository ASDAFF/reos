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
*Returns xml node with the content from xml file.
*Content file is defined on navigator file attribute xml.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/page.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $htm;
global $nm;

if (!isset($htm) && array_key_exists("htm",$this->vars)) $htm=$this->vars["htm"];

$this->html_out .= $this->pgtitle($nm,true,NULL);
/*
$this->html_out .= "<content>";
if (file_exists(_TPLDIR."/"._THEME_DIR."/"._DirHTMLS.$htm."."._IDIOMA.""))
       $this->html_out .= file_get_contents(_TPLDIR."/"._THEME_DIR."/"._DirHTMLS.$htm."."._IDIOMA."");
       else $this->html_out .=""._NO_AV_LG."";
$this->html_out .= "</content>";
*/

if (file_exists(_TPLDIR."/"._THEME_DIR."/"._DirHTMLS.$htm."."._IDIOMA.".xml")) {
	// If php v4	
	if (substr(phpversion(),0,1)=="4") {
		$xp = xslt_create();
		xslt_set_encoding($xp,$this->charset);
		$arguments = array ('/_xml' => file_get_contents(_TPLDIR."/"._THEME_DIR."/"._DirHTMLS.$htm."."._IDIOMA.".xml"));
		$this->html_out .=xslt_process($xp, 'arg:/_xml', _DirINCLUDES.'content.xsl',NULL,$arguments);
		$msg_error = xslt_error($xp);
		xslt_free($xp);
	}
	// If php v5
	if (substr(phpversion(),0,1)=="5") {
		$xp = new XsltProcessor();

		// create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load( _DirINCLUDES.'content.xsl');
		$xsl->getElementsByTagName('output')->item(0)->setAttribute('encoding',_CHARSET);

		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		$xml_doc = new DomDocument;
		$xml_doc->loadXML(file_get_contents(_TPLDIR."/"._THEME_DIR."/"._DirHTMLS.$htm."."._IDIOMA.".xml"));
		$this->html_out .= $xp->transformToXML($xml_doc);
	}
} else $this->html_out .=""._NO_AV_LG."";

?>
