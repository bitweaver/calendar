<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.15 2005/08/19 11:54:23 squareing Exp $

// Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'Calendar.php' );

$gBitSystem->isPackageActive( 'calendar', TRUE );

$gBitSystem->verifyPermission( 'bit_p_view_calendar' );

$Calendar = new Calendar();

// setup list of bit items displayed
if( !empty( $_REQUEST["content_type_guid"] ) ) {
	$_SESSION['calendar']['content_type_guid'] = $_REQUEST["content_type_guid"];
} elseif( !isset( $_SESSION['calendar']['content_type_guid'] ) ) {
	$_SESSION['calendar']['content_type_guid'] = array();
} elseif( isset( $_REQUEST["refresh"] )and !isset( $_REQUEST["content_type_guid"] ) ) {
	$_SESSION['calendar']['content_type_guid'] = array();
}

foreach( $gLibertySystem->mContentTypes as $cType ) {
	$contentTypes[$cType['content_type_guid']] = $cType['content_description'];
}
$gBitSmarty->assign( 'contentTypes', $contentTypes );

if( !empty( $_REQUEST["todate"] ) ) {
	$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'];
} elseif( !empty( $_SESSION['calendar']['focus_date'] ) ) {
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
} else {
	$_SESSION['calendar']['focus_date'] = mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) );
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
}

$focusdate = $_REQUEST['todate'];
list( $focus_day, $focus_month, $focus_year ) = array(
	date( "d", $focusdate ),
	date( "m", $focusdate ),
	date( "Y", $focusdate )
);
$focusdate = mktime( 0, 0, 0, $focus_month, $focus_day, $focus_year );

if( !empty( $_REQUEST["view_mode"] ) ) {
	$_SESSION['calendar']['view_mode'] = $_REQUEST["view_mode"];
} elseif( empty( $_SESSION['calendar']['view_mode'] ) ) {
	$_SESSION['calendar']['view_mode'] = 'month';
}

$z = date( "z" );

$gBitSmarty->assign( 'daybefore',	mktime( 0, 0, 0, $focus_month, $focus_day - 1, $focus_year ) );
$gBitSmarty->assign( 'weekbefore',	mktime( 0, 0, 0, $focus_month, $focus_day - 7, $focus_year ) );
$gBitSmarty->assign( 'monthbefore',	mktime( 0, 0, 0, $focus_month - 1, $focus_day, $focus_year ) );
$gBitSmarty->assign( 'yearbefore',	mktime( 0, 0, 0, $focus_month, $focus_day, $focus_year - 1 ) );
$gBitSmarty->assign( 'dayafter',	mktime( 0, 0, 0, $focus_month, $focus_day + 1, $focus_year ) );
$gBitSmarty->assign( 'weekafter',	mktime( 0, 0, 0, $focus_month, $focus_day + 7, $focus_year ) );
$gBitSmarty->assign( 'monthafter',	mktime( 0, 0, 0, $focus_month + 1, $focus_day, $focus_year ) );
$gBitSmarty->assign( 'yearafter',	mktime( 0, 0, 0, $focus_month, $focus_day, $focus_year + 1 ) );
$gBitSmarty->assign( 'focusmonth',	$focus_month );
$gBitSmarty->assign( 'focusdate',	$focusdate );
$gBitSmarty->assign( 'now',			mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) );

$weekdays = range( 0, 6 );

// calculate timespan for sql query
if( $_SESSION['calendar']['view_mode'] == 'month' ) {
	$viewstart = mktime( 0, 0, 0, $focus_month    , 1, $focus_year );
	$viewend   = mktime( 0, 0, 0, $focus_month + 1, 0, $focus_year );
	// move viewstart back to Sunday....
	$viewstart -= date( "w", $viewstart ) * 86400;
	$viewend +=( 6 - date( "w", $viewend ) ) * 86400 - 1;

	// ISO weeks --- kinda mangled because ours begin on Sunday...
	$firstweek = date( "W", $viewstart + 86400 );
	$lastweek  = date( "W", $viewend );
	if( $lastweek < $firstweek ) {
		if( date( "W", $focusdate ) < $firstweek ) {
			$firstweek -= 52;
		} else {
			$lastweek += 52;
		}
	}
	$numberofweeks = $lastweek - $firstweek;
} elseif( $_SESSION['calendar']['view_mode'] == 'week' ) {
	$viewstart = mktime( 0, 0, 0, $focus_month, $focus_day, $focus_year );
	$numberofweeks = 0;
} else {
	$viewstart = mktime( 0, 0, 0, $focus_month, $focus_day, $focus_year );
	$numberofweeks = 0;
}

