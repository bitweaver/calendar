<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/modules/mod_calendar.php,v 1.6 2005/12/18 08:50:01 lsces Exp $
 * @package calendar
 * @subpackage modules
 */

/**
 * Required setup
 */
include_once( CALENDAR_PKG_PATH.'Calendar.php' );
$cal = new Calendar();

// set up the todate
if( !empty( $_REQUEST['todate'] ) ) {
	// clean up todate. who knows where this has come from
	if ( is_numeric( $_REQUEST['todate'] ) ) {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $cal->mDate->gmmktime( 0, 0, 0, $cal->mDate->date( 'm', $_REQUEST['todate'] ), $cal->mDate->date( 'd', $_REQUEST['todate'] ), $cal->mDate->date( 'Y', $_REQUEST['todate'] ) );
	} else {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $cal->mDate->gmmktime( 0, 0, 0, $cal->mDate->date2( 'm', $_REQUEST['todate'] ), $cal->mDate->date2( 'd', $_REQUEST['todate'] ), $cal->mDate->date2( 'Y', $_REQUEST['todate'] ) );
	}
} elseif( !empty( $_SESSION['calendar']['focus_date'] ) ) {
	$_REQUEST["todate"] = $_SESSION['calendar']['focus_date'];
} else {
	$_SESSION['calendar']['focus_date'] = $cal->mDate->gmmktime( 0, 0, 0, $cal->mDate->date( 'm' ), $cal->mDate->date( 'd' ), $cal->mDate->date( 'Y' ) );
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
