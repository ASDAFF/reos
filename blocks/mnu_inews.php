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
*Returns xml node for menu option that link to immos order by latest date.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/mnu_inews.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

$imnu=0;
//*********************

$mnu[$imnu]["title"]=""._NOVED."";
$mnu[$imnu]["name"]="mnu_inews";

$mnu[$imnu]["links"][0]["name"]="immo_news";
$mnu[$imnu]["links"][0]["txt"]=""._NOVED_TXT."";
$mnu[$imnu]["links"][0]["href"]="pg=verimmo&show=0&keywords=tp_state=1,order_by=dt_create DESC&nm="._NOVED."";

//*********************

reset($mnu);

foreach($mnu as $mnu_tit){
  $this->html_out .="<".$mnu_tit["name"]." title=\"".$mnu_tit["title"]."\">";
  $this->html_out .="<moptions>";
  foreach ($mnu_tit["links"] as $mnu_links)
       {
        $this->html_out .= "<".$mnu_links["name"]." href=\"".$this->getPermalink($mnu_links["txt"])."".$this->url_encrypt("".$mnu_links["href"]."")."\">"
        .$mnu_links["txt"]
        ."</".$mnu_links["name"].">";
       }
   $this->html_out .="</moptions>";
  $this->html_out .="</".$mnu_tit["name"].">";
}

unset($mnu);

?>
