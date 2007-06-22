<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/box.php,v 1.4 2007/06/22 09:06:40 lsces Exp $
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
require_once( '../bit_setup_inc.php' );

global $gContent, $gBitSystem;
include_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

$gContent->mInfo['rendered'] = $gContent->getPreview();

if (!empty($gContent->mInfo['content_type_guid'])) {
	$gContent->mInfo['content_description'] = $gLibertySystem->mContentTypes[$gContent->mInfo['content_type_guid']]['content_description'];
}

$gBitSmarty->assign('cellHash', $gContent->mInfo);

$gBitSmarty->display( "bitpackage:calendar/calendar_box.tpl" );