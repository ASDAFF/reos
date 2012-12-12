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
*Returns xml node for menu of members.
*Creates an menu option for each type of transaction.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/mnu_perfil.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

$id_account=$this->auth["uid"];


if (isset($id_account)&& $id_account!="")
  {
  require_once(_DirINCLUDES."class_account.php");
  $dbi= new account;
  $dbi->dtl_account($id_account);
  $this->add_msg($dbi->txt_error);
  //extract($dbi->Record);
  
  if (isset($dbi->Record["ind_active"])) {$tmp["url"]="";$tmp["txt"]=""._VER_PERFIL."";}
                           else {$tmp["url"]="&view=Add";$tmp["txt"]=""._ADD_PERFIL."";}


  $mnu[0]["title"]=""._CUSTZONE."";
  $mnu[0]["name"]="mnu_members";

  $mnu[0]["links"][0]["name"]="mydata";
  $mnu[0]["links"][0]["txt"]=""._DATPERS."";
  $mnu[0]["links"][0]["href"]="pg=edtacc&id_account=$id_account&nm="._DATPERS."";

//*********************

  foreach($mnu as $mnu_tit){
    $this->html_out .="<".$mnu_tit["name"]." title=\"".$mnu_tit["title"]."\">";
    $this->html_out .="<moptions>";
    foreach ($mnu_tit["links"] as $mnu_links)
         {
          $this->html_out .= "<".$mnu_links["name"]." href=\"".$this->getPermalink($mnu_links["txt"])."".$this->url_encrypt("".$mnu_links["href"]."")."\">"
          .$mnu_links["txt"]
          ."</".$mnu_links["name"].">";
         } // end second foreach
    $this->html_out .="</moptions>";
    $this->html_out .="</".$mnu_tit["name"].">";
    } // end firts foreach

  unset($mnu);
  unset($dbi);
  } // end if isset id_account

?>
