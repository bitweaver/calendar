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
$_template->tpl_vars['mini_cal'] = new Smarty_variable(data_calendar( '', empty($moduleParams['module_params']) ) );
?>
