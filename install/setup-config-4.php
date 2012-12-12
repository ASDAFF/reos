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

error_reporting(E_ALL);
global $_POST;
require_once("functions.php");
if (!create_deffile("../tpl/config.php",$_POST))
	header ("Location: config-error.html");
	else {	
		require_once("../tpl/config.php");		
		require_once("../padmin/includes/class_mysql.php");
        require_once("sql_utils.php");
        $dbi= new DB_Sql;        
        if (!$dbi->connect()) 
          header("Location: config-error.html");
       // else {
       // $dbi->Query("SHOW VARIABLES where variable_name = 'lower_case_table_names';");
       // $dbi->next_record();                
       // if ($dbi->Record[1]["value"]<>1) header("Location: lower_case_table_names-error.html");
       // }
       $active_langs = "";
       foreach($_POST['langs'] as $lg) {
       if ($active_langs == "")  $active_langs = $lg;
       else $active_langs .= ",".$lg;
       }
           				
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


<title>ReOS › Setup Configuration File</title>
<link rel="stylesheet" href="setup_files/install.css" type="text/css">

</head>
<body>
<SCRIPT LANGUAGE="JavaScript">


function show_bar() {
		
document.getElementById("button").innerHTML = '';
document.getElementById("bar").innerHTML = '<img src="setup_files/progress_bar.gif" alt=""/>';
	
}

	

</SCRIPT>



<h1 id="logo"><img alt="WordPress" src="setup_files/reos-logo.png"></h1>
<p>All right sparky! You've made it through this part of the
installation. ReOS can now communicate with your database. If you are
ready, time now to…</p>

<p class="step"><span id="button"><a href="install.php?langs=<?php echo $active_langs ?>"
	class="button" onclick="show_bar()">Run the install</a></span>	
	<span id="bar"><img src="setup_files/blank_bar.gif" alt=""/></span>
	</p>
	
</body>
</html>
