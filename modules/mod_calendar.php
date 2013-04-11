<?php
/**
 * @version $Header$
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
/*	if ( is_numeric( $_REQUEST['todate'] ) ) {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $cal->mDate->gmmktime( 0, 0, 0, $cal->mDate->date( 'm', $_REQUEST['todate'] ), $cal->mDate->date( 'd', $_REQUEST['todate'] ), $cal->mDate->date( 'Y', $_REQUEST['todate'] ) );
	} else {
		$_SESSION['calendar']['focus_date'] = $_REQUEST['todate'] = $cal->mDate->gmmktime( 0, 0, 0, $cal->mDate->date2( 'm', $_REQUEST['todate'] ), $cal->mDate->date2( 'd', $_REQUEST['todate'] ), $cal->mDate->date2( 'Y', $_REQUEST['todate'] ) );
	} */
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

$cal->setupCalendar(FALSE);

$_template->tpl_vars['modCalNavigation'] = new Smarty_variable( $cal->buildCalendarNavigation( $calHash );
$_template->tpl_vars['modCalMonth'] = new Smarty_variable( $calMonth = $cal->buildMonth( $calHash );
?>
