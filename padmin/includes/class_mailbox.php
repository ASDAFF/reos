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
 *
 */
class Mail {
	var $name;
	var $email;
	var $subject;
	var $body;
	var $header;
	var $html;
}
/**
 * Class to open a pop3 mailbox.
 */
class MailBox {
	var $eMails;
	var $host;
	var $popuser;
	var $poppass;
	var $debug;

	/**
	 * Enter description here...
	 *
	 * @return MailBox
	 */
	function MailBox($host, $popuser, $poppass) {
		if (!function_exists('imap_open')) {
			echo "IMAP functions are not available.<br/>";
		}
		$this->host=$host;
		$this->popuser=$popuser;
		$this->poppass=$poppass;
		$this->debug=false;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $structure
	 * @return unknown
	 */
	function get_mime_type(&$structure) {
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

		if($structure->subtype) {
			return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
		}
		return "TEXT/PLAIN";
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $stream
	 * @param unknown_type $msg_number
	 * @param unknown_type $mime_type
	 * @param unknown_type $structure
	 * @param unknown_type $part_number
	 * @return unknown
	 */
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
		
		$structure = imap_bodystruct($stream, $msg_number, 1);

		if($structure) {			
			if($mime_type == $this->get_mime_type($structure)) {
				if(!$part_number) {
					$part_number = "1";
				}							
				$text = imap_fetchbody($stream, $msg_number, $part_number);
				if($structure->encoding == 3) {
					return imap_base64($text);
				} else if($structure->encoding == 4) {
					return imap_qprint($text);
				} else {
					return $text;
				}		
			}		
		}
		return false;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $stream
	 * @param unknown_type $msg_number
	 * @param unknown_type $mime_type
	 * @param unknown_type $structure
	 * @param unknown_type $part_number
	 * @return unknown
	 */
	function get_part_old($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
		
		if(!$structure) {
			$structure = imap_fetchstructure($stream, $msg_number);
		}		
		
		if($structure) {			
			if($mime_type == $this->get_mime_type($structure)) {
				if(!$part_number) {
					$part_number = "1";
				}				
				$text = imap_fetchbody($stream, $msg_number, $part_number);
				if($structure->encoding == 3) {
					return imap_base64($text);
				} else if($structure->encoding == 4) {
					return imap_qprint($text);
				} else {
					return $text;
				}
			}			
			if($structure->type == 1) /* multipart */ {
				while(list($index, $sub_structure) = each($structure->parts)) {
					if($part_number) {
						$prefix = $part_number . '.';
					} else $prefix = "";
					$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
					if($data) {
						return $data;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Get messages from Mail Box.
	 */
	function GetMessages(){		
		if ($this->debug) echo "Trying to open imap.<br/>";
		
		if ($this->host) {
			$mbox = imap_open("{" . $this->host . "/pop3:110}INBOX", $this->popuser, $this->poppass);
			if (!$mbox) {
				$mbox = imap_open("{" . $this->host . "/pop3/notls}INBOX", $this->popuser, $this->poppass);
			}						
			if (!$mbox) {
				echo "Unable to open mailbox.<br/>";
			} else {				
				$curmsg = 1;
				if ($this->debug) echo "Trying to get messages...<br/>";
				
				while ($curmsg <= imap_num_msg($mbox)) {					
					if ($this->debug) echo "Getting message.<br/>";
					$this->eMails[$curmsg-1] = new Mail;
					$this->eMails[$curmsg-1]->html=true;	
										
					$body = $this->get_part($mbox, $curmsg, "TEXT/HTML");					
					if (!$body) {
						$body = $this->get_part($mbox, $curmsg, "TEXT/PLAIN");
						$this->eMails[$curmsg-1]->html=false;
					}
					/*
					 if (!$body) {
					 continue;
					 }*/
					$this->eMails[$curmsg-1]->body = $body;
					$head = imap_headerinfo($mbox, $curmsg, 800, 800);
					$this->eMails[$curmsg-1]->header=$head;
					$email = $head->reply_toaddress;

					if (strpos($email, "<")) {
						$email = preg_replace("/.*<(.*)>.*/i", "\\1", $email);
					}
					if (substr($head->fromaddress, 0,2)=="=?") {
						$elements=imap_mime_header_decode($head->fromaddress);
						$name=$elements[0]->text;
					} else $name = $head->fromaddress;

					if (strpos($name, "<")) {
						$name = preg_replace("/(.*) <.*>.*/i", "\\1", $name);
					}

					if (!$name) {
						$name = $email;
					}

					if (substr($head->fetchsubject, 0,2)=="=?") {
						$elements=imap_mime_header_decode($head->fetchsubject);
						$subject=$elements[0]->text;
					} else {
						$subject = $head->fetchsubject;
					}

					$subject = !$subject ? "[No Subject]": $subject;
					$eml_headers = imap_fetchheader($mbox, $curmsg, FT_PREFETCHTEXT);
					$x_pri = split("\n", $eml_headers);
					foreach ($x_pri as $item) {
						$arr = split(": ", $item);
						if (preg_match("/x-priority/i", $arr[0])) {
							if (strstr($arr[1], "1") or strstr($arr[1], "2")) {
								$pri = 3;
							}
							elseif (strstr($arr[1], "4") or strstr($arr[1], "5")) {
								$pri = 1;
							}
						}
						elseif (preg_match("/X-Originating-IP/i", $arr[0])) {
							$ip = $arr[1];
							$ip = preg_replace("/\[(.*)\]/i", "\\1", $ip);
						}
					}
					if ($this->debug) echo "Getting body, email, subject.<br/>";
					$body = ltrim(rtrim($body));
					$this->eMails[$curmsg-1]->name=$name;
					$this->eMails[$curmsg-1]->email=$email;
					$this->eMails[$curmsg-1]->subject=$subject;
					$this->eMails[$curmsg-1]->body=$body;
					imap_delete($mbox, $curmsg);
					$curmsg++;
				} // end While
				if ($this->debug) echo "Getting message end.<br/>";
				imap_expunge($mbox);
				if ($this->debug) echo "Closing mailbox.<br/>";
				imap_close($mbox);
				if ($curmsg>1) return true; else return false;
			}
		} else return false; // end if ($host)
	}
	 /**
     * Method used to get the information about a specific message
     * from a given mailbox.
     *
     * XXX this function does more than that.
     *
     * @access  public
     * @param   resource $mbox The mailbox
     * @param   array $info The support email account information
     * @param   integer $num The index of the message
     * @return  void
     */
    function getEmailInfo($mbox, $info, $num)
    {
        

        // check if the current message was already seen
        if ($info['ema_get_only_new']) {
            list($overview) = @imap_fetch_overview($mbox, $num);
            if (($overview->seen) || ($overview->deleted) || ($overview->answered)) {
                return;
            }
        }

        $email = @imap_headerinfo($mbox, $num);
        $headers = imap_fetchheader($mbox, $num);
        $body = imap_body($mbox, $num);
        // check for mysterious blank messages
        if (empty($body) and empty($headers)) {
            // XXX do some error reporting?
            return;
        }
        
        $t = array(
            //'ema_id'         => $info['ema_id'],
            //'message_id'     => $message_id,
            //'date'           => @Date_API::getDateGMTByTS($email->udate),
            'from'           => $sender_email,
            'to'             => @$email->toaddress,
            'cc'             => @$email->ccaddress,
            'subject'        => @$email->subject,
            'body'           => @$body,
            'full_email'     => @$message
            //'has_attachment' => $has_attachments,
            // the following items are not inserted, but useful in some methods
            //'headers'        => @$structure->headers
        );
    } 
}
?>