$weeks = array();
$cell = array();

if( $_SESSION['calendar']['content_type_guid'] ) {
	$listHash = $_SESSION['calendar'];
	$listHash['user_id'] = !empty( $_REQUEST['user_id'] ) ?	$_REQUEST['user_id'] : NULL;
	$listHash['sort_mode'] = !empty( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : 'last_modified_asc';
	$listHash['offset'] = 0;
	$listHash['max_records'] = 500;
	$bitEvents = $Calendar->getList( $listHash );
} else {
	$bitEvents = array();
}

// note that number of weeks starts at ZERO( i.e., zero = 1 week to display ).
for( $i = 0; $i <= $numberofweeks; $i++ ) {
	$weeks[] = $firstweek + $i;

	// $start_of_week is a unix timestamp
	$start_of_week = $viewstart + $i * 604800; // one week

	foreach( $weekdays as $wd ) {
		$leday = array();
		if( $_SESSION['calendar']['view_mode'] == 'day' ) {
			$dday = $start_of_week;
		} else {
			$dday = $start_of_week + 86400 * $wd;
		}

		$cell[$i][$wd]['day'] = $dday;

		if( isset( $bitEvents[$dday] ) ) {
			$event = 0;
			foreach( $bitEvents[$dday] as $bitEvent ) {
				$leday["{$bitEvent['last_modified']}$event"] = $bitEvent;
				$gBitSmarty->assign( 'cellHash', $bitEvent );
				$leday["{$bitEvent['last_modified']}$event"]["over"] = $gBitSmarty->fetch( "bitpackage:calendar/calendar_box.tpl" );
				$event++;
			}
		}

		if( is_array( $leday ) ) {
			ksort( $leday );
			$cell[$i][$wd]['items'] = array_values( $leday );
		}
	}
}

$hrows = array();
if( $_SESSION['calendar']['view_mode'] == 'day' ) {
	foreach( $cell[0]["{$weekdays[0]}"]['items'] as $dayitems ) {
		$hrows[intval( date( 'h', $dayitems['last_modified'] ) )][] = $dayitems;
	}
}
$hours = array(	'0:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00',
				 '8:00', '9:00','10:00','11:00','12:00','13:00','14:00','15:00',
				'16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00' );

$daysnames = array(
	tra( "Sunday" ),
	tra( "Monday" ),
	tra( "Tuesday" ),
	tra( "Wednesday" ),
	tra( "Thursday" ),
	tra( "Friday" ),
	tra( "Saturday" )
);
$gBitSmarty->assign( 'daysnames', $daysnames );

$gBitSmarty->assign( 'hrows', $hrows ); 
$gBitSmarty->assign( 'hours', $hours ); 
$gBitSmarty->assign( 'trunc', 12 ); // put in a pref, number of chars displayed in cal cells
$gBitSmarty->assign( 'daformat', $gBitSystem->get_long_date_format()." ".tra( "at" )." %H:%M" ); 
$gBitSmarty->assign( 'daformat2', $gBitSystem->get_long_date_format() ); 
$gBitSmarty->assign( 'firstweek', $firstweek );
$gBitSmarty->assign( 'lastweek', $lastweek );
$gBitSmarty->assign( 'weekdays', $weekdays );
$gBitSmarty->assign( 'weeks', $weeks );
$gBitSmarty->assign( 'cell', $cell );
$gBitSmarty->assign( 'var', '' );

$gBitSystem->display( 'bitpackage:calendar/calendar.tpl' );
?>
