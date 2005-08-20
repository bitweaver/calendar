<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.17 2005/08/20 23:46:32 squareing Exp $

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

$calendar = $Calendar->buildCalendar( $_SESSION['calendar'] );

// TODO: make start and end time of day view configurable
if( $_SESSION['calendar']['view_mode'] == 'day' ) {
	// calculations in preparation of custom dayview range setting - all that needs to be done is adjust start and stop times accordingly
	$start_time = $focusdate;
	$stop_time  = mktime( 0, 0, 0, $focus_month, $focus_day + 1, $focus_year );
	$hours_count = ( $stop_time - $start_time ) / ( 60 * 60 );

	// allow for custom time intervals
	$hour_fraction = $gBitSystem->getPreference( 'calendar_hour_fraction', 1 );
	$row_count = $hours_count * $hour_fraction;
	$hour = strftime( '%H', $start ) - 1;
	$mins = 0;
	for( $i = 0; $i < $row_count; $i++ ) {
		if( !( $i % $hour_fraction ) ) {
			// set vars
			$hour++;
			$mins = 0;
		}
		$dayTime[$i]['time'] = mktime( $hour, $mins, 0, $focus_month, $focus_day, $focus_year );
		$mins += 60 / $hour_fraction;
	}

	// calendar data is added below
	$gBitSmarty->assign_by_ref( 'dayTime', $dayTime ); 
}

foreach( $calendar as $w => $week ) {
	foreach( $week as $d => $day ) {
		$dayEvents = array();
		if( !empty( $bitEvents[$day['day']] ) ) {
			$i = 0;
			foreach( $bitEvents[$day['day']] as $bitEvent ) {
				$dayEvents[$i] = $bitEvent;
				$gBitSmarty->assign( 'cellHash', $bitEvent );
				$dayEvents[$i]["over"] = $gBitSmarty->fetch( "bitpackage:calendar/calendar_box.tpl" );

				// populate $dayTime array with events
				if( !empty ( $bitEvent ) ) {
					foreach( $dayTime as $key => $t ) {
						// special case - last time entry in array - check this first
						if( $bitEvent['last_modified'] >= $dayTime[$key]['time'] && empty( $dayTime[$key + 1]['time'] ) ) {
							$dayTime[$key]['items'][] = $dayEvents[$i];
						} elseif( $bitEvent['last_modified'] >= $dayTime[$key]['time'] && $bitEvent['last_modified'] <= $dayTime[$key + 1]['time'] ) {
							$dayTime[$key]['items'][] = $dayEvents[$i];
						}
					}
				}

				$i++;
			}
		}

		if( !empty( $dayEvents ) ) {
			$calendar[$w][$d]['items'] = array_values( $dayEvents );
		}
	}
}
$gBitSmarty->assign( 'calendar', $calendar );

/* old method - was buggy * /
$calDates = $Calendar->doDateCalculations( $_SESSION['calendar'] );

$weekdays = range( 0, 6 );
$weeks = array();
$cell = array();

// note that number of weeks starts at ZERO( i.e., zero = 1 week to display ).
for( $i = 0; $i <= $calDates['number_of_weeks']; $i++ ) {
	$weeks[] = $calDates['first_week'] + $i;

	// $start_of_week is a unix timestamp
	$start_of_week = $calDates['view_start'] + $i * 604800; // one week

	foreach( $weekdays as $wd ) {
		$leday = array();
		if( $_SESSION['calendar']['view_mode'] == 'day' ) {
			$dday = $start_of_week;
		} else {
			$dday = $start_of_week + 86400 * $wd;
		}

		$cell[$i][$wd]['day'] = $dday;
		//$cell[$i][$wd]['day'] = mktime();

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
//vd($cell);
/**/

$daysnames = array(
	tra( "Sunday" ),
	tra( "Monday" ),
	tra( "Tuesday" ),
	tra( "Wednesday" ),
	tra( "Thursday" ),
	tra( "Friday" ),
	tra( "Saturday" ),
);
$gBitSmarty->assign( 'daysnames', $daysnames );

// calendar navigation
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

$gBitSmarty->assign( 'trunc', 12 ); // put in a pref, number of chars displayed in cal cells

$gBitSystem->display( 'bitpackage:calendar/calendar.tpl' );
?>
