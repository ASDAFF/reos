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
*  Main file of XAPP.
**/
define("FILE_EXT","_adm");
define("_CRYPT_LINKS",1);
require_once("includes/lks".FILE_EXT.".php");  // Lista de Hyperlinks
require_once("includes/constants.php");
require_once(_DirINCLUDES."class_page.php");

$page=new page;
//************************IDIOMA*********************

if (!isset($_GET["idma"])) $parm_idma=null; else $parm_idma=$_GET["idma"];
    if ($file_idioma=$page->cookie_idioma($parm_idma))
    {
    require_once(""._DirLANGS."lang_".$file_idioma.FILE_EXT.".php");
    require_once("includes/globals.php");
    } else {echo "Fail to load Language";die();}
//******************************************************
$page->start();

$page->sitename=_LABEL_ORG;
$page->pagetitle=" >> "._ADMIN;
$page->keywords=_KEYWORDS_ADM;

$page->show_pst=false;
$page->file_nav="navpicker";

if ($page->get_page_params()) extract($page->vars);

/*
if (isset($nm)) $page->pagetitle.=" >> ".$nm; else
    if (array_key_exists("pg",$page->vars) && defined("_".$page->vars["pg"])) $page->pagetitle.=" >> ".constant(strtoupper("_".$page->vars["pg"]));


*/
//$page->vars_ste=$page->get_cookie_state("state");

if (array_key_exists("pg",$page->vars)) {
   $page->central=NULL;
   $pg=explode(" ",$page->vars["pg"]);
   $page->auth["acc"]="C";
   foreach($pg as $value) $page->add_central(_DirPICKERS."$value.php");
   unset($pg);
   }


//$page->state();

$page->out();

//$page->freeze();

?>
