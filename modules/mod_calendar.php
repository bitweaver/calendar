<?php
include_once( CALENDAR_PKG_PATH.'Calendar.php' );
$cal = new Calendar();

// set up the todate
if( !empty( $_REQUEST["todate"] ) ) {
	// clean up todate. who knows where this has come from
	$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = mktime( 0, 0, 0, date( 'm', $_REQUEST['todate'] ), date( 'd', $_REQUEST['todate'] ), date( 'Y', $_REQUEST['todate'] ) );
} elseif( !empty( $_SESSION['calendar']['focus_date'] ) ) {
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
} else {
	$_SESSION['calendar']['focus_date'] = mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) );
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
}

$calHash = array(
	'focus_date' => $_SESSION['calendar']['focus_date'],
	'view_mode' => 'month',
);

// set up daynames for the calendar
$dayNames = array(
	tra( "Mon" ),
	tra( "Tue" ),
	tra( "Wed" ),
	tra( "Thu" ),
	tra( "Fri" ),
	tra( "Sat" ),
	tra( "Sun" ),
);

// depending on what day we want to view first, we need to adjust the dayNames array
$week_offset = $gBitSystem->getPreference( 'week_offset', 1 );
if( !empty( $week_offset ) ) {
	for( $i = 0; $i < $week_offset; $i++ ) {
		$pop = array_pop( $dayNames );
		array_unshift( $dayNames, $pop );
	}
}
$gBitSmarty->assign( 'dayNames', $dayNames );

$gBitSmarty->assign( 'modCalNavigation', $cal->buildCalendarNavigation( $calHash ) );
$gBitSmarty->assign( 'modCalMonth', $calMonth = $cal->buildCalendar( $calHash ) );
?>
