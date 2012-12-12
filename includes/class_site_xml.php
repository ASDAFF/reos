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
 * Class Site definition file
 * See class site documentation
 * @author IT eLazos S.L. - May 2005
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/class_site_xml.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class page definition file
 * Incluye el fichero de declaraci�n de la clase page
 **/
require_once(_DirINCLUDES."class_page.php");
/**
 * Classe site extends classe page
 * Contiene la definici�n de la clase site que extiende la clase page. La clase site es la clase principal de la web publica.
 * Las funciones que realiza son:
 * 1. Modifica la clase page en el sentido que a�ade metodos especificos para la web p�blica.
 * 2. Basa su salida en XML a diferencia de page que basa su salida en HTML
 * 3. Debido al punto 2 realiza la transformaci�n XSL
 * @author Josep Marxuach
 * @version 1.0
 */
class site extends page {
	/**
	 * Returns the xml output of the application
	 * Is the main method called from index file. Create the xml nodes headers, footers, bleft, bright
	 * and executes php files defined in navigator file
	 * @author Josep Marxuach
	 * @access public
	 */
	function out(){

		if (count($this->headers)!=0 || array_key_exists("headers",$this->page_mnu)){
			$header="<headers>\n";
			if (array_key_exists("headers",$this->page_mnu) && count($this->page_mnu["headers"])>0)
			$header.=$this->exec_mnu($this->page_mnu["headers"]);
			$header.=$this->exec_blocks($this->headers)."</headers>\n";
		}else $header="";
		if (count($this->blocki)!=0 || array_key_exists("bleft",$this->page_mnu)){
			$bloci="<bleft>\n";
			if (array_key_exists("bleft",$this->page_mnu) && count($this->page_mnu["bleft"])>0)
			$bloci.=$this->exec_mnu($this->page_mnu["bleft"]);
			$bloci.=$this->exec_blocks($this->blocki)."</bleft>\n";
		} else $bloci="";
		if (count($this->blockd)!=0 || array_key_exists("bright",$this->page_mnu)){
			$blocd="<bright>\n";
			if (array_key_exists("bright",$this->page_mnu) && count($this->page_mnu["bright"])>0)
			$blocd.=$this->exec_mnu($this->page_mnu["bright"]);
			$blocd.=$this->exec_blocks($this->blockd)."</bright>\n";
		}else $blocd="";
		if (count($this->footers)!=0 || array_key_exists("footers",$this->page_mnu)) {
			$footer="<footers>\n";
			if (array_key_exists("footers",$this->page_mnu) && count($this->page_mnu["footers"])>0)
			$footer.=$this->exec_mnu($this->page_mnu["footers"]);
			$footer.=$this->exec_blocks($this->footers)."</footers>\n";
		}else $footer="";

		$central=$this->exec_blocks($this->central);
		//if ($this->show_msg) $central=$this->messages().$central;
		$central="<central>\n".$central."</central>\n";

		// if we want to sent xml output 
		if (isset($this->xout)) {
			$this->charset=_CHARSET;
			$this->send_headers();
			header("Content-Type: text/xml");
  		    echo "<?xml version=\"1.0\" encoding=\"".$this->charset."\"?>\n$central";
  		    exit;
		}
		
		$out_string=$this->head();

		$path=substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],"/"));
		
		if ($this->onload!="") $this->onload="onload=\"".$this->onload."\"";
		$out_string .= "<page ".$this->onload." lang=\""._IDIOMA."\" path=\"".$path."\">\n";
		if ($this->align_center) $align="align=\"center\"";else $align="";
		if (array_key_exists("page",$this->page_mnu) && count($this->page_mnu["page"])>0) $out_string.=$this->exec_mnu($this->page_mnu["page"]);
		if ($this->show_msg) $out_string.=$this->messages();
		$out_string .=$header;
		$out_string .=$bloci;
		$out_string .= $central;
		$out_string .= $blocd;
		$out_string .= $footer;
		//$out_string .= $this->tmp;
		$out_string .= "</page>\n"
		."</html>";
		
		

		if (defined("_XMLOUTPUT2FILE") && _XMLOUTPUT2FILE==true) {
			$handle = fopen(realpath(_TPLDIR)."/"._THEME_DIR."/output.xml", "wb");
			fwrite($handle, $out_string);
			fclose($handle);
		}

		// If php v4
		if (substr(phpversion(),0,1)=="4") {
			$xp = xslt_create();
			xslt_set_encoding($xp,$this->charset);
			$arguments = array ('/_xml' => $out_string);
			$result=xslt_process($xp, 'arg:/_xml', _TPLDIR._THEME_DIR.'/'.$this->file_xslt.'.xsl',NULL,$arguments);
			$msg_error = xslt_error($xp);
			xslt_free($xp);
		}
		// If php v5
		if (substr(phpversion(),0,1)=="5") {
			$xp = new XsltProcessor();

			// create a DOM document and load the XSL stylesheet
			$xsl = new DomDocument;
			$xsl->load(_TPLDIR._THEME_DIR.'/'.$this->file_xslt.'.xsl');
			eval("\$xsl->getElementsByTagName('output')->item(0)->setAttribute('encoding',_CHARSET);");
			// import the XSL styelsheet into the XSLT process
			$xp->importStylesheet($xsl);
			$xml_doc = new DomDocument;
			$xml_doc->loadXML($out_string);	
			$result = $xp->transformToXML($xml_doc);
		}

		$this->send_headers();
		
		if ($result) echo $result;
		else echo $msg_error;	
		

		//echo $out_string;
		if (count($this->emails)>0) $this->sendemail();
	}
	/**
	 * Returns a html table with all messages saved during execution.
	 * This method is the same of class page but returning xml.
	 * To add a message you have to use method add_message of page class.
	 * Returns xml into the class var html_out.
	 * @author Josep Marxuach
	 * @access private
	 */
	function messages(){
		$out_string="";
		$out_string .= "<msg>";
		if (is_array($this->vars_ste) && $this->vars_ste) {
			reset($this->vars_ste);
			$out_string .= "<position>";
			while (list($key,$msg)=each($this->vars_ste)){

				if (isset($msg["nm"])) $name=$msg["nm"];else $name=constant(strtoupper("_".$msg["pg"]));

				if ($msg["pg"]!=$this->vars["pg"] && isset($msg["url"]))
				{if ($msg["url"]!="") $out_string .= "<pos href=\"".LK_PAG.htmlspecialchars($msg["url"])."\">$name</pos>";
				else $out_string .= "<pos href=\"".LK_HOME."\">$name</pos>";}
				else $out_string .= "<pos>".$name."</pos>";
			}

			$out_string .= "</position>";
		}

		reset($this->page_msg);
		if ($this->page_msg){
			$out_string .= "<alerts>";
			while (list($key,$msg)=each($this->page_msg))
			$out_string .= "<msg>"._MSG." : $msg</msg>";
			$out_string .= "</alerts>";
		}
		$out_string .= "</msg>";
		return $out_string;
	}
	/**
	 * Returns a string with page menus from navigator file.
	 * @author Josep Marxuach
	 * @access public
	 * @param array  array["title"]=title of the menu, array["link"]=link to be encrypted, array["href"]=link not to encrypt
	 */
	function exec_mnu($mnus){
		$out="";
		foreach($mnus as $href) {
			if (array_key_exists("title",$href)){
				$title="title=\"".htmlspecialchars(constant(strtoupper("_".$href["title"])))."\"";unset($href["title"]);}else $title="";
				if (array_key_exists("name",$href)) {$name_mnu=$href["name"];unset($href["name"]);}else $name_mnu="mnu";
				$out.="<$name_mnu $title>\n";

				foreach($href as $value){
					$txt="";
					if ($value["txt"][0]=="\"") $txt=htmlspecialchars(str_replace("\"","",$value["txt"]));
					else $txt=htmlspecialchars(constant(strtoupper("_".$value["txt"])));
					if (array_key_exists("name",$value)) $name=$value["name"];else $name="item";
					if (array_key_exists("link",$value)) $out.="<$name href=\"".$this->getPermalink($txt)."".$this->url_encrypt($value["link"]."&nm=$txt")."\">$txt</$name>\n";
					if (array_key_exists("href",$value)) $out.="<$name href=\"".$value["href"]."\">$txt</$name>\n";
				}
				$out.="</$name_mnu>\n";
		}
		return $out;
	}
	/**
	 * Returns xml that represents page title with menu options.
	 * @author Josep Marxuach
	 * @access public
	 * @param string Title of the page
	 * @param bool Enables back button. True is enabled, false no.
	 * @param array Menu options in the following format array[#option][action]. Option is numeric sequence.
	 * Action can be "href"(page vars non encripted), "confirm"(creates a popup when clic),"print"(create a hp for printing page),
	 * and "popup"(includes a target blank on anchor).
	 */
	function pgtitle($title,$back=True, $links=NULL){
		if ($this->pg_title) {
			$out  = "<pgtitle>";
			$out .= "<title>$title</title>";
			$out .= "<options>";


			if ($back) {
				if (isset($this->vars_ste)) $state=$this->vars_ste; else $state=Null;
				$num=count($state);
				//print_r($state);
				if (is_array($state) && array_key_exists($num-2,$state))
				{ if ($state[$num-2]["url"]!="")  $go=LK_PAG.htmlspecialchars($state[$num-2]["url"]);else $go=LK_HOME;}
				else $go="javascript:history.go(-1)";

				$out .= "<back href=\"$go\">"._BACK."</back>";

			}

			if (is_array($links))
			foreach ($links as $lnk) {
				$onclick=false;$target=false;$link_pag="".LK_PAG."";$url="";
				if (array_key_exists("popup",$lnk)) {$target="target=\"_blank\"";$link_pag=$lnk["popup"];}
				if (array_key_exists("confirm",$lnk)) $onclick="onclick=\"return confirm('".$lnk["confirm"]."')\"";
				if (array_key_exists("print",$lnk)) {$lnk["href"]=$this->rev_parse_str($this->vars)."&prt="._NAVPRT;$target="target=\"_blank\"";
				$link_pag=LK_PAG;}

				if (array_key_exists("href",$lnk)) $url=$this->url_encrypt("".$lnk["href"]."");
				$out .= "<item href=\"$link_pag$url\"";
				if ($target)$out .= " $target";else $out .= " target=\"_self\"";
				if ($onclick) $out .= " $onclick"; else  $out .= " onclick=\"\"";
				$out .= ">".$lnk["txt"]."</item>";
			}

			$out .= "</options></pgtitle>";
			return $out;
		} else return "";
	}
	/**
	 * Returns xml that represents an hyperlink (html link)
	 * @author Josep Marxuach
	 * @access public
	 * @param string href or Link
	 * @param string label of the hyperlink
	 * @param string CSS class name
	 * @param boolean if true encrypts link
	 */
	function html_link($link, $txt, $class=null,$enc=true){
		if (isset($class)) $class="class=\"$class\""; else $class="";
		if ($enc) $enc="".$this->url_encrypt("".$link."&nm=$txt")."";else $enc=$link;
		if ($enc=="") $enc="".LK_HOME."";else $enc="".LK_PAG."".$enc."";

		return "<a><href>".$enc."</href><label>".$txt."</label></a>";
	}
	/**
	 * Return the header section of the xml output.
	 * returns also the html header of the page within the xml. This is because does affect to XSL transformation.
	 * returns as well html script node if class var file_script is set.
	 * It uses many class vars, like charset, etc...
	 * @author Josep Marxuach
	 * @access private
	 * @return string xml generated by this method
	 */
	function head(){
		//$out_string = "";
		$this->charset=_CHARSET;
		$out_string  = "<?xml version=\"1.0\" encoding=\"".$this->charset."\"?>\n";
		$out_string .= "<html>\n";
		$out_string .= "<head>\n";
		if (strlen($this->pagetitle)==0)
		    $out_string .= "<title>".$this->sitename."</title>\n";
		    else
		    $out_string .= "<title>".$this->pagetitle." - ".$this->sitename."</title>\n";

		//$out_string .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$this->charset."\"/>\n";
		$out_string .= "<meta http-equiv=\"CONTENT-LANGUAGE\" content=\""._IDIOMA."\"/>";
		$out_string .= "<meta http-equiv=\"EXPIRES\" content=\"0\"/>\n";
		$out_string.=  "<meta name=\"RESOURCE-TYPE\" content=\"DOCUMENT\"/>\n";
		$out_string .= "<meta name=\"DISTRIBUTION\" content=\"GLOBAL\"/>\n";
		$out_string .= "<meta name=\"AUTHOR\" content=\"".$this->sitename."\"/>\n";
		$out_string .= "<meta name=\"COPYRIGHT\" content=\"Copyright (c) ".date("Y")." by ".$this->sitename."\"/>\n";
		$out_string .= "<meta name=\"KEYWORDS\" content=\"".$this->keywords."\"/>\n";
		$out_string .= "<meta name=\"DESCRIPTION\" content=\"".$this->site_slogan."\"/>\n";
		$out_string .= "<meta name=\"ROBOTS\" content=\"INDEX, FOLLOW\"/>\n";
		$out_string .= "<meta name=\"REVISIT-AFTER\" content=\"1 DAYS\"/>\n";
		$out_string .= "<meta name=\"RATING\" content=\"GENERAL\"/>\n";
		$out_string .= "<meta name=\"GENERATOR\" content=\"Copyright ".date("Y")." by ".$this->sitename."\"/>\n";

		if (isset($this->file_script)) $out_string .= "<script language=\"javascript\" type=\"text/javascript\" src=\""._DirADMIN."/".$this->file_script."\"> </script>\n";

		//$out_string .= "<link rel=\"StyleSheet\" href=\"".$this->file_CSS."\" type=\"text/css\"/>\n";

		$out_string .= "</head>\n";
		return $out_string;
	}

	
} //end class
?>
