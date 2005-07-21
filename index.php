<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.5 2005/07/21 09:39:31 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'CalendarLib.php' );

# perms are 
# 	$bit_p_view_calendar
# 	$bit_p_admin_calendar
# 	$bit_p_change_events
# 	$bit_p_add_events
$gBitSystem->isPackageActive('calendar', TRUE);

$bufid = array();
$bufdata = array();
$modifiable = array();

$calendarlib = new CalendarLib();
$calendarlib->loadContentTypes();		

// setup list of bit items displayed
if (isset($_REQUEST["bitcals"])and is_array($_REQUEST["bitcals"])and count($_REQUEST["bitcals"])) {
	$_SESSION['CalendarViewBitCals'] = $_REQUEST["bitcals"];
} elseif (!isset($_SESSION['CalendarViewBitCals'])) {
	$_SESSION['CalendarViewBitCals'] = array();
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["bitcals"])) {
	$_SESSION['CalendarViewBitCals'] = array();
}

// this should be a global array generated by the some object in the kernel
$bitItems = $calendarlib->gContentTypes;
$smarty->assign('bitItems', $bitItems);

$bitcal = array();

foreach ($_SESSION['CalendarViewBitCals'] as $calt) {
	$bitcal["$calt"] = 1;
}

$trunc = "12"; // put in a pref, number of chars displayed in cal cells
$smarty->assign('bitcal', $bitcal);

