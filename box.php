<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/box.php,v 1.10 2010/04/17 22:46:07 wjames5 Exp $
 * @package calendar
 * @subpackage functions
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * Setup
 */
require_once( '../kernel/setup_inc.php' );

global $gContent, $gBitSystem;
include_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

$gContent->mInfo['rendered'] = $gContent->getPreview();

$gBitSmarty->assign('cellHash', $gContent->mInfo);

$gBitSmarty->display( "bitpackage:calendar/calendar_box.tpl" );
