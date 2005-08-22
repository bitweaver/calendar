<?php
global $gBitInstaller;

$gBitInstaller->makePackageHomeable( 'calendar' );

$gBitInstaller->registerPackageInfo( CALENDAR_PKG_NAME, array(
	'description' => "Calendar package to display bitweaver entries by date, and set events",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CALENDAR_PKG_NAME, array(
	array('bit_p_view_calendar', 'Can browse the calendar', 'basic', CALENDAR_PKG_NAME),
) );

// this empty table registration is needed for the installer to pick it up to install the preferences
$gBitInstaller->registerSchemaTable( CALENDAR_PKG_NAME, '', '' );
?>
