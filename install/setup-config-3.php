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



	if (!is_writable("../tpl/config.php")) {
		if (file_exists("../tpl/config.php")){
			header ("Location: config-permissions.html");
		} else 
			if (!is_writable("../tpl")) {
			header ("Location: config-permissions.html");
		}		
	}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>ReOS › Setup Configuration File</title>
<link rel="stylesheet" href="setup_files/install.css" type="text/css"/>

</head><body>
<h1 id="logo"><img alt="ReOS" src="setup_files/reos-logo.png"/></h1>
<form method="post" action="setup-config-4.php">
	<p>Below you should enter your database connection details. If you're not sure about these, contact your host. </p>
	<table class="form-table">
		<tbody><tr>
			<th scope="row"><label for="dbname">Database Name</label></th>
			<td><input name="_DB_NAME" id="dbname" size="25" value="reos" type="text"/></td>
			<td>The name of the database you want to run ReOS in. </td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">User Name</label></th>
			<td><input name="_DB_USER" id="uname" size="25" value="username" type="text"/></td>
			<td>Your MySQL username</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Password</label></th>
			<td><input name="_DB_PWD" id="pwd" size="25" value="password" type="text"/></td>
			<td>...and MySQL password.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Database Host</label></th>
			<td><input name="_DB_HOST" id="dbhost" size="25" value="localhost" type="text"/></td>
			<td>99% chance you won't need to change this value.</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">Table Prefix</label></th>
			<td><input name="_TABLE_PREFIX" id="prefix" value="tb" size="25" type="text"/></td>
			<td>If you want to run multiple ReOS installations in a single database, change this.</td>
		</tr>
		
	   <tr>
			<th scope="row"><label for="prefix">Languages</label></th>
			<td>
			<?php 
			$langs = array ("en"=>"English","en-us"=>"English US","es"=>"Spanish","fr"=>"French","nl"=>"Ducth","ca"=>"Catalan","de"=>"German","ru"=>"Russian","tr"=>"Turkish","zh-tw"=>"Chinese-tw","zh-cn"=>"Chinese-cn");
			 foreach ($langs as $key=>$value){
	         if ($key=="en") $checked="checked=\"checked\""; else $checked="";
	         echo "<input type=\"checkbox\" name=\"langs[]\" value=\"$key\" $checked/>$value<br/>";
	         }
			?>
			</td>
			<td>If you want to run ReOS in multiple languages, select them here.</td>
		</tr>		
		
	</tbody></table>
	<p class="step"><input name="submit" value="Submit" class="button" type="submit"/></p>
</form>
</body></html>