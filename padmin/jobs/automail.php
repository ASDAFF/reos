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
 * Includes class to get mail box messages
 */
require_once(_DirINCLUDES."class_mailbox.php");
/**
 * Class Support.
 */
require_once(_DirINCLUDES."class_support.php");
/**
 * Class SMTP : Used to send emails
 */
require_once(_DirINCLUDES."email/smtp.php");

global $id_org_session;

$Support = new support();
$Support->Debug = 0;
$Support->verctg_list(0,1000);
$ctgList = $Support->select_array();

foreach($ctgList as $ctg){
	$tmpMail = new MailBox($ctg["pophost"],$ctg["popuser"],$ctg["poppass"]);
	if (!$tmpMail->GetMessages()){		
	} else {
		$Mail[$ctg["id_tk_ctg"]]=$tmpMail;
		$CtgSign[$ctg["id_tk_ctg"]]=$ctg["txt_sign"];
	}    
}

if (count($Mail)>0) {

	$MsgByLang=$Support->GetMsgText();

	$smtp = new smtp_class;	
	$smtp->debug = false;

	foreach($Mail as $id_tk_ctg=>$ctgBox){

		//data to send mails from Suport Category
		$smtp->type = 0;  // if 0 uses SMTP server, if 1 uses php mail function	
		$smtp->host_name=$ctgBox->host;
		$smtp->user=$ctgBox->popuser;
		$smtp->password=$ctgBox->poppass;
		
		// Getting Depts.
		$Support->ctg_orgs_list($id_tk_ctg);
		$orgs = $Support->select_array();
		// For each Support Dept.
		foreach($orgs as $org){
			$Support->org_positions($org["id_org"]);
			$positions = $Support->select_array();
			// For each Position within a Support Dept.
			// Saves each position in an array
			foreach($positions as $wkpst){
				$pst[]=$Support->position($wkpst["id_position"]);
				$pst[count($pst)-1]["id_position"]=$wkpst["id_position"]; //adding id_position
			}
			 
			$numMAils = count($ctgBox->eMails);
			$numPst = count($pst);
			$cnt=0;
			// For each email received
			foreach($ctgBox->eMails as $MailItem){
				$contact=false;
				//Check if email from exists in Contact Account.
				// if yes saves contact or account data to an array
				if ($Support->GetContactAccount($MailItem->email)) {
					$contact=$Support->select_array();
				}
				// if email is html
				if ($MailItem->html){
					$pos=strpos($MailItem->body,"---startline---");
					if ($pos===false)
					$fields["txt_msg"]=$MailItem->body;
					else
					$fields["txt_msg"]=substr($MailItem->body,0,$pos);
				} else {
					$pos=strpos($MailItem->body,"---startline---");
					if ($pos===false)
					$fields["txt_msg"]=nl2br($MailItem->body);
					else
					$fields["txt_msg"]=nl2br(substr($MailItem->body,0,$pos));
				}
		
				$fields["fg_private"]="0";
				// Trying to get Ticket number from Subject
				if (!preg_match ("/[[][#][A-Z0-9]{10}[]]/", $MailItem->subject, $regs)) {
					// New ticket
					if ($contact) {
						$fields["id_contact"]=$contact[0]["id_contact"];
						$fields["id_account"]=$contact[0]["id_account"];
						$fields["cod_lang"]=$contact[0]["cod_lang"];
					} else {
						$fields["cod_lang"]=_DEFAULT_LANG;
					}
					 
					$fields["nm_account"]=$MailItem->name;
					$fields["txt_subject"]=$MailItem->subject;
					$fields["txt_email"]=$MailItem->email;
					 
					$fields["ref_ticket"]="";
					 
					$fields["id_tk_ctg"]="$id_tk_ctg";
					 
					$fields["dt_create"]="NOW()";
					$fields["tp_status"]="1";
					$fields["tp_priority"]="3";
					$fields["ref_ticket"]=$Support->GenerateTkRef();
	 			if ($Support->add_ticket($id_org_session,$pst[$cnt]["id_position"],null,$fields)){
	 				$smtp->host_name=$Mail[$id_tk_ctg]->host;
		 			$smtp->user=$Mail[$id_tk_ctg]->popuser;
		 			$smtp->password==$Mail[$id_tk_ctg]->poppass;
		 			$to[0]["name"]=$MailItem->name;
		 			$to[0]["email"]=$MailItem->email;	 			
		 			$smtp->sendemail($Mail[$id_tk_ctg]->popuser,$CtgSign[$id_tk_ctg],$to,"[#".$fields["ref_ticket"]."] ".$MailItem->subject,"---startline---\n".$MsgByLang[$fields["cod_lang"]]["_TK_OPEN"]["txt_email"], false);
	 			}
	 		} else {
	 			// Ticket already exists	 			
	 			$ref_ticket = substr(strstr($regs[0], "[#"), 2, 10);
	 			$id_ticket=$Support->GetTicketByRef($ref_ticket);
	 			if ($id_ticket) {
	 				if ($contact) {
	 					$fields["id_contact"]=$contact[0]["id_contact"];
	 					$cod_lang=$contact[0]["cod_lang"];
	 				} else
	 				$cod_lang=_DEFAULT_LANG;
	 				$fields["fg_closed"]="0";
	 				unset($fields["cod_lang"]);
	 				if ($Support->add_msg($id_ticket, null, $fields)){
	 					$smtp->host_name=$Mail[$id_tk_ctg]->host;
			 			$smtp->user=$Mail[$id_tk_ctg]->popuser;
			 			$smtp->password==$Mail[$id_tk_ctg]->poppass;
			 			$to[0]["name"]=$MailItem->name;
			 			$to[0]["email"]=$MailItem->email;			 			
			 			$smtp->sendemail($Mail[$id_tk_ctg]->popuser,$CtgSign[$id_tk_ctg],$to,$MailItem->subject,"---startline---\n".$MsgByLang[$cod_lang]["_TK_REPLY"]["txt_email"], false);
	 				}
	 			}
	 		}
	 		echo $Support->txt_error;
	 		if ($cnt==($numPst-1)) $cnt=0; else $cnt++;
			} // End foreach email received
		}
	}
}
?>