if (isset($_REQUEST["todate"]) && $_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST['todate'];
} elseif (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
} else {
	$_SESSION['CalendarFocusDate'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
}

$focusdate = $_REQUEST['todate'];
list($focus_day, $focus_month, $focus_year) = array(
	date("d", $focusdate),
	date("m", $focusdate),
	date("Y", $focusdate)
);
$focusdate = mktime(0,0,0,$focus_month,$focus_day,$focus_year);

if (isset($_REQUEST["viewmode"]) and $_REQUEST["viewmode"]) {
	$_SESSION['CalendarViewMode'] = $_REQUEST["viewmode"];
}

if (!isset($_SESSION['CalendarViewMode']) or !$_SESSION['CalendarViewMode']) {
	$_SESSION['CalendarViewMode'] = 'month';
}
$smarty->assign('viewmode', $_SESSION['CalendarViewMode']);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$z = date("z");

$focus_prevday = mktime(0, 0, 0, $focus_month, $focus_day - 1, $focus_year);
$focus_nextday = mktime(0, 0, 0, $focus_month, $focus_day + 1, $focus_year);
$focus_prevweek = mktime(0, 0, 0, $focus_month, $focus_day - 7, $focus_year);
$focus_nextweek = mktime(0, 0, 0, $focus_month, $focus_day + 7, $focus_year);
$focus_prevmonth = mktime(0, 0, 0, $focus_month - 1, $focus_day, $focus_year);
$focus_nextmonth = mktime(0, 0, 0, $focus_month + 1, $focus_day, $focus_year);

$smarty->assign('daybefore', $focus_prevday);
$smarty->assign('weekbefore', $focus_prevweek);
$smarty->assign('monthbefore', $focus_prevmonth);
$smarty->assign('dayafter', $focus_nextday);
$smarty->assign('weekafter', $focus_nextweek);
$smarty->assign('monthafter', $focus_nextmonth);
$smarty->assign('focusmonth', $focus_month);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('now', mktime(0, 0, 0, date('m'), date('d'), date('Y')));

$weekdays = range(0, 6);

$d = 60 * 60 * 24;
$currentweek = date("W", $focusdate);
$wd = date('w', $focusdate);

#if ($wd == 0) $w = 7;
#$wd--;

// calculate timespan for sql query
if ($_SESSION['CalendarViewMode'] == 'month') {
   $viewstart = mktime(0,0,0,$focus_month, 1, $focus_year);
   $TmpWeekday = date("w",$viewstart);
   // move viewstart back to Sunday....
   $viewstart -= $TmpWeekday * $d;
   // this is the last day of $focus_month
   $viewend = mktime(0,0,0,$focus_month + 1, 0, $focus_year);
   $TmpWeekday = date("w", $viewend);
   $viewend += (6 - $TmpWeekday) * $d;
   $viewend -= 1;
   // ISO weeks --- kinda mangled because ours begin on Sunday...
   $firstweek = date("W", $viewstart + $d);
   $lastweek = date("W", $viewend);
   if ($lastweek < $firstweek) {
	   if ($currentweek < $firstweek) {
		   $firstweek -= 52;
	   } else {
		   $lastweek += 52;
	   }
   }
   $numberofweeks = $lastweek - $firstweek;
} elseif ($_SESSION['CalendarViewMode'] == 'week') {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   // start by putting $viewstart at midnight starting focusdate
   $viewstart = mktime(0,0,0,$focus_month, $focus_day, $focus_year);
   // then back up to the preceding Sunday;
   $viewstart -= $wd * $d;
   // then go to the end of the week for $viewend
   $viewend = $viewstart + ((7 * $d) - 1);
   $numberofweeks = 0;
} else {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   $viewstart = mktime(0,0,0,$focus_month, $focus_day, $focus_year);
   $viewend = $viewstart + ($d - 1);
   $weekdays = array(date('w',$focusdate));
   $numberofweeks = 0;
}

// untested (by me, anyway!) function grabbed from the php.net site:
// [2004/01/05:rpg]
function m_weeks($y, $m){
  // monthday array
  $monthdays = array(1=>31, 3=>31, 4=>30, 5=>31, 6=>30,7=>31,
               8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
  // weekdays remaining in a week starting on 7 - Sunday...(could be changed)
  $weekdays = array(7=>7, 1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1);
  $date = mktime( 0, 0, 0, $m, 1, $y);
  $leap = date("L", $date);
  // if it is a leap year set February to 29 days, otherwise 28
  $monthdays[2] = ($leap ? 29 : 28);
  // get the weekday of the first day of the month
  $wn = strftime("%u",$date);
  $days = $monthdays[$m] - $weekdays[$wn];
  return (ceil($days/7)+1);
}


$smarty->assign('viewstart', $viewstart);
$smarty->assign('viewend', $viewend);
$smarty->assign('numberofweeks', $numberofweeks);

$daysnames = array(
	tra("Sunday"),
	tra("Monday"),
	tra("Tuesday"),
	tra("Wednesday"),
	tra("Thursday"),
	tra("Friday"),
	tra("Saturday")
);

$weeks = array();
$cell = array();

if ($_SESSION['CalendarViewGroups']) {
	$listevents = $calendarlib->listItems($_SESSION['CalendarViewGroups'], $gBitUser->mUserId, $viewstart, $viewend, 0, 50, 'name_desc', '');
} else {
	$listevents = array();
}
if ($_SESSION['CalendarViewBitCals']) {
	$listbitevents = $calendarlib->listBitItems($_SESSION['CalendarViewBitCals'], $gBitUser->mUserId, $viewstart, $viewend, 0, 50, 'name_desc', '');
} else {
	$listbitevents = array();
}

define("weekInSeconds", 604800);
// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
for ($i = 0; $i <= $numberofweeks; $i++) {
	$wee = $firstweek + $i;

	$weeks[] = $wee;

	// $startOfWeek is a unix timestamp
	$startOfWeek = $viewstart + $i * weekInSeconds;

	foreach ($weekdays as $w) {
		$leday = array();
	    if ($_SESSION['CalendarViewMode'] == 'day') {
			$dday = $startOfWeek;
		} else {
			$dday = $startOfWeek + $d * $w;
		}

		$cell[$i][$w]['day'] = $dday;

		if (isset($listevents["$dday"])) {
			$e = 0;

			foreach ($listevents["$dday"] as $le) {
				$leday["{$le['time']}$e"] = $le;

				$smarty->assign_by_ref('cellextra', $le["extra"]);
				$smarty->assign_by_ref('cellhead', $le["head"]);
				$smarty->assign_by_ref('cellprio', $le["prio"]);
				$smarty->assign_by_ref('cellcalname', $le["calname"]);
				$smarty->assign_by_ref('cellname', $le["name"]);
				$smarty->assign_by_ref('celldescription', $le["description"]);
				$leday["{$le['time']}$e"]["over"] = $smarty->fetch("bitpackage:calendar/calendar_box.tpl");
				$e++;
			}
		}

		if (isset($listbitevents["$dday"])) {
			$e = 0;

			foreach ($listbitevents["$dday"] as $lte) {
				$leday["{$lte['time']}$e"] = $lte;

				$smarty->assign('cellextra', "");
				$smarty->assign_by_ref('cellhead', $lte["head"]);
				$smarty->assign_by_ref('cellprio', $lte["prio"]);
				$smarty->assign_by_ref('cellcalname', $lte["calname"]);
				$smarty->assign_by_ref('cellname', $lte["name"]);
				$smarty->assign_by_ref('celldescription', $lte["description"]);
				$leday["{$lte['time']}$e"]["over"] = $smarty->fetch("bitpackage:calendar/calendar_box.tpl");
				$e++;
			}
		}

		if (is_array($leday)) {
			ksort ($leday);
			$cell[$i][$w]['items'] = array_values($leday);
		}
	}
}

$hrows = array();
if ($_SESSION['CalendarViewMode'] == 'day') {
	foreach ($cell[0]["{$weekdays[0]}"]['items'] as $dayitems) {
		$rawhour = substr($dayitems['time'],0,2);
		$dayitems['mins'] = substr($dayitems['time'],2);
		$hrows["$rawhour"][] = $dayitems;
	}
}
$hours = array(  '0:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00',
				 '8:00', '8:00','10:00','11:00','12:00','13:00','14:00','15:00',
				'16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00');

$smarty->assign('hrows', $hrows); 
$smarty->assign('hours', $hours); 

$smarty->assign('trunc', $trunc); 
$smarty->assign('daformat', $gBitSystem->get_long_date_format()." ".tra("at")." %H:%M"); 
$smarty->assign('daformat2', $gBitSystem->get_long_date_format()); 
$smarty->assign('currentweek', $currentweek);
$smarty->assign('firstweek', $firstweek);
$smarty->assign('lastweek', $lastweek);
$smarty->assign('weekdays', $weekdays);
$smarty->assign('weeks', $weeks);
$smarty->assign('daysnames', $daysnames);
$smarty->assign('cell', $cell);
$smarty->assign('var', '');

$section = 'calendar';


$gBitSystem->display( 'bitpackage:calendar/calendar.tpl');

//echo "<pre>";print_r($cell);echo "</pre>";
?>
