<?php
include_once( CALENDAR_PKG_PATH.'Calendar.php' );
$cal = new Calendar();

// set up the todate
if( !empty( $_REQUEST["todate"] ) ) {
	// clean up todate. who knows where this has come from
	$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = adodb_mktime( 0, 0, 0, adodb_date( 'm', $_REQUEST['todate'] ), adodb_date( 'd', $_REQUEST['todate'] ), adodb_date( 'Y', $_REQUEST['todate'] ) );
} elseif( !empty( $_SESSION['calendar']['focus_date'] ) ) {
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
} else {
	$_SESSION['calendar']['focus_date'] = adodb_mktime( 0, 0, 0, adodb_date( 'm' ), adodb_date( 'd' ), adodb_date( 'Y' ) );
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
}

$calHash = array(
	'focus_date' => $_SESSION['calendar']['focus_date'],
	'view_mode' => 'month',
);

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

$gBitSmarty->assign( 'modCalNavigation', $cal->buildCalendarNavigation( $calHash ) );
$gBitSmarty->assign( 'modCalMonth', $calMonth = $cal->buildCalendar( $calHash ) );
?>
