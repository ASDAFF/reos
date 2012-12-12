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
 * Set Admin password to avoid password changes from demo users
 */

/**
 * DB Class.
 */

require_once(_DirINCLUDES."class_mysql.php");

$db = new DB_Sql;

$db->query("update ".$db->prefix."_auth_user set password = 'admin' where username = 'admin' ");
$db->query("update ".$db->prefix."_accounts set username='customer', password = 'customer' where id_account = 5 ");
$db->query("update ".$db->prefix."_prefs set vl_pref='blueline' where id_pref = '_THEME_DIR' ");
$db->query("update ".$db->prefix."_prefs set vl_pref='39.027718840211605' where id_pref = '_GMAP_LAT' ");
$db->query("update ".$db->prefix."_prefs set vl_pref='-93.8671875' where id_pref = '_GMAP_LNG' ");
$db->query("update ".$db->prefix."_prefs set vl_pref='3' where id_pref = '_GMAP_LEVEL' ");
$db->query("update ".$db->prefix."_prefs set vl_pref='ABQIAAAAXqhLkdc0bUs8sBT4Gb3cYBQNAhrwv2b04l8JGpHz7Ea3766UlxQEGMQBNrRH1HISmz_l77ghgjEUUQ' where id_pref = '_GMAP_KEY' ");
$db->query("update ".$db->prefix."_prefs set vl_pref='reos.elazos.com/demo' where id_pref = '_UrlSITE' ");
//$db->query("update ".$db->prefix."_immos set tp_state = 1, dt_create = curdate(), dt_valid = curdate()+1");


?>
