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
 * Sends massive emails using table mailing.
 **/
/**
 * Smtp Class.
 **/
require_once(_DirINCLUDES."email/smtp.php");
/**
 * Account Class.
 **/
require_once(_DirINCLUDES."class_account.php");
/**
 * Mailing Class.
 **/
require_once(_DirINCLUDES."class_mailing.php");
/**
 * News Class.
 **/
require_once(_DirINCLUDES."class_noticia.php");


$mailings= new mailing;

$mailings->mail_jobs($id_org_session);

if ($all_jobs=$mailings->select_array()) {

	$accounts= new account;
	$smtp=new smtp_class;
	$smtp->debug=0;               /* Set to 1 to output the communication with the SMTP server */
	$smtp->html_debug=0;

	$smtp->type=_MAIL_FUNCTION;    // set to 1 to use php mail function, set to 0 to uses smtp
	//$smtp->type=0;    // set to 1 to use php mail function, set to 0 to uses smtp

	foreach ($all_jobs as $job) {
		$fields["tp_state"]=4;
		$mailings->update_mailing($job["id_mailing"],$fields);

		if ($accounts->mailing($id_org_session,$job["tp_send_to"],$job["txt_idioma"])) {

			$subject=$job["txt_subject"];

			if ($job["tp_send"]==2) $html=true;else $html=false;

			if (isset($job["id_news"])) {
				if ($html)
				{
					$url=$accounts->url_encrypt("pg=cmt_mail new&prt=navmail&nm=&id_news=".$job["id_news"]);					
					
					$msg=file_get_contents("http://"._UrlSITE."/".LK_PAG."$url&idma=".$job["txt_idioma"]);					
					
					$pattern[] = '/href="(?!http|https|ftp|irc|feed|mailto)([\/]?)(.*)"/ismU';
					$replace[] = 'href="http://'._UrlSITE.'/$2"';
					$pattern[] = '/src="(?!http)([\/]?)(.*)"/ismU';
					$replace[] = 'src="http://'._UrlSITE.'/$2"';
					
					$msg = preg_replace($pattern, $replace, $msg);
					
					//$msg=str_replace("href=\"","href=\"http://"._UrlSITE."/", $msg);
					//$msg=str_replace("src=\"","src=\"http://"._UrlSITE."/", $msg);
				}
				else
				{
					$noticia= new noticia;
					$noticia->dtl_noticia($job["id_news"]);
					$noticia->next_record();
					$resum=preg_replace('/<br \/>/', '', $noticia->Record["txt_resum"]);
					$texto=preg_replace('/<br \/>/', '', $noticia->Record["txt_content"]);
					$unreg=_TXT_UNREG." http://"._UrlSITE."/".LK_PAG."ID_ACCOUNT\n\n";
					$msg="\n".$noticia->Record["txt_title"]."\n\n".$resum."\n\n".$texto."\n\n$unreg"._LABEL_ORG."\nhttp://"._UrlSITE;
				}
			} else $msg="";


			$to=$accounts->select_array();
			foreach($to as $i=>$row)
			$to[$i]["id_account"]=$accounts->url_encrypt("pg=unreg&id_account=".$to[$i]["id_account"]."")."&idma=".$job["txt_idioma"];
			$smtp->sendemail(_SAT_EMAIL,_LABEL_ORG,$to,$subject, $msg, $html);
		}

		$fields["tp_state"]=2;
		$fields["dt_sent"]=date(""._DATE_FORMAT."");
		if (isset($msg)) $fields["txt_content"]=$msg;
		if (isset($to))$fields["num_sent"]=count($to); else $fields["num_sent"]=0;

		$mailings->update_mailing($job["id_mailing"],$fields);
	}
}

?>
