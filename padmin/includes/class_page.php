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
 *Class Page definition file
 *@author Josep Marxuach  - May 2004
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_page.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 *Class Authentification definition file
 **/
require_once(_DirINCLUDES."class_auth.php");
require_once(_DirINCLUDES."class_xml.php");
/**
 * Handles page layout.
 * Executes php files in a layout structure of blocks header, Blocks left, Blocks central, Blocks Right and Blocks footer.
 * Using add_<blocks> methods you add php file to the layout and you execute using page->out.
 * Page->out method returns the generated html page.
 * @author Josep Marxuach
 * @version 1.0
 * @copyright 2004 by Josep Marxuach
 * @package Site
 */
class page extends Auth{
	var $sitename="";
	var $pagetitle;
	var $onload="";
	var $site_address="";
	var $site_slogan="";
	var $file_CSS="style/style.css";
	var $file_nav=_NAVFILE;
	var $file_xslt;
	var $file_script=NULL;
	var $charset="UTF-8"; //ISO-8859-1
	var $keywords="";
	var $xout;

	var $html_out;
	var $headers;
	var $blocki;
	var $blockd;
	var $central;
	var $footers;
	var $jscript=array();
	//var $pag_blocks=array();
	var $page_mnu=array();
	var $page_msg=array();
	var $vars=array();
	var $vars_ste=array();
	var $show_pst=true;
	var $show_msg=True;
	var $pg_title=True;
	var $align_center=True;
	var $emails=array();
	//var $tmp="";

