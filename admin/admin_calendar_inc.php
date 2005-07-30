<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/admin/admin_calendar_inc.php,v 1.4 2005/07/30 12:00:32 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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
$gBitSmarty->assign( 'formCalendarFeatures',$formCalendarFeatures );

if (isset($_REQUEST["calendarfeatures"])) {
	
	foreach( $formCalendarFeatures as $item => $data ) {
		simple_set_toggle( $item );
	}
}

?>
