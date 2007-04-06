<?php

// $Header: /cvsroot/bitweaver/_bit_calendar/box.php,v 1.3 2007/04/06 14:30:10 nickpalmer Exp $

// Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

global $gContent, $gBitSystem;
include_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

$gContent->mInfo['rendered'] = $gContent->getPreview();

if (!empty($gContent->mInfo['content_type_guid'])) {
	$gContent->mInfo['content_description'] = $gLibertySystem->mContentTypes[$gContent->mInfo['content_type_guid']]['content_description'];
}

$gBitSmarty->assign('cellHash', $gContent->mInfo);

$gBitSmarty->display( "bitpackage:calendar/calendar_box.tpl" );