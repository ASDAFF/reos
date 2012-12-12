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
 * Class Calendar definition file
 * @author Josep Marxuach  - January 2005
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_calendar.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Handles calendar views
 * @author Josep Marxuach
 * @version 1.0
 * @copyright 2005 by Josep Marxuach
 * @package Calendar
 */
class Calendar{
	/**
	 * The start day of the week. This is the day that appears in the first column
	 * of the calendar. Sunday = 0.
	 * @var Integer
	 **/
	var $startDay = 6;

	/**
	 * The start month of the year. This is the month that appears in the first slot
	 * of the calendar in the year view. January = 1.
	 * @var Integer
	 **/
	var $startMonth = 1;
	/**
	 * The end month of the year. This is the month that appears in the last slot
	 * of the calendar in the year view. January = 1.
	 * @var Integer
	 **/
	var $endMonth=NULL;

	/**
	 *    Number of cols to show the year view.
	 *@var Integer
	 **/
	var $cols= 4;

	/**
	 *    The labels to display for the days of the week. The first entry in this array
	 *    represents Sunday.
	 *@var Array
	 **/
	var $dayNames = array(_SDAY, _MDAY, _TDAY, _WDAY, _THDAY, _FDAY, _SADAY);

	/**
	 *    The labels to display for the months of the year. The first entry in this array
	 *    represents January.
	 *@var Array
	 **/
	var $monthNames = array(_JANUARY, _FEBRUARY, _MARCH, _APRIL, _MAY, _JUNE,
	_JULY, _AUGUST, _SEPTEMBER, _OCTOBER, _NOVEMBER, _DECEMBER);


	/**
	 *    The number of days in each month. You're unlikely to want to change this...
	 *    The first entry in this array represents January.
	 *@var Array
	 **/
	var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	/**
	 *    Contains the booking/bussy days
	 *@var Array
	 **/
	Var $busydays=array();
	/**
	 *  Contains the day intervals of seasons and prices
	 *@var Array
	 **/
	var $seasons=array();
	/**
	 *    Constructor for the Calendar class
	 **/
	function Calendar(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_TP_SDAYS',_IDIOMA);
	}


	/**
	 * Get the array of strings used to label the days of the week. This array contains seven
	 * elements, one for each day of the week. The first entry in this array represents Sunday.
	 * @access public
	 **/
	function getDayNames(){
		return $this->dayNames;
	}


	/**
	 *    Set the array of strings used to label the days of the week. This array must contain seven
	 *    elements, one for each day of the week. The first entry in this array represents Sunday.
	 *@access public
	 **/
	function setDayNames($names){
		$this->dayNames = $names;
	}

	/**
	 *    Get the array of strings used to label the months of the year. This array contains twelve
	 *    elements, one for each month of the year. The first entry in this array represents January.
	 *@access public
	 **/
	function getMonthNames(){
		return $this->monthNames;
	}

	/**
	 *    Set the array of strings used to label the months of the year. This array must contain twelve
	 *    elements, one for each month of the year. The first entry in this array represents January.
	 *@access public
	 **/
	function setMonthNames($names){
		$this->monthNames = $names;
	}
	/**
	 *    Gets the start day of the week. This is the day that appears in the first column
	 *    of the calendar. Sunday = 0.
	 *@access public
	 **/
	function getStartDay(){
		return $this->startDay;
	}

	/**
	 *    Sets the start day of the week. This is the day that appears in the first column
	 *    of the calendar. Sunday = 0.
	 *@access public
	 **/
	function setStartDay($day){
		$this->startDay = $day;
	}


	/**
	 *    Gets the start month of the year. This is the month that appears first in the year
	 *    view. January = 1.
	 *@access public
	 **/
	function getStartMonth(){
		return $this->startMonth;
	}

	/**
	 *    Sets the start month of the year. This is the month that appears first in the year
	 *    view. January = 1.
	 *@access public
	 **/
	function setStartMonth($month){
		$this->startMonth = $month;
	}


	/**
	 *    Return the URL to link to in order to display a calendar for a given month/year.
	 *    You must override this method if you want to activate the "forward" and "back"
	 *    feature of the calendar.
	 *
	 *    Note: If you return an empty string from this function, no navigation link will
	 *    be displayed. This is the default behaviour.
	 *
	 *    If the calendar is being displayed in "year" view, $month will be set to zero.
	 *@access public
	 **/
	function getCalendarLink($month, $year)	{
		return "";
	}

