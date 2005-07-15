<?php

$tables = array(

'tiki_calendar_categories' => "
  cal_cat_id I4 AUTO PRIMARY,
  calendar_id I4 NOTNULL,
  name C(160) NOTNULL
",

'tiki_calendar_items' => "
  calitem_id I4 AUTO PRIMARY,
  calendar_id I4 NOTNULL,
  start_time I8 NOTNULL,
  end_time I8 NOTNULL,
  location_id I4,
  category_id I4,
  priority C(1) NOTNULL DEFAULT '1',
  status C(1) NOTNULL DEFAULT '0',
  url C(255),
  lang C(2) NOTNULL default 'en',
  name C(255) NOTNULL,
  description B,
  user_id I4,
  created I8 NOTNULL,
  lastmodif I8 NOTNULL
",

'tiki_calendar_locations' => "
  calloc_id I4 AUTO PRIMARY,
  calendar_id I4 NOTNULL,
  name C(160) NOTNULL,
  description X
",

'tiki_calendar_roles' => "
  calitem_id I4 NOTNULL PRIMARY,
  user_id I4 NOTNULL PRIMARY,
  role C(1) NOTNULL DEFAULT '0'
",

'tiki_calendars' => "
  calendar_id I4 AUTO PRIMARY,
  name C(80) NOTNULL,
  description C(255),
  user_id I4 NOTNULL,
  customlocations C(1) NOTNULL DEFAULT 'n',
  customcategories C(1) NOTNULL DEFAULT 'n',
  customlanguages C(1) NOTNULL DEFAULT 'n',
  custompriorities C(1) NOTNULL DEFAULT 'n',
  customparticipants C(1) NOTNULL DEFAULT 'n',
  created I8 NOTNULL,
  lastmodif I8 NOTNULL
"

);

global $gBitInstaller;

$gBitInstaller->makePackageHomeable('calendar');

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CALENDAR_PKG_DIR, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CALENDAR_PKG_DIR, array(
	'description' => "Calendar package to display tiki entries by date, and set events",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'beta',
	'dependencies' => '',
) );

// ### Indexes
$indices = array (
	'tiki_calendar_categories_idx' => array( 'table' => 'tiki_calendar_categories', 'cols' => 'calendar_id,name', 'opts' => NULL ),
	'tiki_calendar_items_idx' => array( 'table' => 'tiki_calendar_items', 'cols' => 'calendar_id', 'opts' => NULL ),
	'tiki_calendar_locations_idx' => array( 'table' => 'tiki_calendar_locations', 'cols' => 'name', 'opts' => NULL )
);

$gBitInstaller->registerSchemaIndexes( CALENDAR_PKG_DIR, $indices );

// ### Defaults

// ### Default Menu Options
$gBitInstaller->registerMenuOptions( CALENDAR_PKG_DIR, array(
	array(42,'o','Calendar','tiki-calendar.php',0,'feature_calendar','bit_p_view_calendar',''),
	array(42,'o','Calendar','tiki-admin_calendars.php',1,'feature_calendar','bit_p_admin_calendar','')
) );

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CALENDAR_PKG_DIR, array(
	array('bit_p_view_calendar', 'Can browse the calendar', 'basic', 'calendar'),
	array('bit_p_change_events', 'Can change events in the calendar', 'registered', 'calendar'),
	array('bit_p_add_events', 'Can add events in the calendar', 'registered', 'calendar'),
	array('bit_p_admin_calendar', 'Can create/admin calendars', 'admin', 'calendar')
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( CALENDAR_PKG_DIR, array(
	array ('kernel', 'feature_jscalendar', 'n'),
	array ('calendar', 'calendar_blogs', 'y'),
	array ('calendar', 'calendar_users', 'y'),
	array ('calendar', 'calendar_wiki', 'y')
) );

?>
