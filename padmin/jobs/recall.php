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
 * Sends a confirmation subscribtion recall.
 **/
/**
 * Smtp Class.
 **/
require_once(_DirINCLUDES."email/smtp.php");
/**
 * Account Class.
 **/
require_once(_DirINCLUDES."class_account.php");

$accounts= new account;
$smtp=new smtp_class;
$smtp->debug=0;               /* Set to 1 to output the communication with the SMTP server */
$smtp->html_debug=0;

$smtp->type=_MAIL_FUNCTION;    // set to 1 to use php mail function, set to 0 to uses smtp

$op=0;

//Deletes accounts with more than 20 days without confirmation
$accounts->query("delete from ".$accounts->prefix."_accounts where tp_state = 3 and DATEDIFF(NOW(),dt_create)>=20;");

if ($accounts->ToValidate($id_org_session)) {

	$subject=_MAIL_SUBS;	
	$html = false;	
	$to=$accounts->select_array();	
	$msg = "";
	$unreg="\n\n"._TXT_UNREG." http://"._UrlSITE."/".LK_PAG."ID_ACCOUNT\n\n";
	
	foreach($to as $i=>$row) {
		$to[$i]["id_account"]=$accounts->url_encrypt("pg=unreg&id_account=".$to[$i]["id_account"]."")."&idma=".$to[$i]["cod_lang"];
		$to[$i]["msg"] = _MAIL_SUBS_TXT."http://"._UrlSITE."/".LK_PAG.$accounts->url_encrypt("pg=confirm&op=$op&usr=".$to[$i]["username"]."&psw=".$to[$i]["password"]."")."\n\n"._LABEL_ORG." - "._SAT_DEPT.$unreg;
	}	
	$smtp->sendemail(_SAT_EMAIL,_LABEL_ORG." - "._SAT_DEPT,$to,$subject, $msg, $html);		 
}
?>
