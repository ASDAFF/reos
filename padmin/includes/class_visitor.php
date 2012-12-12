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
 * Class Visitor definition file.
 *
 * @version      1.2
 * @author       IT eLazos SL
 * @package varios
 */
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_visitor.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
/**
 * Return information about a Visitor.
 *
 * @version      1.2
 * @author       IT eLazos SL
 */
class Visitor
{
    /**
     * Return the visitor's IP.
     *
     * @return       string The visitor's IP address
     */
    function ip ()
    {
        if (isset($_SERVER['X_FORWARDED_FOR'])) {
            $ip = $_SERVER['X_FORWARDED_FOR'];
                } else {
            $ip = $_SERVER['REMOTE_ADDR'];
                }

        return $ip;
    }


    /**
     * Return the visitor's Hostname.
     *
     * @return       string The visitor's IP address
     */
    function host ()
    {
        return gethostbyaddr(Visitor::ip());
    }


    /**
     * Return the visitor's Referer.
     *
     * @return       string The visitor's HTTP_REFERER address
     */
    function referer ()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
                } else {
            $referer = null;
                }

        return $referer;
    }

}

?>