	/**
	 *Get the url paramters and puts them into the class array var called vars
	 *@author Josep Marxuach
	 *@access public
	 *@return boolean True if get parameters
	 */
	function get_page_params(){
		//global $_SERVER;
		//global $_POST;

		if (array_key_exists("QUERY_STRING",$_SERVER)){
			$qstring=$_SERVER["QUERY_STRING"];
		} else $qstring="";

		if ($qstring!=""){

			$qstring=urldecode($qstring);  // needed when url comming from xml file
			parse_str($qstring,$_GET);     // when urlencode $_GET not working


			if (($pos=strpos($qstring,"xout="))>0){
				$this->xout = 1;
				parse_str($qstring,$this->vars);
				return true;
			}

			if ("url="==substr($qstring,0,4)){
				parse_str($qstring);
				if (isset($url) && $url!=""){
					if (!$vars=$this->url_decrypt($url)) return false;
					$this->vars=$vars;
					return true;
				} else return false;
			}
				
			if (($pos=strpos($qstring,"data="))>0){
				parse_str(substr($qstring,($pos),strpos(substr($qstring,($pos),strlen($qstring)),"&")));
				if (!$this->cryptar) $data = str_replace(",","&",$data);
				if (!$vars=$this->url_decrypt($data)) return false;
				$this->vars=$vars;
				if (array_key_exists("list_del",$_GET)){
					$this->vars["list_del"]=$_GET["list_del"];
				}
				return true;
			}

			if (!$this->cryptar){
				parse_str($qstring, $this->vars);
				return true;
			}

		} elseif (isset($_POST)){

			if (array_key_exists("form1_data",$_POST)){
				if (!$this->cryptar) $_POST["form1_data"] = str_replace(",","&",$_POST["form1_data"]);
				if (!$vars=$this->url_decrypt($_POST["form1_data"])) return false;
				$this->vars=$vars;
				return true;
			}

		}
		return false;
	}
	/**
	 * Executes all php blocks and makes an echo
	 * @author Josep Marxuach
	 * @access public
	 */
	function out(){
		if (!isset($this->xout)) {
			$central=$this->exec_blocks($this->central);
			$header=$this->exec_blocks($this->headers);
			if (count($this->blocki)!=0)
			$bloci="<td class=\"bleft\">\n".$this->exec_blocks($this->blocki)."</td>\n";
			else $bloci="";
			if (count($this->blockd)!=0)
			$blocd="<td class=\"bright\">\n".$this->exec_blocks($this->blockd)."</td>\n";
			else $blocd="";
			$footer=$this->exec_blocks($this->footers);
			if ($this->show_pst) $position=$this->position();
			if ($this->show_msg) $messages=$this->messages();

			$out_string=$this->head();
			if ($this->onload!="") $this->onload=" onload=\"".$this->onload."\"";
			$out_string .= "<body".$this->onload.">\n";
			if ($this->align_center) $align="align=\"center\"";else $align="";
			$out_string .= "<table class=\"pagina\" $align><tr><td>\n";
			$out_string .=$header;
			$out_string .= "<table class=\"tablas\">\n"
			."<tr>\n";
			$out_string .=$bloci;
			$out_string .= "<td class=\"pcentral\">\n"; // abro una columna
			if ($this->show_pst) $out_string .=$position;
			if ($this->show_msg) $out_string .=$messages;
			$out_string .= $central;
			$out_string .= "</td>\n";
			$out_string .= $blocd;
			$out_string .= "</tr>\n"; // Cierro la fila
			$out_string .= "</table>\n"; // Cierro la tabla de la parte central
			$out_string .= $footer;
			$out_string .= "</td></tr></table>\n";
			//$out_string .= $this->tmp;
			$out_string .= "</body>\n"
			."</html>";
		} else {
			$out_string="<?xml version=\"1.0\" encoding=\"".$this->charset."\"?>";
			$out_string.="<data>";
			$out_string.=$this->exec_blocks($this->central);
			$out_string.="</data>";
		}

		$this->send_headers();
		echo $out_string;
		$this->sendemail();
	}
	/**
	 * Saves to the session cookie the current pg and url.
	 * It will be used by the navigator manager to redirect to the next url
	 * @author Josep Marxuach
	 * @access public
	 */
	function state(){
		//global $_SERVER;

		$xml= new xml;
		if ($db = $xml->GetXMLTree($this->file_nav.".xml")){

			$layout = $db["NAVIGATOR"][0]["PAGE"][0];

			//If no pg is set then take home from layout
			if (!isset($this->vars["pg"]) && array_key_exists("HOME_CENTRAL",$layout)) {
				$this->add_central(_DirBLOCKS.$layout["HOME_CENTRAL"][0]["VALUE"].".php");
				$this->vars["pg"]=$layout["HOME_CENTRAL"][0]["VALUE"];
				$pg=$this->vars["pg"];
				if (array_key_exists("ATTRIBUTES",$layout["HOME_CENTRAL"][0])) $this->vars["htm"]=$layout["HOME_CENTRAL"][0]["ATTRIBUTES"]["XML"];
					
			}

			if (is_array($this->vars_ste) && isset($this->vars_ste) && is_array($this->vars))
			{
				//if(substr($_SERVER["QUERY_STRING"],0,4)=="url=") $url=true;else $url=false;
				$state=$this->vars_ste;
				$num=count($state);
				if (array_key_exists("pg",$this->vars)) $pg=$this->vars["pg"];else $pg=NULL;
				if ($num>0) $pag_ant=$state[($num-1)]["pg"];else $pag_ant="";
				$in_state=false;

				for ($i=0;$i<$num-2;$i++) if ($pg==$state[$i]["pg"]) {$in_state=true;break;};
				if (!$in_state && array_key_exists("PG",$db["NAVIGATOR"][0])) {

					foreach ($db["NAVIGATOR"][0]["PG"] as $pags) {
						foreach ($pags["NAV"] as $pag) {
							//print_r($pag);
							if ($pg==$pag["PREV"][0]["VALUE"]
							&& $state[($num-1)]["pg"]==$pags["ATTRIBUTES"]["NAME"]
							&& $pag["ACTION"][0]["VALUE"]=="add") {unset($state[($num-1)]);break 2;}

							$action="";
							if ($pg==$pags["ATTRIBUTES"]["NAME"])
							{

								if ($pag["PREV"][0]["VALUE"]==$pag_ant) $action=$pag["ACTION"][0]["VALUE"];
								if ($pag["PREV"][0]["VALUE"]=="any") $action=$pag["ACTION"][0]["VALUE"];
								if (array_key_exists("REDIR",$pag)) $this->vars["redir"]=$pag["REDIR"][0]["VALUE"];

								switch($action){
									case "home":

										$state=array();
										$state[0]["pg"]=$this->vars["pg"];
										$state[0]["url"]="";//$_SERVER["QUERY_STRING"];
										if (isset($this->vars["nm"])) $state[0]["nm"]=$this->vars["nm"];
										break 3;

									case "reset":
										/*
										 $state=array_slice($state,0,1);
										 $state[1]["pg"]=$this->vars["pg"];
										 $state[1]["url"]=$_SERVER["QUERY_STRING"];
										 */
										$state=array();
										$state[0]["pg"]="home";
										$state[0]["url"]="";//$_SERVER["QUERY_STRING"];
										$state[0]["nm"]=_HOME;
										$state[1]["pg"]=$this->vars["pg"];
										$state[1]["url"]=$_SERVER["QUERY_STRING"];
										if (isset($this->vars["nm"])) $state[1]["nm"]=$this->vars["nm"];
										//else $state[1]["nm"]=_UNTITLED;

										break 3;
									case "add":
										$state[$num]["pg"]=$this->vars["pg"];
										$state[$num]["url"]=$_SERVER["QUERY_STRING"];
										if (isset($this->vars["nm"])) $state[$num]["nm"]=$this->vars["nm"];
										if (array_key_exists("NEXT",$pag)) $state[$num]["next"]=$pag["NEXT"][0]["VALUE"];
										break 3;
									case "current":
										$state[$num-1]["pg"]=$this->vars["pg"];
										$state[$num-1]["url"]=$_SERVER["QUERY_STRING"];
										if (isset($this->vars["nm"])) $state[$num-1]["nm"]=$this->vars["nm"];

										break 3;
									case "delete":
										unset($state[$num-1]);
										break 3;
									case "nothing":
										break 3;
									default:
										//echo $pag->next;
										break ;
								}
							}
						}
					}
				} else {
					for ($z=$i+1;$z<$num;$z++) unset($state[$z]);
				}
				// print_r($state);
				//Fin guardar estado
			} else {if (isset($this->vars["pg"])) {$state[0]["pg"]=$this->vars["pg"];$state[0]["url"]="";} else $state=NULL;}

			if (isset($state)) $this->vars_ste=$state;

			// create the layout of the page
			//print_r($pags);

			if (array_key_exists("XSLT",$layout)) $this->file_xslt=$layout["XSLT"][0]["VALUE"];
			if (array_key_exists("MSG",$layout)) {
				$this->set_cookie_state(""._CkSTATE."", $this->vars_ste);
				$this->show_msg=true; }else $this->show_msg=false;

				if (array_key_exists("PGTITLE",$layout)) $this->pg_title=true; else $this->pg_title=false;

				if ((!isset($action) || $action=="") && array_key_exists("PG",$db["NAVIGATOR"][0]))
				foreach ($db["NAVIGATOR"][0]["PG"] as $pags)
				if ($pg==$pags["ATTRIBUTES"]["NAME"]) break;

				//print_r($layout);
				// -----------------------------------------------
				if (isset($pags) && array_key_exists("CENTRAL",$pags)) {
					if (array_key_exists("MNU",$pags["CENTRAL"][0])) $this->add_mnu("central",$pags["CENTRAL"][0]["MNU"]);
					if (array_key_exists("BLOCK",$pags["CENTRAL"][0]))
					foreach ($pags["CENTRAL"][0]["BLOCK"] as $block) {
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_central(_DirBLOCKS.$block["VALUE"].".php");
					}
				}
				// -----------------------------------------------
				if (isset($pags) && array_key_exists("HEADERS",$pags)) {
					if (array_key_exists("MNU",$pags["HEADERS"][0])) $this->add_mnu("headers",$pags["HEADERS"][0]["MNU"]);
					if (array_key_exists("BLOCK",$pags["HEADERS"][0]))
					foreach ($pags["HEADERS"][0]["BLOCK"] as $block) {
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_headers(_DirBLOCKS.$block["VALUE"].".php");
					}

				} elseif (array_key_exists("HEADERS",$layout))
				{
					if (array_key_exists("MNU",$layout["HEADERS"][0])) $this->add_mnu("headers",$layout["HEADERS"][0]["MNU"]);
					if (array_key_exists("BLOCK",$layout["HEADERS"][0]))
					foreach ($layout["HEADERS"][0]["BLOCK"] as $block){
						if (array_key_exists("ATTRIBUTES",$block))$this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_headers(_DirBLOCKS.$block["VALUE"].".php");
					} else $this->headers=true;
				}
				// -----------------------------------------------
				if (isset($pags) && array_key_exists("BLEFT",$pags)) {
					if (array_key_exists("MNU",$pags["BLEFT"][0])) $this->add_mnu("bleft",$pags["BLEFT"][0]["MNU"]);
					if (array_key_exists("BLOCK",$pags["BLEFT"][0]))
					foreach ($pags["BLEFT"][0]["BLOCK"] as $block) {
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_blocki(_DirBLOCKS.$block["VALUE"].".php");
					}

				} elseif (array_key_exists("BLEFT",$layout))
				{
					if (array_key_exists("MNU",$layout["BLEFT"][0])) $this->add_mnu("bleft",$layout["BLEFT"][0]["MNU"]);
					if (array_key_exists("BLOCK",$layout["BLEFT"][0]))
					foreach ($layout["BLEFT"][0]["BLOCK"] as $block){
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_blocki(_DirBLOCKS.$block["VALUE"].".php");
					} else $this->blocki=true;
				}
				// ------------------------------------------------

				if (isset($pags) && array_key_exists("BRIGHT",$pags)) {
					if (array_key_exists("MNU",$pags["BRIGHT"][0])) $this->add_mnu("bright",$pags["BRIGHT"][0]["MNU"]);
					if (array_key_exists("BLOCK",$pags["BRIGHT"][0]))
					foreach ($pags["BRIGHT"][0]["BLOCK"] as $block) {
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_blockd(_DirBLOCKS.$block["VALUE"].".php");
					}

				} elseif (array_key_exists("BRIGHT",$layout))
				{
					if (array_key_exists("MNU",$layout["BRIGHT"][0])) $this->add_mnu("bright",$layout["BRIGHT"][0]["MNU"]);
					if (array_key_exists("BLOCK",$layout["BRIGHT"][0]))
					foreach ($layout["BRIGHT"][0]["BLOCK"] as $block){
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_blockd(_DirBLOCKS.$block["VALUE"].".php");
					} else $this->blockd=true;
				}
				// ------------------------------------------------


				if (isset($pags) && array_key_exists("FOOTERS",$pags)) {
					if (array_key_exists("MNU",$pags["FOOTERS"][0])) $this->add_mnu("footers",$pags["FOOTERS"][0]["MNU"]);
					if (array_key_exists("BLOCK",$pags["FOOTERS"][0]))
					foreach ($pags["FOOTERS"][0]["BLOCK"] as $block) {
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_footers(_DirBLOCKS.$block["VALUE"].".php");
					}

				} elseif (array_key_exists("FOOTERS",$layout))
				{
					if (array_key_exists("MNU",$layout["FOOTERS"][0])) $this->add_mnu("footers",$layout["FOOTERS"][0]["MNU"]);
					if (array_key_exists("BLOCK",$layout["FOOTERS"][0]))
					foreach ($layout["FOOTERS"][0]["BLOCK"] as $block){
						if (array_key_exists("ATTRIBUTES",$block)) $this->vars["xml"][$block["VALUE"]]=$block["ATTRIBUTES"]["XML"];
						$this->add_footers(_DirBLOCKS.$block["VALUE"].".php");
					} else $this->footers=true;
				}
				// Set Main menu
				if (array_key_exists("MNU",$layout)) {
					$this->add_mnu("page",$layout["MNU"]);
				}

				// ------------------------------------------------
		}
		//print_r($this->vars);
	}
	/**
	 *Add mnu items to page layout.
	 *@author Josep Marxuach
	 *@access public
	 *@param string node name
	 *@param array array[item]=href of menu items
	 */
	function add_mnu($node,$mnus){
		$z=0;
		foreach ($mnus as $items) {
			if (array_key_exists("ITEM",$items)) {
				$i=0;
				foreach ($items["ITEM"] as $item) {
					if (array_key_exists("HREF",$item["ATTRIBUTES"])) $this->page_mnu[$node][$z][$i]["href"]=$item["ATTRIBUTES"]["HREF"];
					if (array_key_exists("LINK",$item["ATTRIBUTES"])) $this->page_mnu[$node][$z][$i]["link"]=$item["ATTRIBUTES"]["LINK"];
					if (array_key_exists("NAME",$item["ATTRIBUTES"])) $this->page_mnu[$node][$z][$i]["name"]=$item["ATTRIBUTES"]["NAME"];

					$this->page_mnu[$node][$z][$i]["txt"]=$item["VALUE"];
					$i++;
				}
				if (array_key_exists("ATTRIBUTES",$items)){
					if (array_key_exists("TITLE",$items["ATTRIBUTES"])) $this->page_mnu[$node][$z]["title"]=$items["ATTRIBUTES"]["TITLE"];
					if (array_key_exists("NAME",$items["ATTRIBUTES"])) $this->page_mnu[$node][$z]["name"]=$items["ATTRIBUTES"]["NAME"];
				}
			}
			$z++;
		}
	}
	/**
	 *Calls a url depending on the navigator manager.
	 *Uses the php function header("location: url")
	 *Defines redirection on var action:
	 * action=1 go to num-2.
	 * action=2 no redirect goes to current url num-1.
	 * action=3 go to $this->vars[redir] node and param vars.
	 *@author Josep Marxuach
	 *@access public
	 *@param string variables needed for redirection ex: "id_account=34"
	 */
	function redirect($vars=Null){
		$state=$this->vars_ste;
		$go=null;
		if (array_key_exists("redir",$this->vars)){
			$action=1;
			if ($this->vars["redir"]!="") {
				$action=3;
				if (isset($vars)) $vars="&".$vars;else $vars="";
				$go=$this->url_encrypt("pg=".$this->vars["redir"].$vars);
			}
		} else $action=2;

		$num=count($state);

		switch($action) {
			case 1:
				$go=$state[$num-2]["url"];
				unset($state[$num-1]);
				unset($state[$num-2]);
				$this->vars_ste=$state;
				break;
			case 2:
				$go=$state[$num-1]["url"];
				unset($state[$num-1]);
				$this->vars_ste=$state;
				break;
			default:
				break;
		}
		//echo $action."<br/>".$go;die();
		if ($go!="") header ("Location: ".LK_PAG.htmlspecialchars_decode($go));else header ("Location: ".LK_HOME_ADM."");

	}
	/**
	 *Adds a php file to the class array var to be displayed at the header section of the page
	 *It will be "include" executed with the method exec_blocks
	 *@author Josep Marxuach
	 *@access private
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_headers($new_header){
		$n=count($this->headers);
		$this->headers[(int)$n]=$new_header;
	}
	/**
	 *Adds a php file to the class array var to be displayed at the footer section of the page
	 *It will be "include" executed with the method exec_blocks
	 *@author Josep Marxuach
	 *@access private
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_footers($new_footer){
		$n=count($this->footers);
		$this->footers[(int)$n]=$new_footer;
	}
	/**
	 *Adds a php file to the class array var to be displayed at the left section of the page
	 *It will be "include" executed with the method exec_blocks
	 *@author Josep Marxuach
	 *@access private
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_blocki($new_block){
		$n=count($this->blocki);
		$this->blocki[(int)$n]=$new_block;
	}
	/**
	 *Adds a php file to the class array var to be displayed at the right section of the page
	 *It will be "include" executed with the method exec_blocks
	 *@author Josep Marxuach
	 *@access private
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_blockd($new_block){
		$n=count($this->blockd);
		$this->blockd[(int)$n]=$new_block;
	}
	/**
	 *Adds a php file to the class array var to be displayed at the central section of the page
	 *It will be "include" executed with the method exec_blocks
	 *@author Josep Marxuach
	 *@access private
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_central($new_block){
		$n=count($this->central);
		$this->central[(int)$n]=$new_block;
	}
	/**
	 *Adds a jscript file to the class array var to be displayed at the head section of the page
	 *@author Josep Marxuach
	 *@access public
	 *@param array Php File names. layout array[position]=filename(inluding path file)
	 */
	function add_jscript($new_jscript){
		$n=count($this->jscript);
		$this->jscript[(int)$n]=$new_jscript;
	}
	/**
	 *Sends headers of the page with properties like cache-control, expires, pragma and last-modified info
	 *@author Josep Marxuach
	 *@access private
	 */
	function send_headers(){
		header("Expires: Mon, 26 Jul 2003 05:00:00 GMT");    // Fecha en el pasado
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Indica que siempre ha sido modificada
		header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");                          // HTTP/1.0
	}
	/**
	 *Return the <head> section of the html page. It uses many class vars, like charset, etc...
	 *@author Josep Marxuach
	 *@access private
	 *@return string Html generated by this method
	 */
	function head(){
		//$out_string = "";
		$this->charset = _CHARSET;
		$out_string  = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		$out_string .= "<html lang=\""._IDIOMA."\">\n";
		$out_string .= "<head>\n";
		$out_string .= "<title>".$this->pagetitle." ".$this->sitename."</title>\n";
		$out_string .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$this->charset."\"/>\n";
		$out_string .= "<meta http-equiv=\"CONTENT-LANGUAGE\" content=\""._IDIOMA."\"/>\n";
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

		if (isset($this->file_script)) $out_string .= "<script language=\"javascript\" type=\"text/javascript\" src=\"".$this->file_script."\"></script>\n";

		foreach ($this->jscript as $script) {
			$out_string .= "<script language=\"javascript\" type=\"text/javascript\" src=\"".$script."\"></script>\n";
		}

		$out_string .= "<link rel=\"StyleSheet\" href=\"".$this->file_CSS."\" type=\"text/css\"/>\n";

		$out_string .= "</head>\n";
		return $out_string;
	}
	/**
	 *Makes an include for each php file pass in the paramenter array.
	 *Every php file has to use $this->html_out var to print anything.
	 *@author Josep Marxuach
	 *@access private
	 *@param arrar List of php files to include. Layout array[position]=filename(including path file)
	 *@return string Html generated by php files
	 */
	function exec_blocks($php_files) {
		$this->html_out="";
		if (is_array($php_files)) {
			reset($php_files);
			foreach ($php_files as $xth_file_name) {
				if($xth_file_name!="" && file_exists($xth_file_name)) include($xth_file_name);
			}
		}
		return $this->html_out;
	}
	/**
	 *Handles the languaje cookie*.
	 *If doesn't exists returns the default languaje code "es"
	 *@author Josep Marxuach
	 *@access public
	 *@param string languaje Code to save in the cookie
	 *@return string Languaje code to use on the require_once or false
	 */
	function cookie_idioma($parm_idma=NULL){

		if (isset($parm_idma)) {
			if (file_exists(""._DirLANGS."lang_$parm_idma".FILE_EXT.".php")) {
				setcookie(_CkLANG,$parm_idma,time()+31536000*10);
				return $parm_idma;
			} else return false;
		} else
		if (isset($_COOKIE[_CkLANG])){
			$idioma=$_COOKIE[_CkLANG];
			if (file_exists(""._DirLANGS."lang_$idioma".FILE_EXT.".php")) return $idioma; else return false;
		}
		else
		{
			if (file_exists(""._DirLANGS."lang_"._DEFAULT_LANG.FILE_EXT.".php"))
			{ setcookie(_CkLANG,_DEFAULT_LANG,time()+31536000*10); return _DEFAULT_LANG;
			} else return false;
			// or select language using a intro page make --> include("blocks/intro.php");exit();

		}
	}
	/**
	 *Creates a cookie.
	 *If cookie can not be set returns false.
	 *@author Josep Marxuach
	 *@access public
	 *@param string Name of the cookie
	 *@param string Content of the cookie
	 *@return string Languaje code to use on the require_once or false
	 */
	function set_cookie_state($ck_name,$content=NULL){

		if (is_array($content)) $str=serialize($content); else $str=NULL;

		if (setcookie($ck_name,$str))
		return true;
		else return false;
	}
	/**
	 *Gets cookie content.
	 *If doesn't exists returns NULL
	 *@author Josep Marxuach
	 *@access public
	 *@param string Name of the cookie
	 *@return string Unserialized content of the cookie or Null if it doesn't exist
	 */
	function get_cookie_state($ck_name)
	{

		if (isset($_COOKIE[$ck_name])){
			$str=stripslashes($_COOKIE[$ck_name]);
			//$this->tmp.=$str."<br />";
			$state=unserialize($str);
			//$this->tmp.= var_export($state,true)."<br />";

			return $state;
		}
		else
		{
			return null;
		}
	}

