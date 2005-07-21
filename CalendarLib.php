<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Attic/CalendarLib.php,v 1.3 2005/07/21 09:39:31 lsces Exp $
 * @package calendar
 */

/**
 * @package calendar
 * @subpackage CalendarLib
 */
class CalendarLib extends BitBase {
	var $gContentTypes;

	function CalendarLib() {
				BitBase::BitBase();
	}

    /**
	* This method builds a table of content_type records to be displayed in the calendar.
	* It populates the $this->gContentTypes array
	**/
	function loadContentTypes() {
		global $gBitSystem, $gBitUser;
		$query = "select `content_type_guid`, `content_description`, `handler_class`, `handler_package`, `handler_file` from `".BIT_DB_PREFIX."tiki_content_types` order by `content_type_guid`";
		$rs = $this->query($query);
		$this->gContentTypes = array();
		if ($rs) {
			while (!$rs->EOF) {
				$ctg = $rs->fields['content_type_guid'];
				$this->gContentTypes[$ctg] = array();
				$this->gContentTypes[$ctg]['label'] = tra($rs->fields['content_description']);
				$this->gContentTypes[$ctg]['feature'] = $gBitSystem->getPreference('calendar_'.$rs->fields['handler_package'], 'y');
				$this->gContentTypes[$ctg]['right'] = $gBitUser->getPreference('bit_p_calendar_'.$rs->fields['handler_package'], 'y');
				$this->gContentTypes[$ctg]['content_type_guid'] = $rs->fields['content_type_guid'];
				$this->gContentTypes[$ctg]['handler'] = $rs->fields['handler_package'];
				$rs->MoveNext();
			}
		}
	}

    /**
	* This method generates a calendar entry record which is displayed as a fly over pop-up.
	* The Liberty items to be displayed are defined in the $bitobj array
	* At present no filtering is provided on $user_id
	* It a full array of items between $tstart and $tstop 
	**/
	function listBitItems($bitobj, $user_id, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
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
						"url" => BIT_ROOT_URL.$this->gContentTypes[$bit]['handler']."/index.php?content_id=" . $res["content_id"],
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
