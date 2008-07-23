<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/modules/mod_minical.php,v 1.5 2008/07/23 12:47:17 wjames5 Exp $
 * @package calendar
 * @subpackage modules
 */

/**
 * Required setup
 */
global $gBitSmarty;

// Make sure we know how to do the data_calendar
require_once(CALENDAR_PKG_PATH.'liberty_plugins/data.calendar.php');
$gBitSmarty->assign( 'mini_cal',data_calendar( '', empty($moduleParams['module_params']) ? NULL : $moduleParams['module_params'] ) );
?>
