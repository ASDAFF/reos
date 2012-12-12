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


require_once("../tpl/config.php");
error_reporting(E_ALL);
define("_DirINCLUDES","../padmin/includes/");
require_once("../padmin/includes/class_backup.php");
require_once("sql_utils.php");

$lst_idiomas = array ("en"=>"English","en-us"=>"English US","es"=>"Spanish","fr"=>"French","nl"=>"Ducth","ca"=>"Catalan","ru"=>"Russian", "de"=>"German","tr"=>"Turkish","zh-tw"=>"Chinese-tw","zh-cn"=>"Chinese-cn");

$langs = preg_split("/,/",$_GET["langs"]);

$dbi= new mysql_backup();
//$sql_files=array("crebas.sql","struct_exp.sql","data_exp.sql","cities_exp.sql","labels_exp.sql","lists_exp.sql","tk_ctg_exp.sql","tk_msgtext_exp.sql");

$sql_files=array("crebas.sql");

foreach ($sql_files as $file) {
	$sql=file_get_contents($file);
	$sql=str_replace("arpa",_TABLE_PREFIX,$sql);
	$ret=NULL;
	sql_file($ret, $sql);
	foreach ($ret as $sql_line) {
		if (strlen(rtrim($sql_line))>0)
		if ($dbi->query($sql_line)) $install=true;
		else {
			$install=false;
			break 2;}
	}
}

//$xml_files=array("data/struct.xml", "data/data.xml", "data/country.xml", "data/comunidad.xml", "data/provincia.xml","data/poblacion.xml","data/tk_ctg.xml","data/tk_msgtext.xml");
$xml_files=array("data/struct.xml", "data/data.xml", "data/country.xml", "data/tk_ctg.xml","data/tk_msgtext.xml");
$active_langs = "";
foreach($langs as $lang_key){
	$lang_name = $lst_idiomas[$lang_key];
	$xml_files[]="data/langs/lang-".$lang_key.".xml";
	if ($active_langs=="") $active_langs = $lang_key." = ".$lang_name;
	else $active_langs .= ", ".$lang_key." = ".$lang_name;
}

//$xml_files=array("data/struct.xml", "data/data.xml");
foreach ($xml_files as $file) {
$install=$dbi->Import($file);
}
// writing selected langs to prefix_prefs table

if (!$dbi->query("update "._TABLE_PREFIX."_prefs set vl_pref ='".$active_langs."' where id_pref = '_ACTIVE_LANGS'")) 
    $install=false;
if (!$dbi->query("update "._TABLE_PREFIX."_prefs set vl_pref ='".$langs[0]."' where id_pref = '_DEFAULT_LANG'")) 
    $install=false;    

if ($install) header ("Location: install-ok.html");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>ReOS › Error</title>
	<link rel="stylesheet" href="setup_files/install.css" type="text/css"/>
</head><body id="error-page">
<h1 id="logo"><img alt="ReOS" src="setup_files/ReOS-logo.png"></h1>
	<p>
</p><h1>Error while executing installation SQL statements</h1>
<p>This means that the your MySQL database installation doesn't execute ReOS installation SQL scripts.</p>
<p>If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href="http://reos.elazos.com/">ReOS Support Site</a>.</p>
<p>
<?php
if (count($dbi->Error_Messages)>0) echo "<b>".count($dbi->Error_Messages)." errors found !</b><br/><br/>";
foreach($dbi->Error_Messages as $msg) echo $msg."<br/>";
?>
</p>
</body></html>
