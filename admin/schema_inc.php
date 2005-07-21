<?php
global $gBitInstaller;

$gBitInstaller->makePackageHomeable('calendar');

$gBitInstaller->registerPackageInfo( CALENDAR_PKG_NAME, array(
	'description' => "Calendar package to display bitweaver entries by date, and set events",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Defaults

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CALENDAR_PKG_NAME, array(
	array('bit_p_view_calendar', 'Can browse the calendar', 'basic', CALENDAR_PKG_NAME),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( CALENDAR_PKG_NAME, array(
	array (CALENDAR_PKG_NAME, 'calendar_blogs', 'y'),
	array (CALENDAR_PKG_NAME, 'calendar_users', 'y'),
	array (CALENDAR_PKG_NAME, 'calendar_wiki', 'y')
) );

?>
