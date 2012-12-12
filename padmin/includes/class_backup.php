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

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_backup.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_mysql.php");
/**
 *Backup Handling to XML file
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2005 by Josep Marxuach
 *@package Database
 */
class mysql_backup extends DB_Sql {

	var $filename;            // file name(sqldata.txt)
	var $structure_only;    // Output method : true/false
	var $fptr;              // Do Not change this.
	var $charset;


	/**
	 * Constructor function: This will Inisialize variables.
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $structure_only
	 * @return mysql_backup
	 */
	function mysql_backup(){
		set_time_limit (1200);
		$this->charset="UTF-8";
	}

	/**
	 * This will create the sqldata.txt file.
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $structure_only
	 * @return unknown
	 */
	function MMysqlbackup($filename, $structure_only){
		if (strval($this->filename)!="") $this->fptr=fopen($this->filename,"w"); else $this->fptr=false;
		$data="<?xml version=\"1.0\" encoding=\"".$this->charset."\"?>\n<root>\n";
		if ($this->fptr!=false) fwrite($this->fptr,$data);
		$tables=$this->table_names();
		foreach($tables as $tb) {
			$data="";
			if ($this->query("select * from ".$tb["table_name"])){
				$nodeName = substr($tb["table_name"],strlen($this->prefix)+1,strlen($tb["table_name"]));
				$data.="<".$nodeName.">\n";
				$rows=$this->select_xml($this->table_columns($tb["table_name"]),false);
				$data.="$rows";
				$data.="</".$nodeName.">\n";
				if ($this->fptr!=false) fwrite($this->fptr,$data);
			}
		}
		$data="</root>";
		//echo $data;
		if ($this->fptr!=false) fwrite($this->fptr,$data);
		if ($this->fptr!=false) fclose($this->fptr);
		return 0;
	}

	/**
	 * Main method. Executes the backup.
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $structure_only
	 * @return unknown
	 */
	function Backup($filename,$structure_only){
		$this->filename = $filename;
		$this->structure_only = $structure_only;
		$this->MMysqlbackup($this->filename,$this->structure_only);
		return 1;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $FileName File Name with path
	 */
	function Import($FileName){

		$dom = new DOMDocument;
		$dom->load($FileName);

		//create the xPath object _after_  loading the xml source, otherwise the query won't work:
		$xPath = new DOMXPath($dom);

		//now get the nodes in a DOMNodeList:
		$TableList = $xPath->query("//central/*|//root/*");
		$NoError = True;		
		
			foreach ($TableList as $ElemTable){

				$RowList = $xPath->query("//central/".$ElemTable->nodeName."/*|//root/".$ElemTable->nodeName."/*");
					
				foreach ($RowList as $ElemRow){
					
					$FieldList = $ElemRow->childNodes;
					$first = true;
					$strNames = "(";
					$strValues = "(";
					foreach ($FieldList as $ElemField) {
						$type = $ElemField->attributes->item(0)->nodeValue;
						if ($first) $strNames.=$ElemField->nodeName;
						else $strNames.=",".$ElemField->nodeName;
						if ($type=="string" || $type=="varchar" || $type=="char" || $type=="date" || $type=="text")
						$value = "'".mysql_real_escape_string($ElemField->nodeValue)."'";
						else
						$value = $ElemField->nodeValue;
						if ($first) {
							$strValues .= $value;
						} else {
							$strValues .= ",".$value;
						}
						$first = false;
					}
					$strNames .= ")";
					$strValues .= ")";
					//echo "insert into ".$this->prefix."_".$ElemTable->nodeName." $strNames values $strValues;<br/>";
					if (!$this->query("insert into ".$this->prefix."_".$ElemTable->nodeName." $strNames values $strValues;"))
					$NoError = false;
				}
			}
			return $NoError;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $FileName File Name with path
	 */
	/*
	 function Import($FileName){

		// open the XML-file
		$xml = domxml_open_file($FileName);

		// get the root element
		$root = $xml->document_element();
		// get the list of the nodes
		$TableList = $root->child_nodes();

		$NoError = True;
		foreach ($TableList as $ElemTable){

		//$RowList = $xPath->query("//root/".$ElemTable->nodeName."/*");
		$RowList = $ElemTable->child_nodes();

		foreach ($RowList as $ElemRow){
		$FieldList = $ElemRow->child_nodes();
		$first = true;
		$strNames = "(";
		$strValues = "(";
		foreach ($FieldList as $ElemField) {
		$type = $ElemField->get_attributte("type");
		if ($first) $strNames.=$ElemField->node_name();
		else $strNames.=",".$ElemField->node_name();
		if ($type=="string" || $type=="varchar" || $type=="char" || $type=="date" || $type=="text")
		$value = "'".mysql_real_escape_string($ElemField->node_value())."'";
		else
		$value = $ElemField->node_value();
		if ($first) {
		$strValues .= $value;
		} else {
		$strValues .= ",".$value;
		}
		$first = false;
		}
		$strNames .= ")";
		$strValues .= ")";
		//echo "insert into ".$this->prefix."_".$ElemTable->nodeName." $strNames values $strValues;<br/>";
		if (!$this->query("insert into ".$this->prefix."_".$ElemTable->node_name()." $strNames values $strValues;"))
		$NoError = false;
		}
		}
		return $NoError;
		}*/
	//--------------------------------------------------------
}
?>
