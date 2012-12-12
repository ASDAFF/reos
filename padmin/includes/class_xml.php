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
 *Handles xml files
 *creates a array from a xml file
 *Example : $xml= new xml;$tree = $xml->GetXMLTree('nav2.xml');print_r($tree);
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by Josep Marxuach
 *@package xml
 */
class xml {
	function GetChildren($vals, &$i){
		$children = array();     // Contains node data

		/* Node has CDATA before it's children */
		if (isset($vals[$i]['value']))
		$children['VALUE'] = $vals[$i]['value'];

		/* Loop through children */
		while (++$i < count($vals)){
			switch ($vals[$i]['type'])
			{
				/* Node has CDATA after one of it's children
				 (Add to cdata found before if this is the case) */
				case 'cdata':
					if (isset($children['VALUE']))
					$children['VALUE'] .= $vals[$i]['value'];
					else
					$children['VALUE'] = $vals[$i]['value'];
					break;
					/* At end of current branch */
				case 'complete':
					if (isset($vals[$i]['attributes'])) {
						$children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
						$index = count($children[$vals[$i]['tag']])-1;

						if (isset($vals[$i]['value']))
						$children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value'];
						else
						$children[$vals[$i]['tag']][$index]['VALUE'] = '';
					} else {
						if (isset($vals[$i]['value']))
						$children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value'];
						else
						$children[$vals[$i]['tag']][]['VALUE'] = '';
					}
					break;
					/* Node has more children */
				case 'open':
					if (isset($vals[$i]['attributes'])) {
						$children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
						$index = count($children[$vals[$i]['tag']])-1;
						$children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index],$this->GetChildren($vals, $i));
					} else {
						$children[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);
					}
					break;
					/* End of node, return collected data */
				case 'close':
					return $children;
			}
		}
	}

	/* Function will attempt to open the xmlloc as a local file, on fail it will attempt to open it as a web link */
	function GetXMLTree($xmlloc){
		if (file_exists($xmlloc))
		$data = implode('', file($xmlloc));
		else {
			return false;
			//$fp = fopen($xmlloc,'r');
			//$data = fread($fp, 100000000);
			//fclose($fp);
		}

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $vals, $index);
		xml_parser_free($parser);

		$tree = array();
		$i = 0;

		if (isset($vals[$i]['attributes'])) {
			$tree[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
			$index = count($tree[$vals[$i]['tag']])-1;
			$tree[$vals[$i]['tag']][$index] =  array_merge($tree[$vals[$i]['tag']][$index], $this->GetChildren($vals, $i));
		}
		else
		$tree[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);

		return $tree;
	}
	// END CLASS
}

?>
