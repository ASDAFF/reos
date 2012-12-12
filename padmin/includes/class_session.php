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
*Class Session file
*@author Josep Marxuach  - May 2004
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_session.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
/**
*Class Utils file.
*Class Session extends class Utils.
**/
require_once(_DirINCLUDES."class_utils.php");
//require_once("includes/class_utils.php");
/**
*Session Handling
*@author Josep Marxuach
*@version 1.0
*@copyright 2005 by Josep Marxuach
*@package Site
*/
class Session extends utils {

  var $name = "c14cbf1";
  var $id = ""; // Sesion id
  var $cookie_path = '/';
  /**
  * Duration in Minutes of session
  **/
  var $lifetime = 120;
  var $cookie_domain = '';
  var $mode = "cookie"; //var $mode = "cookie";  ## We propagate session IDs with cookies - If not set use GET
  var $trans_id_enabled = true;
  var $allowcache = 'nocache';
  
  var $auth = array();            ## Data array

  /**
  *Constructor.
  *Do not make anything.
  **/
  function Session() {

  }
/**
*Pone en marcha un session.
**/
function start() {
     

    if ( $this->mode=="cookie")  {
      ini_set ("session.use_only_cookie","1");       // Session using Cookie
    } else ini_set ("session.use_only_cookie","0"); //Session using GET

    $this->set_tokenname();

    $this->put_headers();
    //echo session_name();
    $ok = session_start();
    $this->id = session_id();
    //echo "session ID".$this->id;
    if (is_array($_SESSION)){
       reset ($_SESSION);
       while (list ($clave, $val) = each ($_SESSION)) {
       $this->auth[$clave]=$val;
       }
       
   }
   
    return $ok;
  } // end func start
  /**
  * freezes all registered things ( scalar variables, arrays, objects )
  * by saving all registered things to $_SESSION.
  * @access public
  **/
function freeze() {
  if (is_array($this->auth))
     {reset ($this->auth);
      while (list ($clave, $val) = each ($this->auth)) {
      $_SESSION[$clave]=$val;
      }
  }
}
  
  /**
  * Inicializa la session.
  * Pone nombre id a la session y la duraci�n
  **/
function set_tokenname(){

      //if ($this->name==false) $this->name=substr(md5(uniqid("")), 0, 4);
      session_name ($this->name);

      if (!$this->cookie_domain) {
        $this->cookie_domain = get_cfg_var ("session.cookie_domain");
      }

      if (!$this->cookie_path && get_cfg_var('session.cookie_path')) {
        $this->cookie_path = get_cfg_var('session.cookie_path');
      } elseif (!$this->cookie_path) {
        $this->cookie_path = "/";
      }

      if ($this->lifetime > 0) {
        $lifetime = $this->lifetime*60;
      } else {
        $lifetime = 0;
      }

     session_set_cookie_params(0, $this->cookie_path, $this->cookie_domain);
     //session_set_cookie_params($lifetime);
} // end func set_tokenname


  /**
  * Put headers of session.
  * set session.cache_limiter corresponding to $this->allowcache.
  */
function put_headers() {

    switch ($this->allowcache) {

      case "passive":
      case "public":
        session_cache_limiter ("public");
        break;

      case "private":
        session_cache_limiter ("private");
        break;

      default:
        session_cache_limiter ("nocache");
        break;
    }
  } // end func put_headers
  
  /**
  * Delete the cookie holding the session id.
  * 
  * RFC: is this really needed? can we prune this function?
  * the only reason to keep it is if one wants to also
  * unset the cookie when session_destroy()ing,which PHP
  * doesn't seem to do (looking @ the session.c:940)
  * uw: yes we should keep it to remain the same interface, but deprec. 
  * @access public
  **/
  function put_id() {
    /**
    * Array of cookies
    * @global array
    **/
    global $HTTP_COOKIE_VARS;
     
    if (get_cfg_var ('session.use_cookies') == 1) {
      $cookie_params = session_get_cookie_params();
      setCookie($this->name, '', 0, $cookie_params['path'], $cookie_params['domain']);
      $HTTP_COOKIE_VARS[$this->name] = "";
    }
    
  } // end func put_id
  
  /**
  * Delete the current session destroying all registered data.
  */
  function delete() {
    $this->put_id();
    return session_destroy();
  } // end func delete
  /**
  * Returns url without vars.
  * @access Private
  **/
  function url($url) {
     global $HTTP_COOKIE_VARS;

    if ($this->trans_id_enabled)
      return $url;

    // Remove existing session info from url
    $url = preg_replace(
      "/([&?])/".quotemeta(urlencode($this->name))."=(.)*(&|$)","\\1", $url); # we clean any(also bogus) sess in url
    // Remove trailing ?/& if needed
    $url = preg_replace("/[&?]+$/", "", $url);

    if (!$HTTP_COOKIE_VARS[$this->name]) {
      $url .= ( strpos($url, "?") != false ?  "&" : "?" ) . urlencode($this->name) . "=" . $this->id;
    }

    // Encode naughty characters in the URL
    $url = str_replace(array("<", ">", " ", "\"", "'"),
                       array("%3C", "%3E", "+", "%22", "%27"), $url);
    return $url;
  } // end func url
  /**
  *Returns Current url without vars.
  *@access Public
  **/
  function self_url() {
    global $HTTP_SERVER_VARS;
  
    return $this->url($HTTP_SERVER_VARS["PHP_SELF"] . 
      ((isset($HTTP_SERVER_VARS["QUERY_STRING"]) && ("" != $HTTP_SERVER_VARS["QUERY_STRING"]))
        ? "?" . $HTTP_SERVER_VARS["QUERY_STRING"] : ""));
    # return $this->url(getenv('REQUEST_URI'));
  } // end func self_url

//**********************************END CLASS**********************************
}
?>
