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
 * Class Authentification definition file
 * @author Josep Marxuach  - May 2004
 **/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_auth.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class permissions definition file
 **/
require_once(_DirINCLUDES."class_perm.php");
/**
 *Handles user authentification - using table arpa_auth_user and prefix_positions
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by Josep Marxuach
 *@package Site
 */
class Auth extends Perm {
	/**
	 * "log" for login only systems,
	 * "reg" for user self registration
	 *@var string
	 */
	var $mode_type = "log";
	/**
	 *If true, a default auth is created...
	 *@var boolean
	 */
	var $nobody = false;
	/**
	 *Database
	 *@var object
	 */
	var $db;
	/**
	 *Database Name
	 *@var string
	 */
	var $database_class = "DB_Sql";
	/**
	 *Database Table Name - users,password and permisions
	 *@var string
	 */
	var $database_table = "_auth_user";
	/**
	 *Database Table Name - User positions
	 *@var string
	 */
	var $position_table = "_position"; //NULL if not position required

	/**
	 * Constructor clase Authorization.
	 * Inicializa las tabla de usuarios y tabla de posiciones/cargos.
	 **/
	function Auth(){
		$this->database_table=_TABLE_PREFIX.$this->database_table;
		$this->position_table=_TABLE_PREFIX.$this->position_table;
	}
	/**
	 * Initialization.
	 * Se utiliza para iniciar una session de usuario.
	 * Check current auth state. Should be one of :
	 * 1) Not logged in (no valid auth info or auth expired).
	 * 2) Logged in (valid auth info).
	 * 3) Login in progress (if $this->cancel_login, revert to state 1).
	 **/
	function initiate($id_org=null) {

		if ($this->is_authenticated()) {
			$uid = $this->auth["uid"];
			switch ($uid) {
				case "form":
					# Login in progress
					# Set state to "Login in progress"
					$state = 3;
					break;
				default:
					# User is authenticated and auth not expired
					$state = 2;
					break;
			}
		} else {
			# User is not (yet) authenticated
			$this->unauth();
			$state = 1;
		}

		//echo "estado $state -  auth[uid]=".$this->auth["uid"]."<br/>";

		switch ($state) {
			case 1:
				# No valid auth info or auth is expired
				# Check for "log" vs. "reg" mode
				# Show the login form
				//$this->auth_loginform();
				$this->auth["uid"] = "form";
				$this->auth["exp"] = 0x7fffffff;
				$this->freeze();
				return false;//exit;
				break;
			case 2:
				# Valid auth info
				## DEFAUTH handling: do not update exp for nobody.
				if ($uid != "nobody")
				$this->auth["exp"] = time() + (60 * $this->lifetime);
				return true;
				break;
			case 3:
				# Login in progress, check results and act accordingly
				if ($uid = $this->auth_validatelogin($id_org) ) {
					$this->auth["uid"] = $uid;
					$this->auth["exp"] = time() + (60 * $this->lifetime);
					return true;
				} else {
					//$this->auth_loginform();
					$this->auth["uid"] = "form";
					$this->auth["exp"] = 0x7fffffff;
					$this->freeze();
					return false;//exit;
				}
				break;
			default:
				# This should never happen. Complain.
				echo "Error in auth handling: invalid state reached.\n";
				$this->freeze();
				return false;//exit;
				break;
		}
	}

	/**
	 * Unauthorized.
	 **/
	function unauth($nobody = false) {
		$this->auth["uid"]   = "";
		$this->auth["perm"]  = "";
		$this->auth["exp"]   = 0;

		## Back compatibility: passing $nobody to this method is
		## deprecated
		if ($nobody) {
			$this->auth["uid"]   = "nobody";
			$this->auth["perm"]  = "";
			$this->auth["exp"]   = 0x7fffffff;
		}
	}

	/**
	 * Logout function.
	 **/
	function logout($nobody = "") {
		//$this->unregister("auth");
		unset($this->auth["uname"]);
		$this->unauth($nobody == "" ? $this->nobody : $nobody);
		$this->delete();
	}
	/**
	 * Is authenticated.
	 * Check if user is authenticated.
	 **/
	function is_authenticated() {
		if (
		isset($this->auth["uid"])
		&&
		$this->auth["uid"]
		&&
		(($this->lifetime <= 0) || (time() < $this->auth["exp"]))
		) {
			# session is valid (registered, not expired).
			//if($this->auth["uid"]!="form" && isset($this->database_class)) {
				//$this->db = new $this->database_class;				
				//$this->db->query("select user_id, name_user ".
                 //            " from "._TABLE_PREFIX."_auth_user".
                 //            " where user_id = '".$this->auth["uid"]."' ");
				//if ($this->db->num_rows()==1) {
				 //  $this->db->next_record();
				 //  $this->auth["nm_user"] = $this->db->f("name_user");				
				//} else return false;
				
			//}
				
			return $this->auth["uid"];
		} else {
			return false;
		}
	}
	/**
	 * Validate Login.
	 * Validate user username and password from table $this->database_table.
	 * Get user position after login validation.
	 **/
	function auth_validatelogin($id_org=null) {
		//global $_POST;

		if(isset($_POST["username"])) {
			$this->auth["uname"] = $_POST["username"];   ## This provides access for "loginform.ihtml"
		}

		$uid = false;

		if (isset($_POST["username"])) $post_vars_username=$_POST["username"]; else $post_vars_username=Null;
		if (isset($_POST["password"])) $post_vars_password=$_POST["password"]; else $post_vars_password=Null;

		if(isset($this->database_class)) {
			$this->db = new $this->database_class;
		}
		$where="";
		$join="";
		if ($this->database_table==_TABLE_PREFIX."_accounts")
		{$Qstring="id_account as user_id, id_org";$where=" and id_org=$id_org and tp_state=1";}
		else {
			$Qstring=_TABLE_PREFIX."_auth_user.user_id, id_perm, "._TABLE_PREFIX."_auth_user.id_org";
			$join = " left join "._TABLE_PREFIX."_position on "._TABLE_PREFIX."_auth_user.user_id = "._TABLE_PREFIX."_position.user_id"
			." left join "._TABLE_PREFIX."_org on "._TABLE_PREFIX."_position.id_org = "._TABLE_PREFIX."_org.id_org";
		}

		$this->db->query(sprintf("select $Qstring ".
                             "        from %s $join".
                             "       where username = '%s' ".
                             "         and password = '%s'$where",
		$this->database_table,
		addslashes($post_vars_username),
		addslashes($post_vars_password)));

		while($this->db->next_record()) {
			$uid = $this->db->f("user_id");
			//$this->auth["id_uid"] = $uid;

			$this->auth["perm"] = $this->db->f("id_perm");
			$this->auth["id_org"] = $this->db->f("id_org");
		}
		// Get position of the user_id if position_table is as class var
		if ($this->position_table) {
			$this->db->query(sprintf("select id_position ".
                             "        from %s ".
                             "        where user_id='%s'",
			$this->position_table, $uid));
			while($this->db->next_record()) {
				$this->auth["id_position"]= $this->db->f("id_position");
			}
		}
		return $uid;
	}
	//***********************************************************************************

}
?>
