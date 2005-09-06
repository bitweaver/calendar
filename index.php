<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.35 2005/09/06 08:43:58 lsces Exp $

// Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'Calendar.php' );

$gBitSystem->isPackageActive( 'calendar', TRUE );

$gBitSystem->verifyPermission( 'bit_p_view_calendar' );

// set up $_SESSION and $_REQUEST to make the usable later on
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

// now, lets get the ball rolling!
$gCalendar = new Calendar();

// set up the todate
if( !empty( $_REQUEST["todate"] ) ) {
	// clean up todate. who knows where this has come from
	if ( is_numeric( $_REQUEST['todate'] ) ) {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $gCalendar->mDate->gmmktime( 0, 0, 0, $gCalendar->mDate->date( 'm', $_REQUEST['todate'] ), $gCalendar->mDate->date( 'd', $_REQUEST['todate'] ), $gCalendar->mDate->date( 'Y', $_REQUEST['todate'] ) );
	} else {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $gCalendar->mDate->gmmktime( 0, 0, 0, $gCalendar->mDate->date2( 'm', $_REQUEST['todate'] ), $gCalendar->mDate->date2( 'd', $_REQUEST['todate'] ), $gCalendar->mDate->date2( 'Y', $_REQUEST['todate'] ) );
	}
} elseif( !empty( $_SESSION['calendar']['focus_date'] ) ) {
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
} else {
	$_SESSION['calendar']['focus_date'] = $gCalendar->mDate->gmmktime( 0, 0, 0, $gCalendar->mDate->date( 'm' ), $gCalendar->mDate->date( 'd' ), $gCalendar->mDate->date( 'Y' ) );
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
}

$focus = $_REQUEST['todate'];

if( !empty( $_REQUEST["view_mode"] ) ) {
	$_SESSION['calendar']['view_mode'] = $_REQUEST["view_mode"];
} elseif( empty( $_SESSION['calendar']['view_mode'] ) ) {
	$_SESSION['calendar']['view_mode'] = 'month';
}

$gBitSmarty->assign( 'navigation', $gCalendar->buildCalendarNavigation( $_SESSION['calendar'] ) );
$gBitSmarty->assign_by_ref( 'calMonth', $calMonth = $gCalendar->buildCalendar( $_SESSION['calendar'] ) );
$gBitSmarty->assign_by_ref( 'calDay', $calDay = $gCalendar->buildDay( $_SESSION['calendar'] ) );

if( $_SESSION['calendar']['content_type_guid'] ) {
	$listHash = $_SESSION['calendar'];
	$listHash['user_id'] = !empty( $_REQUEST['user_id'] ) ?	$_REQUEST['user_id'] : NULL;
	$listHash['sort_mode'] = !empty( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : 'last_modified_asc';
	$listHash['offset'] = 0;
	$listHash['max_records'] = 500;
	$bitEvents = $gCalendar->getList( $listHash );
} else {
	$bitEvents = array();
}

// finally we have all the stuff ready to populate the $calMonth and $calDay arrays
foreach( $calMonth as $w => $week ) {
	foreach( $week as $d => $day ) {
		$dayEvents = array();
		if( !empty( $bitEvents[$day['day']] ) ) {
			$i = 0;
			foreach( $bitEvents[$day['day']] as $bitEvent ) {
				$dayEvents[$i] = $bitEvent;
				$gBitSmarty->assign( 'cellHash', $bitEvent );
				$dayEvents[$i]["over"] = $gBitSmarty->fetch( "bitpackage:calendar/calendar_box.tpl" );

				// populate $calDay array with events
				if( !empty ( $bitEvent ) && $_SESSION['calendar']['view_mode'] == 'day' ) {
					foreach( $calDay as $key => $t ) {
						// special case - last item entry in array - check this first
						if( $bitEvent['timestamp'] >= $calDay[$key]['time'] && empty( $calDay[$key + 1]['time'] ) ) {
							$calDay[$key]['items'][] = $dayEvents[$i];
						} elseif( $bitEvent['timestamp'] >= $calDay[$key]['time'] && $bitEvent['timestamp'] <= $calDay[$key + 1]['time'] ) {
							$calDay[$key]['items'][] = $dayEvents[$i];
						}
					}
				}

				$i++;
			}
		}
		if( !empty( $dayEvents ) ) {
			$calMonth[$w][$d]['items'] = array_values( $dayEvents );
		}
	}
}
// set up daynames for the calendar
$dayNames = array(
	tra( "Monday" ),
	tra( "Tuesday" ),
	tra( "Wednesday" ),
	tra( "Thursday" ),
	tra( "Friday" ),
	tra( "Saturday" ),
	tra( "Sunday" ),
);

// depending on what day we want to view first, we need to adjust the dayNames array
for( $i = 0; $i < WEEK_OFFSET; $i++ ) {
	$pop = array_pop( $dayNames );
	array_unshift( $dayNames, $pop );
}
$gBitSmarty->assign( 'dayNames', $dayNames );

// TODO: make this a pref
$gBitSmarty->assign( 'trunc', $gBitSystem->getPreference( 'title_truncate', 12 ) );

$gBitSystem->display( 'bitpackage:calendar/calendar.tpl' );
?>
