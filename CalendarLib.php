<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Attic/CalendarLib.php,v 1.6 2005/08/18 11:37:11 squareing Exp $
 * @package calendar
 */

/**
 * @package calendar
 * @subpackage CalendarLib
 */
class CalendarLib extends LibertyContent {

	function CalendarLib() {
		LibertyContent::LibertyContent();
	}

    /**
	* This method generates a calendar entry record which is displayed as a fly over pop-up.
	* The Liberty items to be displayed are defined in the $bitobj array
	* At present no filtering is provided on $user_id
	* It a full array of items between $tstart and $tstop 
	**/
	function getList($bitobj, $user_id, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		global $gLibertySystem, $gBitUser;
		$ret = array();

		$res = $dstart = '';

		foreach( $bitobj as $bit ) {
			$query = "SELECT tc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name
				FROM `".BIT_DB_PREFIX."tiki_content` tc
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON ( uue.`user_id` = tc.`modifier_user_id` )
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON ( uuc.`user_id` = tc.`user_id` )
				WHERE (`last_modified`>? AND `last_modified`<?) AND `content_type_guid` = ?
				ORDER BY tc.`last_modified`";
			$result = $this->mDb->query( $query, array( $tstart, $tstop, $bit ) );

			while( $res = $result->fetchRow() ) {
				$dstart = mktime(0, 0, 0, date("m", $res['last_modified']), date("d", $res['last_modified']), date("Y", $res['last_modified']));
				$tstart = date("Hi", $res["last_modified"]);

				$aux = $res;
				$aux['calname'] = "";
				$aux['prio'] = "";
				$aux['time'] = $tstart;
				$aux['type'] = $bit;
				$aux['url'] = BIT_ROOT_URL.$gLibertySystem->mContentTypes[$bit]['handler_package']."/index.php?content_id=" . $res["content_id"];
				$aux['name'] = $res["title"];
				$ret[$dstart][] = $aux;
			}
		}
		return $ret;
	}
}
?>
