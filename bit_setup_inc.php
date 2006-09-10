<?php
global $gBitSystem, $gBitSmarty, $gBitUser;

$registerHash = array(
	'package_name' => 'calendar',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'calendar' ) ) {
	if( $gBitUser->hasPermission( 'p_calendar_view' ) ) {
		$gBitSystem->registerAppMenu( CALENDAR_PKG_NAME, ucfirst( CALENDAR_PKG_DIR ), CALENDAR_PKG_URL.'index.php', 'bitpackage:calendar/menu_calendar.tpl', CALENDAR_PKG_NAME );
	}
}

?>
