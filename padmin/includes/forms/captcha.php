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

session_start();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

function _generateRandom($length=6){
	$_rand_src = array(
	array(48,57) //digits
	, array(97,122) //lowercase chars
	//        , array(65,90) //uppercase chars
	);
	srand ((double) microtime() * 1000000);
	$random_string = "";
	for($i=0;$i<$length;$i++){
		$i1=rand(0,sizeof($_rand_src)-1);
		$random_string .= chr(rand($_rand_src[$i1][0],$_rand_src[$i1][1]));
	}
	return $random_string;
}

$im = imagecreatefromjpeg("captcha.jpg");

$rand1 = _generateRandom(6);
ImageString($im, 5, 2, 2, $rand1, ImageColorAllocate ($im, 0, 0, 0));
$_SESSION['co9k383'] = $rand1;
/*
$rand1 = _generateRandom(3);
ImageString($im, 5, 2, 2, $rand1[0]." ".$rand1[1]." ".$rand1[2]." ", ImageColorAllocate ($im, 0, 0, 0));
$rand2 = _generateRandom(3);
ImageString($im, 5, 2, 2, " ".$rand2[0]." ".$rand2[1]." ".$rand2[2], ImageColorAllocate ($im, 255, 0, 0));
$_SESSION['captcha'] = $rand1.$rand2;
*/
Header ('Content-type: image/jpeg');
imagejpeg($im,null,100);
ImageDestroy($im);
?>