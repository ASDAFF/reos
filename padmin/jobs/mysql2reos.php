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
 * Convert XML file from MysqlBrowser export to ReOS xml import files 
 * 
 */
$FileName = "install/cities_ok.xml";
$TableName = "poblacion";

error_reporting(E_ALL);
$dom = new DOMDocument;
$dom->load($FileName);

/*create the xPath object _after_  loading the xml source, otherwise the query won't work:*/
$xPath = new DOMXPath($dom);

/*now get the nodes in a DOMNodeList:*/
$nodeList = $xPath->query("//ROOT/row");

/*create a new DOMDocument and add a root element:*/
$newDom = new DOMDocument('1.0','UTF-8');
$root = $newDom->createElement('root');
$table = $newDom->createElement($TableName);
$root = $newDom->appendChild($root);
$root = $root->appendChild($table);


/* append all nodes from $nodeList to the new dom, as children of $root:*/
foreach ($nodeList as $domElement){
	//$domNode = $newDom->importNode($domElement, true);
	$row = $newDom->createElement('row');

	$children = $domElement->getElementsByTagName( "field" );

	foreach ($children as $child) {
		//$row->appendChild();
		/*foreach ($child->attributes as $attrName => $attrNode) {
				echo $attrNode->nodeValue." ";
				$field = $newDom->createElement($attrNode->nodeValue);
		}
		*/
		$field = $newDom->createElement($child->attributes->item(0)->nodeValue);
		$field->nodeValue = $child->nodeValue;
		//echo $child->nodeValue."<br/>";
		$row->appendChild($field);
	}
	$root->appendChild($row);
}
/*please note: importNode does not cast a DOMElement to a DOMNode!*/

/*save the new dom */
echo 'Wrote: ' . $newDom->save('install/newDOM.xml') . ' bytes';
?>
