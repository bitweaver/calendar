<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Attic/CalendarLib.php,v 1.4 2005/07/24 17:41:13 lsces Exp $
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

		foreach ($bitobj as $bit) {
				$query = "select * from `".BIT_DB_PREFIX."tiki_content` tc where (`last_modified`>? and `last_modified`<?) and `content_type_guid` = '".$bit."'";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$dstart = mktime(0, 0, 0, date("m", $res['last_modified']), date("d", $res['last_modified']), date("Y", $res['last_modified']));
					$tstart = date("Hi", $res["last_modified"]);
					$quote = "<i>" . tra("by"). " " . $res["modifier_user_id"] . "</i><br/>" . addslashes(str_replace('"', "'", $res["title"]));
					$ret["$dstart"][] = array(
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"type" => $bit,
						"url" => BIT_ROOT_URL.$gLibertySystem->mContentTypes[$bit]['handler_package']."/index.php?content_id=" . $res["content_id"],
						"name" => $res["title"],
						"head" => "<b>" . date("H:i", $res["last_modified"]). "</b> " . tra("in"). " <b>$bit</b>",
						"description" => str_replace("\n|\r", "", $quote)
					);
				}
		}
		return $ret;
	}
}
?>
