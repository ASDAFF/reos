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

define("FILE_EXT","_adm");
require_once("includes/lks_1.php");  // Lista de Hyperlinks
require_once("includes/constants.php");
require_once("includes/class_site_xml.php");

$page=new site;

if ($page->get_page_params()) extract($page->vars);

//************************IDIOMA*********************
//$parm_idma="ca";
if (!isset($_GET["idma"])) $parm_idma=null; else $parm_idma=$_GET["idma"];
    if ($file_idioma=$page->cookie_idioma($parm_idma))
    {
    if (file_exists(_TPLDIR."/"._THEME_DIR."/langs/lang_$file_idioma.php"))
        require_once(_TPLDIR."/"._THEME_DIR."/langs/lang_$file_idioma.php");
    require_once(_DirLANGS."lang_".$file_idioma.FILE_EXT.".php");   
    require_once("includes/globals.php");
    } else {echo "Fail to load Language";die();}
//******************************************************

//$page->name = "c14cbf2"; //session name
//$page->lifetime = 50000; //session time life in minutes
//$page->start();

//$page->align_center=true;

$page->sitename=_LABEL_ORG;
$page->site_slogan=_SLOGAN;
$page->keywords=_KEYWORDS;

if (isset($prt)) $page->file_nav=_TPLDIR."/"._THEME_DIR."/".$prt; else
                 $page->file_nav=_TPLDIR."/"._THEME_DIR."/"._NAVFILE;

$page->vars_ste=$page->get_cookie_state(""._CkSTATE."");

if (array_key_exists("pg",$page->vars)) {
   $page->central=NULL;
   $pg=explode(" ",$page->vars["pg"]);
   foreach($pg as $value) $page->add_central(_DirBLOCKS."$value.php");
   unset($pg);
   }

$page->state();

if (isset($nm)) $page->pagetitle=$nm; elseif (array_key_exists("pg",$page->vars) && defined(strtoupper("_".$page->vars["pg"]))) if ($page->vars["pg"]!='home') $page->pagetitle=constant(strtoupper("_".$page->vars["pg"]));
    
//$page->set_cookie_state(""._CkSTATE."",$page->vars["state"]);
$page->out();

//$page->freeze();



?>
