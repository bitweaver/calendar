<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/user_preferences_inc.php,v 1.2 2006/10/13 12:42:50 lsces Exp $
 * @package calendar
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