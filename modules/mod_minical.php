<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/modules/mod_minical.php,v 1.4 2008/02/10 12:18:52 nickpalmer Exp $
 * @package calendar
 * @subpackage modules
 */

/**
 * Required setup
 */
global $gBitSmarty;

// Make sure we know how to do the data_calendar
require_once(LIBERTY_PKG_PATH.'plugins/data.calendar.php');
$gBitSmarty->assign( 'mini_cal',data_calendar( '', empty($moduleParams['module_params']) ? NULL : $moduleParams['module_params'] ) );
?>