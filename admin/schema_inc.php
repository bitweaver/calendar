<?php
global $gBitInstaller;

$gBitInstaller->registerPackageInfo( CALENDAR_PKG_NAME, array(
	'description' => "Calendar package to display bitweaver entries by date, and set events",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( KERNEL_PKG_NAME, array(
	array(CALENDAR_PKG_NAME,'calendar_week_offset','7'),
	array(CALENDAR_PKG_NAME,'calendar_hour_fraction','1'),
	array(CALENDAR_PKG_NAME,'calendar_user_prefs','y'),
	array(CALENDAR_PKG_NAME,'calendar_day_start','0'),
	array(CALENDAR_PKG_NAME,'calendar_day_end','24'),
) );

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CALENDAR_PKG_NAME, array(
	array('p_calendar_view', 'Can browse the calendar', 'basic', CALENDAR_PKG_NAME),
	array('p_calendar_view_changes', 'Can browse content changes in the calendar', 'editors', CALENDAR_PKG_NAME),
) );

// this empty table registration is needed for the installer to pick it up to install the preferences
$gBitInstaller->registerSchemaTable( CALENDAR_PKG_NAME, '', '' );

// Requirements
$gBitInstaller->registerRequirements( CALENDAR_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.4' ),
));
