<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/index.php,v 1.55 2007/09/30 15:47:40 nickpalmer Exp $

// Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( CALENDAR_PKG_PATH.'Calendar.php' );

$gBitSystem->isPackageActive( 'calendar', TRUE );

$gBitSystem->verifyPermission( 'p_calendar_view' );

// now, lets get the ball rolling!
$gCalendar = new Calendar();

// Handle the request hash storing into the session.
$gCalendar->processRequestHash($_REQUEST, $_SESSION['calendar']);

// Setup which content types we want to view.
if( $gBitUser->hasPermission("p_calendar_view_changes") && $_SESSION['calendar']['content_type_guid'] ) {
	$listHash = $_SESSION['calendar'];
} else {
	foreach ($gLibertySystem->mContentTypes as $key => $val) {
		if ($gBitSystem->isFeatureActive('calendar_default_'.$key)) {
			$listHash['content_type_guid'][] = $key;
		}
	}
}
// Build the calendar
$gCalendar->buildCalendar($listHash, $_SESSION['calendar']);
// And display it with a nice title.
$gCalendar->display(tra('Calendar'));

?>
