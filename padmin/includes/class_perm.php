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
*Class Permisions file
*@author Josep Marxuach  - May 2004
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_perm.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
/**
*Class Session file
**/
require_once(_DirINCLUDES."class_session.php");
/**
*Manage user permisions.
*You can have permisions set on array $permisions {1,2,3}
*@author Josep Marxuach
*@version 1.0
*@copyright 2004 by Josep Marxuach
*@package Site
*/
class Perm extends Session {
   /**
   * Hash ("Name" => Permission-Bitmask)
   **/
   var $permissions = array(
                            "1" => 1,  //Less Rights
                            "2" => 8,
                            "3" => 16 //,
                            //"4" => 8,
                            //"5" => 16  // More Rights
                          );

  /**
  *Not used
  **/
  function check($p) {
    if (! $this->have_perm($p)) {
      if (! isset($auth->auth["perm"]) ) {
        $this->auth["perm"] = "";
      }
      $this->perm_invalid($auth->auth["perm"], $p);
      exit();
    }
  }
  /**
  *Not used
  **/
  function have_perm($p) {

    
    if (! isset($this->auth["perm"]) ) {
      $this->auth["perm"] = "";
    }
    $pageperm = split(",", $p);
    $userperm = split(",", $this->auth["perm"]);
    
    list ($ok0, $pagebits) = $this->permsum($pageperm);
    list ($ok1, $userbits) = $this->permsum($userperm);

    $has_all = (($userbits & $pagebits) == $pagebits);
    if (!($has_all && $ok0 && $ok1) ) {
      return false;
    } else {
      return true;
    }
  }

  /**
  *Makes a sum  of 2 permisions
  **/
  function permsum($p) {

    
    if (!is_array($p)) {
      return array(false, 0);
    }
    $perms = $this->permissions;
    
    $r = 0;
    reset($p);
    while(list($key, $val) = each($p)) {
      if (!isset($perms[$val])) {
        return array(false, 0);
      }
      $r |= $perms[$val];
    }

    return array(true, $r);
  }
  
  /**
  *Look for a match within an list of strints
  *I couldn't figure out a way to do this generally using preg_match().
  **/

  function perm_islisted($perms, $look_for) {
    $permlist = explode( ",", $perms );
    while( list($a,$b) = each($permlist) ) {
      if( $look_for == $b ) { return true; };
    };
    return false;
  }

  /**
  *Return a complete <select> tag for permission
  *selection.
  **/
  function perm_sel($name, $current = "", $class = "") {
    reset($this->permissions);
    
    $ret = sprintf("<select multiple name=\"%s[]\"%s>\n", $name, ($class!="")?" class=$class":"");
    while(list($k, $v) = each($this->permissions)) {
      $ret .= sprintf(" <option%s%s>%s\n",
                $this->perm_islisted($current,$k)?" selected":"",
                ($class!="")?" class=$class":"",
                $k);
    }
    $ret .= "</select>";

    return $ret;
  }

  /**
  * Dummy Method. Must be overridden by user.
  **/
  function perm_invalid($does_have, $must_have) {
   
   
  }
}
?>
