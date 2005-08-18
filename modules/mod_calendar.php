<?php
$gBitSmarty->assign( "todate", !empty( $_SESSION['calendar_focus_date'] ) ? $_SESSION['calendar_focus_date'] : date() );
?>