	/**
	 *Saves into the class var array page_msg any message to be displayed as error or help with the method ->messages.
	 *@author Josep Marxuach
	 *@access public
	 *@param string Message
	 *@return boolean
	 */
	function add_msg($string){

		reset($this->page_msg);
		while (list($key,$msg)=each($this->page_msg))
		{ if ($msg==$string) return true;}

		if (isset($string) && $string!="")
		$this->page_msg[count($this->page_msg)]=$string;
		return true;
	}
	/**
	 *Returns a html table with all messages saved during page execution. To add a message you use method add_message.
	 *Returns html into the class var html_out.
	 *@author Josep Marxuach
	 *@access private
	 */
	function messages(){
		$out_string="";

		reset($this->page_msg);
		if ($this->page_msg){
			$out_string .= "<table class=\"msg_admin\"><tr><td class=\"msg_admin\">";
			while (list($key,$msg)=each($this->page_msg))
			$out_string .= ""._MSG." : $msg<br/>";
			$out_string .= "</td></tr></table>";
		}

		return $out_string;
	}
	/**
	 * Creates a table 'you are here' links.
	 *
	 * @return html table with anchors
	 */
	function position(){
		$out_string="";
		if (is_array($this->vars_ste) && $this->vars_ste) {
			reset($this->vars_ste);
			$out_string .= "<table class=\"msg_position\"><tr><td class=\"msg_position\">";
			while (list($key,$msg)=each($this->vars_ste)){

				if (isset($msg["nm"])) $name=$msg["nm"];else {
					if (defined(strtoupper("_".$msg["pg"]))) $name=constant(strtoupper("_".$msg["pg"]));
					else $name=_UNTITLED;
				}

				if ($msg["pg"]!=$this->vars["pg"] && isset($msg["url"])) $out_string .= " >> ".$this->html_link($msg["url"],$name,"msg_position",false);
				else $out_string .= " >> ".$name;
			}

			$out_string .= "</td></tr></table>";
		}
		return $out_string;
	}
	/**
	 *Returns a html table list from an array of columns.
	 *The array layout is list[row_num][fieldname]=fieldvalue and List[0][field_name]=fieldname.
	 *Returns html into the class var html_out.
	 *@author Josep Marxuach
	 *@access public
	 *@param array   array[row_num][fieldname]=fieldvalue
	 *@param integer initial row number within the sql query, needed to link with next & previous page
	 *@param integer Number of rows to show
	 *@param string current page name for links. Here vars that want to pass allways.
	 *@param string edit page name & parameter for links Ex: "pg=edtpage&id="
	 *@param string Parameter to delete row Ex: "id_to_del="
	 *@param integer Variables to pass through Ex : "view=$view&id_account=$id_account"
	 */
	function print_list( $list, $from, $num_rows, $pagina, $edt_pagina, $borrar, $vars,$found_rows=Null) {
		$next=$from+$num_rows;
		$previous=$from-$num_rows;
		$total=count($list)-1;
		$col_num=count($list[0])-1;

		//if (($this->permissions["".$this->auth["perm"].""])<16) $borrar=NULL;
		if (isset($borrar)) $col_num++;

		if (isset($pagina) && $pagina!="")
		$pagina="pg=".$this->vars["pg"]."&$pagina";
		else $pagina="pg=".$this->vars["pg"];

		if (isset($borrar)) $this->html_out .="<form id=\"list\" action=\"".LK_HOME_ADM."\" method=\"get\" enctype=\"multipart/form-data\">\n";

		$this->html_out .= "<table class=\"lista\">\n";
		$this->html_out .= "<tr><td class=\"list_found\" colspan=\"$col_num\">"._FOUND." $found_rows "._ROWS."</td></tr>\n";
		if ($found_rows>0) {

			if($found_rows>$num_rows){
				$this->html_out .= "<tr><td class=\"list_pages\" colspan=\"$col_num\">"._PAGS." ";
				$z=0;
				for ($i=0;$i<$found_rows;$i=$i+$num_rows){
					if ($from>=($i-($num_rows*5)) && $from<=($i+($num_rows*5))){
						if ($z>0) $this->html_out .="-";
						if ($from==$i) $this->html_out .= "<span class=\"list_page\">".($i/$num_rows+1)."</span>";
						else $this->html_out .= " <a href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$i&$vars")."\">".($i/$num_rows+1)."</a> ";
						$z++;
					}
				}
				$this->html_out .= " "._OF." ".ceil($found_rows/$num_rows)." ";

				if ($from>0) $this->html_out .= " <a href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$previous&$vars")."\">"._PREVIOUS."</a>";
				if ($from>0 && $found_rows>$next) $this->html_out .=" - ";
				if ($found_rows>$next) $this->html_out .= "<a href=\"".LK_PAG."".$this->url_encrypt("$pagina&from=$next&$vars")."\">"._NEXT."</a>";
				$this->html_out .= "</td></tr>\n";
			}

