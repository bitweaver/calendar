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
		$menuHash = array(
			'package_name'  => CALENDAR_PKG_NAME,
			'index_url'     => CALENDAR_PKG_URL.'index.php',
			'menu_template' => 'bitpackage:calendar/menu_calendar.tpl',
		);
		$gBitSystem->registerAppMenu( $menuHash );
	}
}

?>