	/**
	 *    Return the URL to link to  for a given date.
	 *    You must override this method if you want to activate the date linking
	 *    feature of the calendar.
	 *
	 *    Note: If you return an empty string from this function, no navigation link will
	 *    be displayed. This is the default behaviour.
	 *@access public
	 **/
	function getDateLink($day, $month, $year){
		return "";
	}


	/**
	 *    Return the HTML for the current month
	 *@access public
	 **/
	function getCurrentMonthView(){
		$d = getdate(time());
		return $this->getMonthView($d["mon"], $d["year"]);
	}


	/**
	 *    Return the HTML for the current year
	 *@access public
	 **/
	function getCurrentYearView(){
		$d = getdate(time());
		return $this->getYearView($d["year"]);
	}


	/**
	 *    Return the HTML for a specified month
	 *@access public
	 **/
	function getMonthView($month, $year){
		return $this->getMonthHTML($month, $year);
	}


	/**
	 *    Return the HTML for a specified year
	 *@access public
	 **/
	function getYearView($year, $show_year=0){
		return $this->getYearHTML($year,$show_year);
	}



	/**
	 *Calculate the number of days in a month, taking into account leap years.
	 *@access private
	 **/
	function getDaysInMonth($month, $year){
		if ($month < 1 || $month > 12)
		{
			return 0;
		}
			
		$d = $this->daysInMonth[$month - 1];
			
		if ($month == 2)
		{
			// Check for leap year
			// Forget the 4000 rule, I doubt I'll be around then...

			if ($year%4 == 0)
			{
				if ($year%100 == 0)
				{
					if ($year%400 == 0)
					{
						$d = 29;
					}
				}
				else
				{
					$d = 29;
				}
			}
		}

		return $d;
	}


	/**
	 *    Generate the HTML for a given month
	 *@access private
	 **/
	function getMonthHTML($m, $y, $showYear = 1){
		$s = "";

		$a = $this->adjustDate($m, $y);
		$month = $a[0];
		$year = $a[1];

		$daysInMonth = $this->getDaysInMonth($month, $year);
		$date = getdate(mktime(12, 0, 0, $month, 1, $year));

		$first = $date["wday"];
		$monthName = $this->monthNames[$month - 1];

		$prev = $this->adjustDate($month - 1, $year);
		$next = $this->adjustDate($month + 1, $year);

		if ($showYear == 1)
		{
			$prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
			$nextMonth = $this->getCalendarLink($next[0], $next[1]);
		}
		else
		{
			$prevMonth = "";
			$nextMonth = "";
		}

		$header = $monthName . (($showYear > 0) ? " " . $year : "");

		$s .= "<table class=\"month\">\n";
		$s .= "<tr>\n";
		$s .= "<td>" . (($prevMonth == "") ? "&#32;" : "<a href=\"$prevMonth\">&lt;&lt;</a>")  . "</td>\n";
		$s .= "<td class=\"monthhd\" colspan=\"5\">$header</td>\n";
		$s .= "<td>" . (($nextMonth == "") ? "&#32;" : "<a href=\"$nextMonth\">&gt;&gt;</a>")  . "</td>\n";
		$s .= "</tr>\n";

		$s .= "<tr>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
		$s .= "<td class=\"dayname\">" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
		$s .= "</tr>\n";

		// We need to work out what date to start at so that the first appears in the correct column
		$d = $this->startDay + 1 - $first;
		while ($d > 1)
		{
			$d -= 7;
		}

		// Make sure we know when today is, so that we can use a different CSS style
		$today = getdate(time());

		while ($d <= $daysInMonth){
			$s .= "<tr>\n";

			for ($i = 0; $i < 7; $i++)
			{
				$class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "today" : "day";

				//if (in_array(($year*100+$month)*100+$d,$this->busydays)) $class="bday";
				if ($d > 0 && $d <= $daysInMonth)
				{
					$dday=($year*100+$month)*100+$d;
					$sday=($year*100+$month)*100+$d;
					if (is_array($this->busydays) && count($this->busydays)>0)
					foreach($this->busydays as $busy) if ($busy["dt_start"]<=$dday && $busy["dt_end"]>=$dday) $class="bday";

					foreach($this->seasons as $seas) if ($seas["dt_start"]<=$sday && $seas["dt_end"]>=$sday && $class!="today")
					$class=$class.$seas["tp_sdays"];
				}

				$s .= "<td class=\"$class\">";
				if ($d > 0 && $d <= $daysInMonth)
				{
					$link = $this->getDateLink($d, $month, $year);
					$s .= (($link == "") ? $d : "<a href=\"$link\">$d</a>");
				}
				else
				{
					$s .= "&#32;";
				}
				$s .= "</td>\n";
				$d++;
			}
			$s .= "</tr>\n";
		}

		$s .= "</table>\n";

		return $s;
	}