			if (isset($borrar))  $this->html_out .= "<input type=\"hidden\" name=\"list_data\" value=\"".$this->txt_encrypt("$pagina&from=$from&$vars")."\"/>\n";
			if (isset($borrar)) $this->html_out .="<tr><td colspan=\"$col_num\"><input  type=\"submit\" name=\"submit\" value=\""._DELETE."\" class=\"btonlist\" /></td></tr>\n";

			// Cabecera

			if (isset($borrar)) {
				$this->html_out .= "<script type='text/javascript'>
				function checkAll () {
					if (document.getElementById('checkall').checked)
						new_val = true;
						else
						new_val = false;
					form = document.getElementById('list');	
					for (var i =0; i < form.length; i++){			
					 if (form.elements[i].name=='list_del[]') 
					 	form.elements[i].checked = new_val;					 	
					}
      			}
			</script>\n";
			}
			$this->html_out .= "<tr class=\"colheader\">\n";
			// ".constant(strtoupper("_todo"))."
			if (isset($borrar)) $this->html_out .= "<td class=\"hcheck\"><input type='checkbox' id='checkall' onclick='checkAll()' class='hcheck'/></td>";

			$z=false; // sirve para controla que la 1a columna no se visualize
			$field_keys=array_keys($list[0]);
			while (list($key,$value)=each($field_keys)){
				if ($z) $this->html_out .= "<td class=\"colheader\">".constant(strtoupper("_$value"))."</td>";
				else $z=true;
			}
			//if (isset($borrar)) $this->html_out .= "<td class=\"colheader\">".constant(strtoupper("_todo"))."</td>";
			$this->html_out .= "</tr>\n";
			// Contenido

