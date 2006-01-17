<?php
global $gBitSystem, $gBitSmarty;
$gBitSystem->registerPackage( 'calendar', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'calendar' ) ) {
	if( $gBitUser->hasPermission( 'bit_p_view_calendar' ) ) {
		$gBitSystem->registerAppMenu( CALENDAR_PKG_NAME, ucfirst( CALENDAR_PKG_DIR ), CALENDAR_PKG_URL.'index.php', 'bitpackage:calendar/menu_calendar.tpl', CALENDAR_PKG_NAME );
	}
}

?>
