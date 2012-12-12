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
 * ReOS config file creation
 */
function create_deffile($file,$defs) {

	$lst_idiomas = array ("en"=>"English","en-us"=>"English US","es"=>"Spanish","fr"=>"French","nl"=>"Ducth","ca"=>"Catalan","ru"=>"Russian", "de"=>"German","tr"=>"Turkish","zh-tw"=>"Chinese-tw","zh-cn"=>"Chinese-cn");
	unset($defs["submit"]);
	
	if	($handle = fopen($file, "w")) {
		$str="<?php
\n\n//File generated from a txt file at : ".date("r")."\n\n"
		."error_reporting(0);\n\n";
		fwrite($handle,$str);
		 
		$notdefines=array("langs","op");

		foreach ($defs as $key=>$value) {
			//** starts LST's
			if (!in_array($key,$notdefines)) {
				if ($key=="_IMAGE_SIZE" || $key=="_THUMB_SIZE") $str="define(\"$key\",$value);\n";
				else $str="define(\"$key\",\"$value\");\n";
				fwrite($handle,$str);
			}
			//** ends LST's
		}
		$str="?>";
		fwrite($handle,$str);
		fclose($handle);
		return true;
	} else {
		return false;
	}
}

?>
