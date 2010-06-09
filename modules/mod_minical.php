<?php
/**
 * @version $Header$
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