			$i=0;
			while ($i<=$total)
			{
				$ln=$i % 2;
				$this->html_out .= "<tr class=\"linea$ln\" onmouseover=\"this.className='linea_over'\" onmouseout=\"this.className='linea$ln'\">\n";
				if (isset($borrar)) $this->html_out .= "<td class=\"check\"><input type=\"checkbox\" name=\"list_del[]\" value=\"".$list[$i][$field_keys[0]]."\" class=\"check\"/></td>\n";
				$z=0; // sirve para controla que la 1a columna no se visualize y el link a editar
				while (list($key,$value)=each($list[$i])) {
					$class="lista";
					if (strstr($key,"precio") && isset($value)) {if (($value-(int)$value)!=0) $dec=2;else $dec=0;
					$value=number_format($value,$dec,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY."";$class="lista_r";}
					if ($z==1) { if (!isset($value) || $value=="") $tmp=_UNTITLED;else $tmp=$value;
					if (isset($edt_pagina)) $tmp_link="<a href=\"".LK_PAG."".$this->url_encrypt("nm=$tmp&$edt_pagina".$list[$i][$field_keys[0]]."")."\">$tmp</a>";
					else $tmp_link=$tmp;
					$this->html_out .= "<td class=\"lista\">$tmp_link</td>";
					}
					if ($z>1) {
						if(!strpos($value,"href=")) $value = wordwrap($value, 110, "<br />\n");
						$this->html_out .= "<td class=\"$class\">$value</td>";}
						$z++;
				}
				//if (isset($borrar)) $this->html_out .= "<td class=\"lista\"><a href=\"".LK_PAG."".$this->url_encrypt("$pagina&$borrar".$list[$i][$field_keys[0]]."&from=$from&$vars")."\" onclick=\"return confirm('"._DELETE." - ".addslashes($list[$i][$field_keys[1]])."?')\">"._DELETE."</a></td>";
				$this->html_out .= "</tr>\n";
				$i=++$i;
			}
		}
		$this->html_out .= "</table>\n";
		if (isset($borrar))  $this->html_out .= "</form>\n";
	}
	/**
	 *Returns jscript seccion <script> for a tree in javascript.
	 *Needs the javascript file arbol.js that generates tree.
	 *Returns html into the class var html_out.
	 *@author Josep Marxuach
	 *@access public
	 *@param integer id of the first element that will be the tree parent, is used recursivily as the next subtree
	 *@param array* Pointer to the array of elements of the tree. Layout arry[Parent_id][position]["id"]["value"]
	 *@param string Name of the root element, is not included on the array of elements
	 *@param integer Current level within the tree. Always 0 to call the function. Is used for recursivity
	 *@param string page name for the links Ex: "pg=edtpage"
	 */
	function create_tree($id,&$tree_array,$name_parent, $nivell, $pagina) {

		if ($nivell==0) {$this->html_out .= "<script src=\""._DirSCRIPTS."browser.js\"></script>\n"
		."<script src=\""._DirSCRIPTS."arbol.js\"></script>\n"
		."<script>\n"
		."arbol = titulo(\"<b>$name_parent</b>\", \"".LK_PAG."".$this->url_encrypt("$pagina")."\")\n";
		}

		if (is_array($tree_array) && array_key_exists($id,$tree_array)) $maxim=count($tree_array[$id]);else $maxim=0;

		if ($nivell>0) {
			if ($nivell==1) $nodo="arbol";else $nodo="id_nodo".($nivell-1)."";

			if ($maxim>0) $this->html_out .= "id_nodo$nivell = nodo($nodo, titulo(\"$name_parent\", \"".LK_PAG."".$this->url_encrypt("$pagina$id")."\"))\n";
			else $this->html_out .= "hoja($nodo, adoc(\"S\", \"$name_parent\", \"".LK_PAG."".$this->url_encrypt("$pagina$id")."\"))\n";
		}

		$z=0;
		while ($z<$maxim){
			$id_category=$tree_array[$id][$z]["id"];
			$name=$tree_array[$id][$z]["value"];
			$this->create_tree($id_category,$tree_array, $name,($nivell+1), $pagina);
			$z++;
		}
		if ($nivell==0) {$this->html_out .= "arbol.treeID = \"$name_parent\"\n</script>\n";}
	}
	/**
	 *creates a html table that represents page title with menu options.
	 *Returns html code.
	 *@author Josep Marxuach
	 *@access public
	 *@param string Title of the page
	 *@param bool Enables back button. True is enabled, false no.
	 *@param array Menu options in the following format array[#option][action]. Option is numeric sequence.
	 *Action can be href(page vars non encripted), confirm(creates a popup when clic),print(create a hp for printing page),
	 *and popup(includes a target blank on anchor).
	 */

	function pgtitle($title,$back=True, $links=NULL){
		if ($this->pg_title) {
			$out  = "<table class=\"title\"><tr><td class=\"title_label\">";
			$out .= "$title";
			$out .= "</td><td class=\"title_options\"><table class=\"tdoptions\"><tr><td class=\"tdoptions\">";


			if ($back) {
				if (isset($this->vars_ste)) $state=$this->vars_ste; else $state=Null;
				$num=count($state);
				//print_r($state);
				if (is_array($state) && array_key_exists($num-2,$state))
				{ if ($state[$num-2]["url"]!="")  $go=LK_PAG.$state[$num-2]["url"];else $go=LK_HOME;}
				else $go="javascript:history.go(-1)";

				$out .= "<a class=\"boton\" href=\"$go\"><img class=\"boton\" src=\""._DirIMAGES."blue_butt_left.gif\" alt=\"\" /> "._BACK."</a><br />";

			}

			if (is_array($links))
			foreach ($links as $lnk) {
				$onclick="";$target="";$link_pag="".LK_PAG."";$vars="";$url="";$href="";$name="";$op="";
				if (array_key_exists("popup",$lnk)) {$target="target=\"_blank\"";$link_pag=$lnk["popup"];}
				if (array_key_exists("confirm",$lnk)) $onclick="onclick=\"return confirm('".$lnk["confirm"]."')\"";
				if (array_key_exists("print",$lnk)) {$vars="&prt="._NAVPRT;$target="target=\"_blank\"";
				$link_pag=$_SERVER["PHP_SELF"]."?";$url=$_SERVER['QUERY_STRING'];
				$href="href=\"".$link_pag.$url.$vars."\"";
				}
				if (array_key_exists("name",$lnk)) {$name="name=\"".$lnk["name"]."\"";$href="href=\"\"";$onclick="onclick=\"return false\"";}
				if (array_key_exists("href",$lnk)) $href="href=\"$link_pag".$this->url_encrypt("".$lnk["href"]."")."$vars\"";
				if (array_key_exists("op",$lnk)) $op=$lnk["op"];
				$out .= "<a class=\"boton\" $op $name $href $onclick $target><img class=\"boton\" src=\""._DirIMAGES."blue_butt.gif\" alt=\"\"/> ".$lnk["txt"]."</a><br />";
			}

			$out .= "</td></tr></table></td></tr></table>";
			return $out;
		} else return "";
	}
	/**
	 *Add an email to the class var array of emails to be send later on.
	 *Returns Nothing.
	 *@author Josep Marxuach
	 *@access public
	 *@param string email address used to send the email
	 *@param string email name used to send the email
	 *@param string email address that will receive the email
	 *@param string subject of the email
	 *@param string msg body of the email
	 */
	function add_email($from, $from_name, $to,$subject, $msg, $html=false){
		$num=count($this->emails);
		$this->emails[$num]["from"]=$from;
		$this->emails[$num]["from_name"]=$from_name;
		$this->emails[$num]["to"][0]["name"]=$to;
		$this->emails[$num]["to"][0]["email"]=$to;
		$this->emails[$num]["subject"]=$subject;
		$this->emails[$num]["msg"]=$msg;
		$this->emails[$num]["html"]=$html;
	}
	/**
	 *Send all emails stored in class var array emails.
	 *@author Josep Marxuach
	 *@access public
	 */
	function sendemail(){
		require_once(_DirINCLUDES."email/smtp.php");
		$smtp=new smtp_class;
		//$smtp->debug=1;               /* Set to 1 to output the communication with the SMTP server */
		//$smtp->html_debug=1;
		$smtp->type=_MAIL_FUNCTION;   // set to 1 to use php mail function, set to 0 to uses smtp

		foreach($this->emails as $item) {
			$smtp->sendemail($item["from"],$item["from_name"],$item["to"],$item["subject"],$item["msg"],$item["html"]);
		}
	}
	/**
	 *Creates the html code of an hyperlink.
	 *Returns Html code of an anchor.
	 *@author Josep Marxuach
	 *@access public
	 *@param string Url of the hyperlink
	 *@param string Text to be shown on the hyperlink
	 *@param string CSS class
	 *@param string true=Encrypted, false not encrypted
	 */
	function html_link($link, $txt, $class=null,$enc=true){
		if (isset($class)) $class="class=\"$class\""; else $class="";
		if ($enc) $enc="".$this->url_encrypt("".$link."&nm=$txt")."";else $enc=$link;
		if ($enc=="") $enc="".LK_HOME."";else $enc="".LK_PAG."".$enc."";

		return "<a $class href=\"".$enc."\">".$txt."</a>";
	}
	/**
	 *Gets XSL template files from NAV files.
	 *Returns string file name without extension.
	 *Used only in admin.
	 *@author IT eLazos SL
	 *@access public
	 *@param string xml NAV filename without extension
	 */
	function getxslfile($navfile){

		$xml= new xml;
		if ($db = $xml->GetXMLTree(_TPLDIR._THEME_DIR."/$navfile.xml"))
		{
			$layout = $db["NAVIGATOR"][0]["PAGE"][0];
			if (array_key_exists("XSLT",$layout)) {$xslt=$layout["XSLT"][0]["VALUE"];
			return $xslt;
			}
		}
		return false;
	}
	/**
	 * Enter description here...
	 *
	 */
	function include_block($php_files){
		$this->html_out="";
		if (is_array($php_files)) {			
			reset($php_files);
			foreach ($php_files as $xth_file_name) {				
				if($xth_file_name!="") include($xth_file_name);
			}
		}
		return $this->html_out;		
	}


	//**************************** FIN CLASE ************************************************
}
?>
