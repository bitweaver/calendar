<?php
global $gBitSmarty;

// Make sure we know how to do the data_calendar
require_once(LIBERTY_PKG_PATH.'plugins/data.calendar.php');
$gBitSmarty->assign( 'mini_cal',data_calendar( '', '' ) );
?>