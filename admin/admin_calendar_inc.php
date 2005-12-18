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
	'calendar_week_offset',
	'calendar_hour_fraction',
	'calendar_day_start',
	'calendar_day_end',
	'calendar_user_prefs',
);

// this function only exists if it's been included by the index.php page. if
// it's been included from anywhere else, we don't execute this section
if( function_exists( 'simple_set_value' ) && $gBitUser->isAdmin() && !empty( $_REQUEST['calendar_submit'] ) ) {
	foreach( $calendarValues as $item ) {
		if ( $item != 'calendar_user_prefs' )
			simple_set_value( $item, CALENDAR_PKG_NAME );
		else
			simple_set_toggle( $item, CALENDAR_PKG_NAME );
	}
}
?>
