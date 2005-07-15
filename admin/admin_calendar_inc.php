<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/admin/admin_calendar_inc.php,v 1.2 2005/07/15 16:04:07 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (isset($_REQUEST["calendarset"]) && isset($_REQUEST["homeCalendar"])) {
	
	$gBitSystem->storePreference("home_calendar", $_REQUEST["homeCalendar"]);
	$smarty->assign('home_calendar', $_REQUEST["homeCalendar"]);
}

include_once( CALENDAR_PKG_PATH.'CalendarLib.php' );

$formCalendarFeatures = array(
	"calendar_blogs" => array(
		'label' => 'Display blog updates',
	),
	"calendar_users" => array(
		'label' => 'Display user personal page updates',
	),
	"calendar_wiki" => array(
		'label' => 'Display wiki page updates',
	),
	"calendar_contact" => array(
		'label' => 'Display contact record updates',
	),
	"feature_jscalendar" => array(
		'label' => 'Enable jscalendar popup',
	),
);
$smarty->assign( 'formCalendarFeatures',$formCalendarFeatures );

if (isset($_REQUEST["calendarfeatures"])) {
	
	foreach( $formCalendarFeatures as $item => $data ) {
		simple_set_toggle( $item );
	}

	if ($gBitSystem->isPackageActive( 'categories' )) {
		if (isset($_REQUEST["cal_categ"]) && $_REQUEST["cal_categ"] == "on") {
			$gBitSystem->storePreference("cal_categ", 'y');
			$smarty->assign("cal_categ", 'y');
		} else {
			$gBitSystem->storePreference("blog_categ", 'n');
			$smarty->assign("blog_categ", 'n');
		}
		$gBitSystem->storePreference("cal_parent_categ", $_REQUEST["cal_parent_categ"]);
		$smarty->assign('cal_parent_categ', $_REQUEST['cal_parent_categ']);
	}
}

?>
