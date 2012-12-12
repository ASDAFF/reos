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


	$xslt   = true;
	$mcrypt = true;
	$config = true;
	$galleries = true;
	$tmp = true;
	$imgnews = true;
	$is_ok = true;

	if (substr(phpversion(),0,1)=="4") {
		if (!function_exists('xslt_create')){		
			$xslt = false;
			$is_ok = false;
		}
	}

	if (substr(phpversion(),0,1)=="5") {
		if (!(extension_loaded('xsl'))){
			$xslt = false;
			$is_ok = false;
		}
	}

	if (!(extension_loaded('mcrypt'))){
		$mcrypt = false;
		$is_ok = false;
	}

	if (!is_writable("../tpl/config.php")) {
		if (file_exists("../tpl/config.php")){
			$config = false;
			$is_ok = false;
		} else 
			if (!is_writable("../tpl")) {
			$config = false;
			$is_ok = false;
		}		
	}

	if (!is_writable("../padmin/galleries")) {	
		$galleries = false;
		$is_ok = false;
	}

	if (!is_writable("../padmin/tmp")) {	
		$tmp = false;
		$is_ok = false;
	}

	if (!is_writable("../padmin/imgnews")) {	
		$imgnews = false;
		$is_ok = false;
	}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>ReOS › Setup File and Folder Permissions</title>
<link rel="stylesheet" href="setup_files/install.css" type="text/css">

</head><body>
<h1 id="logo"><img alt="ReOS" src="setup_files/reos-logo.png"></h1>
<form method="post" action="setup-config-3.php">
	<p>Welcome to ReOS. Before getting started, we need to verify the following :</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row"><label for="xsl">Xsl Php Extention</label></th>
			<td><?php
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

			if ($xslt) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";		
			?></td>
			<td>This is a php extention needed to run ReOS.</td>
		</tr>
		
		<tr>
			<th scope="row"><label for="mcript">MCRYPT Php Extention</label></th>
			<td><?php
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

			if ($mcrypt) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";		
			?></td>
			<td>This is a php extention needed to run ReOS.</td>
		</tr>
		
		<tr>
			<th scope="row"><label for="config">Config.php</label></th>
			<td><?php
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

			if ($config) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";			
			?></td>
			<td>File <code>config.php</code> or folder <code>tpl</code> must be writable.</td>
		</tr>
		
	   <tr>
			<th scope="row"><label for="config">padmin/galleries</label></th>
			<td><?php
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

			if ($galleries) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";		
			?></td>
			<td>Folder <code>padmin/galleries</code> must be writable.</td>
		</tr>
		
	    <tr>
			<th scope="row"><label for="config">padmin/tmp</label></th>
			<td><?php
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

			if ($tmp) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";		
			?></td>
			<td>Folder <code>padmin/tmp</code> must be writable.</td>
		</tr>
		
	    <tr>
			<th scope="row"><label for="config">padmin/imgnews</label></th>
			<td><?php
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

			if ($imgnews) echo "<img src=\"setup_files/ok_checked.png\" alt=\"OK\"/>";
			else
			echo "<img src=\"setup_files/not_checked.png\" alt=\"Ups!\"/>";			
			?></td>
			<td>Folder <code>padmin/imgnews</code> must be writable.</td>
		</tr>

		
	
	</tbody></table>
	
	<p>If you're not sure about these, contact your hosting provider. </p>
	
	<p class="step">
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
 
	if ($is_ok)
		echo "<a href=\"setup-config-2.php\" class=\"button\">Next</a>";
		else 
		echo "<a href=\"setup-config-1.php\" class=\"button\">Check Again !</a>";
		
	?>
	
	</p>
</form>
</body></html>