<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/Attic/add_calendar.php,v 1.3 2005/07/15 17:48:59 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'CalendarLib.php' );

if ($bit_p_admin_calendar != 'y' and $bit_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["calendar_id"])) {
	$_REQUEST["calendar_id"] = 0;
}

$calendarlib = new CalendarLib();

if (isset($_REQUEST["drop"])) {
	
	$calendarlib->dropCalendar($_REQUEST["drop"]);
	$_REQUEST["calendar_id"] = 0;
}

if (isset($_REQUEST["save"])) {
	
	$customflags["customlanguages"] = $_REQUEST["customlanguages"];
	$customflags["customlocations"] = $_REQUEST["customlocations"];
	$customflags["customcategories"] = $_REQUEST["customcategories"];
	$customflags["custompriorities"] = $_REQUEST["custompriorities"];
	$_REQUEST["calendar_id"] = $calendarlib->setCalendar($_REQUEST["calendar_id"],$gBitUser->mUserId,$_REQUEST["name"],$_REQUEST["description"],$customflags);
}

if ($_REQUEST["calendar_id"]) {
	$info = $calendarlib->getCalendar($_REQUEST["calendar_id"]);
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab2");
} else {
	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["customlanguages"] = 'n';
	$info["customlocations"] = 'n';
	$info["customcategories"] = 'n';
	$info["custompriorities"] = 'n';
	$info["user_id"] = $gBitUser->mUserId;
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('user_id', $info["user_id"]);
$smarty->assign('customlanguages', $info["customlanguages"]);
$smarty->assign('customlocations', $info["customlocations"]);
$smarty->assign('customcategories', $info["customcategories"]);
$smarty->assign('custompriorities', $info["custompriorities"]);
$smarty->assign('calendar_id', $_REQUEST["calendar_id"]);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$pagination_url = $gBitSystem->pagination_url($find, $sort_mode);
$smarty->assign_by_ref('pagination_url', $pagination_url);

$calendars = $calendarlib->listCalendars(0, -1, $sort_mode, $find, 0);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST['page'])) {
	$page = &$_REQUEST['page'];
	$offset = ($page - 1) * $maxRecords;
}
$smarty->assign_by_ref('offset', $offset);

$cant_pages = ceil($calendars["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($calendars["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('calendars', $calendars["data"]);


$groups = $userlib->get_groups();

$cat_type = 'calendar';
$cat_objid = $_REQUEST["calendar_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );



// Display the template
$gBitSystem->display( 'bitpackage:calendar/add_calendars.tpl');

?>
