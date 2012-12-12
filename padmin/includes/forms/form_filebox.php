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


require_once("form_field.php");
/**
 * Creates a filebox in html
 * @author   IT Elazos SL
 * @version  1.0
 * @package  Forms
 */
class form_filebox extends form_field {
	var $maxsize;
	var $uploadfolder;
	var $image_uploaded;


	function form_filebox( $form_name, $field, $title, $size, $maxsize, $uploadfolder )
	{
		$this->form_name = $form_name;
		$this->field = $field;
		$this->title = $title;
		$this->size = $size;
		$this->maxsize = $maxsize;
		$this->uploadfolder = $uploadfolder;
		$this->key = $this->form_name . "_" . $this->field;
		$this->cssclass = "field_filebox";
	}

	function get_string(){
		if( strlen($this->onblur) ) $javascript = "onblur=\"{$this->onblur}\"";
		else $javascript="";
		/*if( !empty($this->title) ) $ret = $this->title."";
		 else $ret = "";*/ $ret="";
		return $ret."<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"{$this->maxsize}\"/><input type=\"file\" class=\"{$this->cssclass}\" name=\"{$this->key}\" size=\"{$this->size}\" $javascript {$this->tag_extra}/>\n";
	}

	function process(){

		if (isset($_FILES["$this->key"]['tmp_name']) && $_FILES["$this->key"]['tmp_name']!="" && is_uploaded_file($_FILES["$this->key"]['tmp_name']) ){

			// Making file name writable
			$accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/';
			$string = preg_replace($accents,'$1',$_FILES["$this->key"]['name']);
			// clean out the rest
			$replace = array('([\40])','(\'|,|%)','([^a-zA-Z0-9.])','(-{2,})');
			$with = array('_','_','','_');
			$string = strtolower(preg_replace($replace,$with,$string));			
			$image=substr(md5(uniqid("")), 0, 4).$string;
			// End Filename Writable
			
			if (file_exists($this->uploadfolder.$image)) unlink($this->uploadfolder.$image);
			$this->image_uploaded=$image;
			if (!move_uploaded_file($_FILES["$this->key"]['tmp_name'], $this->uploadfolder.$image)){
				$this->image_uploaded=false;
			} else
				$this->value=$image;

		} else $this->image_uploaded=false;
	}
}
?>