	/**
	 *    Generate the HTML for a given year
	 *@access private
	 **/
	function getYearHTML($year, $show_year=0){
		$s = "";
		$prev = $this->getCalendarLink(0, $year - 1);
		$next = $this->getCalendarLink(0, $year + 1);

		$s .= "<table class=\"calendar\">\n";

		if (count($this->seasons)>0) $s.="<tr><td class=\"legend\" colspan=\"".$this->cols."\">".$this->getlabel()."</td></tr>";


		$s .= "<tr>";
		//    $s .= "<td>" . (($prev == "") ? "&#32;" : "<a href=\"$prev\">&lt;&lt;</a>")  . "</td>\n";

		if (!$show_year) $s .= "<td class=\"calendarHeader\" colspan=\"".$this->cols."\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";

		//    $s .= "<td>" . (($next == "") ? "&#32;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
		$s .= "</tr>\n";
		$s .= "<tr>";
		for($i=0;$i<12;$i++) {
			//if (isset($this->endMonth) && $this->endMonth<($this->startMonth+$i)) break;
			if ($i>0 && ($i%$this->cols)==0) $s .= "</tr><tr>\n";
			$s .= "<td class=\"calendar\">" . $this->getMonthHTML($i + $this->startMonth, $year, $show_year) ."</td>\n";
		}
		$s .= "</tr>\n";
		$s .= "</table>\n";

		return $s;
	}

	/**
	 *    Returns the seasons label colors with prices
	 *@access private
	 **/
	function getlabel(){
		$i=0;
		$tp[0]=array();
		foreach($this->seasons as $seas) {
			if (!in_array($seas["tp_sdays"],$tp)) {
				$vals[$i]["precio"]=$seas["precio"];
				$vals[$i]["tp_price"]=$seas["tp_price"];
				$vals[$i]["tp_sdays"]=$seas["tp_sdays"];
			}
			$tp[$i]=$seas["tp_sdays"];
			$i++;
		}
		$out="<table class=\"legend\"><tr><td class=\"legend_color\"></td></tr>";
		eval("\$lst_sdays=array('',"._LST_TP_SDAYS.");");
		foreach($vals as $val) {
			$class="day".$val["tp_sdays"];
			$out.="<tr>";

			$label=$lst_sdays[$val["tp_sdays"]];

			if ($val["precio"] || $val["precio"]!="") $precio=number_format($val["precio"],0,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY." ".$val["tp_price"];
			else $precio="";
			$out.="<td class=\"$class\">$precio</td>";
			$out.="<td class=\"callabel\">$label</td>";
			$out.="</tr>";
		}
		if (is_array($this->busydays) && count($this->busydays)>0) {
			$out.="<tr>";
			$out.="<td class=\"bday\">"._DAY."</td>";
			$out.="<td class=\"callabel\">"._BUSY."</td>";
			$out.="</tr>";
		}
		$out.="</table>";
		return $out;
	}


	/**
	 *    Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
	 *    e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
	 *@access private
	 **/
	function adjustDate($month, $year){
		$a = array();
		$a[0] = $month;
		$a[1] = $year;

		while ($a[0] > 12)
		{
			$a[0] -= 12;
			$a[1]++;
		}

		while ($a[0] <= 0)
		{
			$a[0] += 12;
			$a[1]--;
		}

		return $a;
	}



}
?>
