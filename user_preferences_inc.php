<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/user_preferences_inc.php,v 1.3 2007/06/22 09:06:40 lsces Exp $
 * @package calendar
 * @subpackage functions
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 * Setup
 */
if( $gBitSystem->isFeatureActive('calendar_user_prefs') ) {
	include_once( CALENDAR_PKG_PATH.'admin/admin_calendar_inc.php' );
	if( !empty( $_REQUEST['calendar_submit'] ) ) {
		foreach( $calendarValues as $item ) {
			if( !empty( $_REQUEST[$item] ) ) {
				$editUser->storePreference( $item, $_REQUEST[$item], 'calendar' );
			}
		}
	}
}
?>