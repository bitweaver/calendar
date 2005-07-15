<?php
global $gBitSystem, $smarty;
$gBitSystem->registerPackage( 'calendar', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'calendar' ) ) {
	$gBitSystem->registerAppMenu( 'calendar', 'Calendar', CALENDAR_PKG_URL.'index.php', 'bitpackage:calendar/menu_calendar.tpl', 'calendar');
}

?>
