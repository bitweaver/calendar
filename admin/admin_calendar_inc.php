<?php
$hourValues = array(  1,  2,  3,  4,  6, 12 );
$hourOutput = array( 60, 30, 20, 15, 10, 5  );
$gBitSmarty->assign( 'hourValues', $hourValues );
$gBitSmarty->assign( 'hourOutput', $hourOutput );

$firstDayValues = array( 7, 6, 5, 4, 3, 2, 1 );
$firstDayOutput = array( tra( "Monday" ), tra( "Tuesday" ), tra( "Wednesday" ), tra( "Thursday" ), tra( "Friday" ), tra( "Saturday" ), tra( "Sunday" ) );
$gBitSmarty->assign( 'firstDayValues', $firstDayValues );
$gBitSmarty->assign( 'firstDayOutput', $firstDayOutput );

$dayStart = range( 0, 12 );
$gBitSmarty->assign( 'dayStart', $dayStart );
$dayEnd = range( 24, 13 );
$gBitSmarty->assign( 'dayEnd', $dayEnd );

$calendarValues = array(
	'week_offset',
	'hour_fraction',
	'day_start',
	'day_end',
);

// this function only exists if it's been included by the index.php page. if
// it's been included from anywhere else, we don't execute this section
if( function_exists( 'simple_set_value' ) && $gBitUser->isAdmin() && !empty( $_REQUEST['calendar_submit'] ) ) {
	foreach( $calendarValues as $item ) {
		simple_set_value( $item, CALENDAR_PKG_NAME );
	}
}
?>
