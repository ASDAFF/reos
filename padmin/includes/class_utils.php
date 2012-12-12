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
 *Class Utils file
 *@author IT eLazos SL  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_utils.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 *Class Database file
 *Utils extends DB_Sql class.
 **/
require_once(_DirINCLUDES."class_mysql.php");
/**
 *Adds somes utilities like encryptation, image resize, save image to folder, delete folder, check date format, etc...
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by IT eLazos SL
 *@package Site
 */
class utils extends DB_Sql  {
	/**
	 * Crypt.
	 * If 1 = yes All Url "GET" are crypted, 0 =  No
	 *@var Integer
	 */
	var $cryptar = _CRYPT_LINKS;
	/**
	 * Random.
	 * Si 1 = encrypta con un numero final random rand(1,100) para que sean siempre diferentes, 0 =  Se encripta sin Rand
	 * @var boolean
	 */
	var $Random = False;
	var $iv64="cx0DjR012c0PACRAvpm01LojVUjvUL0cH9Yyp5/WMYc=";
	var $ekey = "a7bc27daf59679de9db7b68b1ef92785";

	/**
	 * Encryptar una url.
	 * Recoge todas variables de una URL y las encrypta. Devuelve una variable URL=texto emcriptado.
	 *@param string Vars de una URL para encriptar
	 **/
	function url_encrypt($text){

		if ($this->cryptar) {
			$text="url".$text;
			if ($this->Random) $text.="#".rand(1,100);else $text.="#00";

			$text=$this->ecrypt($text);
			$text="url=".base64_encode($text);
		} else $text = htmlspecialchars($text);// = urlencode($text); //$text = htmlentities($text);

		return $text;
	}
	/**
	 * Desencrypta una url.
	 *@param string Texto a encriptar.
	 **/
	function url_decrypt($text){
		if ($this->cryptar){
			$text=$this->txt_decrypt($text);
			if (substr($text, 0, 3)=="url") {

				$text=rtrim($text);
				$text=substr($text, 3, (strrpos($text,"#")-3));

				if ($pos=strpos($text,"&ref=")){
					$url=substr($text, 0, $pos);
					$referer=substr($text,(strpos($text,"http://")), strlen($text));
				} else $url=$text;
				//echo $referer."................";
				parse_str($url,$result);
				if (isset($referer)) $result["referer"]=$referer;

				return $result;
			} else return FALSE;
		} else {
			parse_str($text,$result);
			return $result;
		}
	}
	/**
	 * Encrypts to be used in forms
	 *
	 *@param string URL para  desencriptar
	 **/
	function txt_encrypt($text){
		if ($this->cryptar) {
			$text="url".$text;
			if ($this->Random) $text.="#".rand(1,100);else $text.="#00";
			$text=$this->ecrypt($text);
			$text=base64_encode($text);
		}
		return str_replace("&",",",$text);
	}
	/**
	 * Decrypts text
	 *@param string Texto a desencriptar.
	 **/
	function txt_decrypt($text){
		if ($this->cryptar){
			$text=base64_decode($text);
			$text=$this->dcrypt($text);
		}
		return $text;
	}
	/**
	 * Encripta un texto.
	 *@param string texto a encryptar.
	 **/
	function ecrypt($text) {
		$td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
		$iv=base64_decode($this->iv64);
		mcrypt_generic_init($td, $this->ekey, $iv);
		$encrypted = mcrypt_generic($td, $text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return urlencode($encrypted); //Poner si hay problemas con el servidor http
		//return $encrypted;
	}
	/**
	 * Decripta un texto.
	 *@param string texto a desencryptar
	 **/
	function dcrypt($encrypted){
		$encrypted=urldecode($encrypted); //poner si hay problemas con el servidor http
		$td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
		$iv=base64_decode($this->iv64);
		mcrypt_generic_init($td, $this->ekey, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		//echo trim($decrypted)."<br/>";
		return trim($decrypted);
	}
	/**
	 *Change image size used by another method save_image
	 *@author Josep Marxuach
	 *@access private
	 **/
	function img_resize($imagen, $destino, $max_pixels, $SelectSide=true){
		/* TIPOS DE IMAGENES
		 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel),
		 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC,
		 14 = IFF, 15 = WBMP, 16 = XBM.
		 */

		if (file_exists($imagen)) {

			if (!$tam = getimagesize($imagen)) return false ; //Devuelve una array con varios datos

			if ($tam[2]!=2) return false; // no es jpg

			if (!$img_origen = imagecreatefromjpeg($imagen)) return false;

			//$x=imagesx($img_origen);
			//$y=imagesy($img_origen);

			$x=$tam[0];
			$y=$tam[1];
			if ($SelectSide){
				if ($x>$y)
				{ $nova_largura = $max_pixels; $nova_altura = $max_pixels * $y / $x;}
				else
				{ $nova_altura = $max_pixels; $nova_largura = $max_pixels * $x / $y;}
			} else {
				$nova_largura = $max_pixels; $nova_altura = $max_pixels * $y / $x;
			}
			$img_destino = imagecreatetruecolor($nova_largura,$nova_altura);
			imagecopyresampled($img_destino,$img_origen,0,0,0,0,$nova_largura,$nova_altura, $x, $y);
			imagejpeg($img_destino,$destino);
			return TRUE;
		} else return FALSE;
	}
	/**
	 * Cambia el tamany de una imatge
	 **/
	function save_imagen($imagen_old, $imagen, $origen, $destino, $max_pixels){

		if ($imagen && !preg_match("/[^A-Za-z0-9._]/", $imagen)) {

			if (!$this->img_resize($origen.$imagen, $destino.$imagen, $max_pixels, false))
			{unlink($origen.$imagen); return false;} else unlink($origen.$imagen);  // borro el fichero de temporal
			if (isset($imagen_old)
			&& $imagen_old!=""
			&& file_exists($destino.$imagen_old))
			unlink($destino.$imagen_old);
			return true;      // borro el fichero de anterior
		} else return false;
	}
	/**
	 * Cambia el formato de la fecha para que se pueda introducir en una base de datos SQL
	 * El formato SQL es YYYYMMDD
	 **/
	function date_sql_format($date,$current_format){

		if (!isset($date) || $date=="") return false;

		if (strchr($date,"-")) $date=preg_replace("/-/","/",$date);

		if (strchr($date,".")) $date=preg_replace("/\./","/",$date);

		if ($current_format=="d/m/Y" || $current_format=="d/m/y")
		list($day,$month,$year) = explode("/",$date);
		else list($month, $day, $year) = explode("/",$date);

		if (strlen($year)!=4) return false;
		//if ($year<=99)
		//if ($year<65) $year="20$year"; else $year="19$year";

		//echo $day.$month.$year;
		if (!checkdate($month,$day,$year)) return false;

		return sprintf ("%4s%02s%02s", $year, $month, $day);
	}
	/**
	 * Borrar un directorio este lleno o no
	 **/
	function deldir($dir){

		if (!$current_dir = opendir($dir)) return false;

		while($entryname = readdir($current_dir)){
			if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
				$this->deldir("$dir/$entryname");
			}elseif($entryname != "." and $entryname!=".."){
				unlink("$dir/$entryname");
			}
		}
		closedir($current_dir);
		rmdir($dir);
		return true;
	}
	/**
	 * Devuelve una lista de valores de un array separados por coma
	 **/
	function array2list($array_values,$separator){
		reset($array_values);
		$result="";
		foreach($array_values as $value)
		if (is_array($value)){
			$result2="";
			foreach($value as $value2 )
			if ($result2=="") $result2=$value2;else $result2=$result2.";".$value2;
			if ($result=="") $result=$result2;else $result=$result."$separator".$result2;
		} else
		{ if ($result=="") $result=$value;else $result=$result."$separator".$value;}
		return $result;
	}
	/**
	 * Reverse of parse_str().  Converts array into
	 * string with query format
	 **/
	function rev_parse_str ($params) {
		$str = '';
		foreach ($params as $key => $value) {
			$str .= (strlen($str) < 1) ? '' : '&';
			$str .= $key . '=' . rawurlencode($value);
		}
		return ($str);
	}
	/**
	 * Enter description here...
	 *
	 */
	function getPermalink($name = LK_PAG){

		if (!defined("_PERMALINKS")){
			$MakePermalinks = false;
		} else {
			$MakePermalinks = _PERMALINKS;
		}
		if ($MakePermalinks) {
			// replace accented chars
			$accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/';
			$string_encoded = htmlentities($name,ENT_NOQUOTES,'UTF-8');

			$string = preg_replace($accents,'$1',$string_encoded);
			// clean out the rest
			$replace = array('([\40])','(\'|,|%)','([^a-zA-Z0-9-])','(-{2,})');
			$with = array('-','-','','-');
			$string = strtolower(preg_replace($replace,$with,$string)).".html?";

			return $string;
		} else return LK_PAG;
		
	}


	//************************************FIN CLASSE
}
?>
