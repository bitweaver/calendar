<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.12 2005/08/18 21:42:37 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'Calendar.php' );

$gBitSystem->isPackageActive( 'calendar', TRUE );

$gBitSystem->verifyPermission( 'bit_p_view_calendar' );

$Calendar = new Calendar();

// setup list of bit items displayed
if (isset($_REQUEST["bitcals"])and is_array($_REQUEST["bitcals"])and count($_REQUEST["bitcals"])) {
	$_SESSION['calendarContentGuids'] = $_REQUEST["bitcals"];
} elseif (!isset($_SESSION['calendarContentGuids'])) {
	$_SESSION['calendarContentGuids'] = array();
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["bitcals"])) {
	$_SESSION['calendarContentGuids'] = array();
}

// this should be a global array generated by the some object in the kernel
$gBitSmarty->assign( 'bitItems', $bitItems = $gLibertySystem->mContentTypes );

$bitcal = array();

foreach ($_SESSION['calendarContentGuids'] as $calt) {
	$bitcal[$calt] = 1;
}
$gBitSmarty->assign('bitcal', $bitcal);

if (isset($_REQUEST["todate"]) && $_REQUEST['todate']) {
	$_SESSION['calendar_focus_date'] = $_REQUEST['todate'];
} elseif (isset($_SESSION['calendar_focus_date']) && $_SESSION['calendar_focus_date']) {
	$_REQUEST["todate"] = $_SESSION['calendar_focus_date'];
} else {
	$_SESSION['calendar_focus_date'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	$_REQUEST["todate"] = $_SESSION['calendar_focus_date'];
}

$focusdate = $_REQUEST['todate'];
list($focus_day, $focus_month, $focus_year) = array(
	date("d", $focusdate),
	date("m", $focusdate),
	date("Y", $focusdate)
);
$focusdate = mktime(0,0,0,$focus_month,$focus_day,$focus_year);

if (isset($_REQUEST["viewmode"]) and $_REQUEST["viewmode"]) {
	$_SESSION['calendar_view_mode'] = $_REQUEST["viewmode"];
}

if (!isset($_SESSION['calendar_view_mode']) or !$_SESSION['calendar_view_mode']) {
	$_SESSION['calendar_view_mode'] = 'month';
}
$gBitSmarty->assign('viewmode', $_SESSION['calendar_view_mode']);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$gBitSmarty->assign('find', $find);

$z = date("z");

$gBitSmarty->assign('daybefore',    mktime( 0, 0, 0, $focus_month, $focus_day - 1, $focus_year ) );
$gBitSmarty->assign('weekbefore',   mktime( 0, 0, 0, $focus_month, $focus_day - 7, $focus_year ) );
$gBitSmarty->assign('monthbefore',  mktime( 0, 0, 0, $focus_month - 1, $focus_day, $focus_year ) );
$gBitSmarty->assign('dayafter',     mktime( 0, 0, 0, $focus_month, $focus_day + 1, $focus_year ) );
$gBitSmarty->assign('weekafter',    mktime( 0, 0, 0, $focus_month, $focus_day + 7, $focus_year ) );
$gBitSmarty->assign('monthafter',   mktime( 0, 0, 0, $focus_month + 1, $focus_day, $focus_year ) );
$gBitSmarty->assign('focusmonth',   $focus_month );
$gBitSmarty->assign('focusdate',    $focusdate );
$gBitSmarty->assign('now',          mktime( 0, 0, 0, date('m'), date('d'), date('Y') ) );

$weekdays = range( 0, 6 );

$d = 60 * 60 * 24;
$currentweek = date("W", $focusdate);
$wd = date('w', $focusdate);

// calculate timespan for sql query
if ($_SESSION['calendar_view_mode'] == 'month') {
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
} elseif ($_SESSION['calendar_view_mode'] == 'week') {
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
  $monthdays = array( 1=>31, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31 );
  // weekdays remaining in a week starting on 7 - Sunday...(could be changed)
  $weekdays = array( 7=>7, 1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1 );
  $date = mktime( 0, 0, 0, $m, 1, $y );
  $leap = date("L", $date);
  // if it is a leap year set February to 29 days, otherwise 28
  $monthdays[2] = ($leap ? 29 : 28);
  // get the weekday of the first day of the month
  $wn = strftime("%u",$date);
  $days = $monthdays[$m] - $weekdays[$wn];
  return (ceil($days/7)+1);
}

$gBitSmarty->assign( 'viewstart', $viewstart );
$gBitSmarty->assign( 'viewend', $viewend );
$gBitSmarty->assign( 'numberofweeks', $numberofweeks );

$daysnames = array(
	tra( "Sunday" ),
	tra( "Monday" ),
	tra( "Tuesday" ),
	tra( "Wednesday" ),
	tra( "Thursday" ),
	tra( "Friday" ),
	tra( "Saturday" )
);

$weeks = array();
$cell = array();

if ($_SESSION['calendarContentGuids']) {
	$listHash = array(
		'content_type_guid' => $_SESSION['calendarContentGuids'],
		'user_id' => !empty( $_REQUEST['user_id'] ) ?  $_REQUEST['user_id'] : NULL,
		'start' => $viewstart,
		'stop' => $viewend,
		'sort_mode' => !empty( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : 'last_modified_asc',
		'offset' => 0,
		'max_records' => 500,
	);
	$listbitevents = $Calendar->getList( $listHash );
} else {
	$listbitevents = array();
}

// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
for( $i = 0; $i <= $numberofweeks; $i++ ) {
	$weeks[] = $firstweek + $i;

	// $startOfWeek is a unix timestamp
	$startOfWeek = $viewstart + $i * 604800; // one week

	foreach( $weekdays as $w ) {
		$leday = array();
	    if( $_SESSION['calendar_view_mode'] == 'day' ) {
			$dday = $startOfWeek;
		} else {
			$dday = $startOfWeek + $d * $w;
		}

		$cell[$i][$w]['day'] = $dday;

		if( isset( $listbitevents[$dday] ) ) {
			$event = 0;
			foreach ($listbitevents["$dday"] as $bitevent) {
				$leday["{$bitevent['last_modified']}$event"] = $bitevent;
				$gBitSmarty->assign( 'cellHash', $bitevent );
				$leday["{$bitevent['last_modified']}$event"]["over"] = $gBitSmarty->fetch("bitpackage:calendar/calendar_box.tpl");
				$event++;
			}
		}

		if (is_array($leday)) {
			ksort ($leday);
			$cell[$i][$w]['items'] = array_values($leday);
		}
	}
}

$hrows = array();
if ($_SESSION['calendar_view_mode'] == 'day') {
	foreach( $cell[0]["{$weekdays[0]}"]['items'] as $dayitems ) {
		$hrows[intval(date( 'h', $dayitems['last_modified'] ))][] = $dayitems;
	}
}
$hours = array(  '0:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00',
				 '8:00', '9:00','10:00','11:00','12:00','13:00','14:00','15:00',
				'16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00');

$gBitSmarty->assign('hrows', $hrows); 
$gBitSmarty->assign('hours', $hours); 
$gBitSmarty->assign('trunc', 12); // put in a pref, number of chars displayed in cal cells
$gBitSmarty->assign('daformat', $gBitSystem->get_long_date_format()." ".tra("at")." %H:%M"); 
$gBitSmarty->assign('daformat2', $gBitSystem->get_long_date_format()); 
$gBitSmarty->assign('currentweek', $currentweek);
$gBitSmarty->assign('firstweek', $firstweek);
$gBitSmarty->assign('lastweek', $lastweek);
$gBitSmarty->assign('weekdays', $weekdays);
$gBitSmarty->assign('weeks', $weeks);
$gBitSmarty->assign('daysnames', $daysnames);
$gBitSmarty->assign('cell', $cell);
$gBitSmarty->assign('var', '');

$gBitSystem->display( 'bitpackage:calendar/calendar.tpl');
?>
