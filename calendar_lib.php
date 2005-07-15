<?php

class CalendarLib extends BitBase {
	var $gContentTypes;

	function CalendarLib() {
				BitBase::BitBase();
	}

	function listCalendars($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {
		$mid = '';
		$res = array();
		$bindvars = array();
		if ($find) {
			$mid = "where `name` like ?";
			$bindvars[] = $findesc;
		}
		$query = "select * from `".BIT_DB_PREFIX."tiki_calendars` $mid order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_calendars` $mid";
		$cant = $this->getOne($query_cant,$bindvars);

		$res = array();
		while ($r = $result->fetchRow()) {
			$k = $r["calendar_id"];
			$res["$k"] = $r;
		}
		$retval["data"] = $res;
		$retval["cant"] = $cant;
		return $retval;
	}

	// give out an array with Ids viewable by $user_id
	function listUserCalIds() {
		global $gBitUser;
		$user_id = $gBitUser->getUserId();
		if ($user_id) {
			// $groups = $userlib->get_user_groups($user_id);
			// need to add something
			$query = "select `calendar_id` from `".BIT_DB_PREFIX."tiki_calendars` where `user_id`=?";
			$bindvars=array($user_id);
		} else {
			$query = "select `calendar_id` from `".BIT_DB_PREFIX."tiki_calendars`";
			$bindvars=array();
		}
		$result = $this->query($query,$bindvars);
		$ret = array();
		while ($r = $result->fetchRow()) {
			$res[] = $r['calendar_id'];
		}
		return $res;
	}

	function setCalendar($calendar_id, $user_id, $name, $description, $customflags=array()) {
		$name = strip_tags($name);
		$description = strip_tags($description);
		$now = time();
		if ($calendar_id > 0) {
			// modification of a calendar
			$query = "update `".BIT_DB_PREFIX."tiki_calendars` set `name`=?, `user_id`=?, `description`=?, ";
			$bindvars = array($name,$user_id,$description);
			foreach ($customflags as $k => $v) {
				$query .= "`$k`=?, ";
				$bindvars[]=$v;
			}
			$query .= "`lastmodif`=?  where `calendar_id`=?";
			$bindvars[] = $now;
			$bindvars[] = $calendar_id;
			$result = $this->query($query,$bindvars);
		} else {
			// create a new calendar
			$query = "insert into `".BIT_DB_PREFIX."tiki_calendars` (`name`,`user_id`,`description`,`created`,`lastmodif`,`" . implode("`,`", array_keys($customflags)). "`) ";
			$query.= "values (?,?,?,?,?," . implode(",", array_fill(0,count($customflags),"?")). ")";
			$bindvars=array($name,$user_id,$description,$now,$now);
			foreach ($customflags as $k => $v) $bindvars[]=$v;
			$result = $this->query($query,$bindvars);
			$calendar_id=$this->GetOne("select `calendar_id` from `".BIT_DB_PREFIX."tiki_calendars` where `created`=?",array($now));
		}
		return $calendar_id;
	}

	function getCalendar($calendar_id) {
		$res = $this->query("select * from `".BIT_DB_PREFIX."tiki_calendars` where `calendar_id`=?",array((int)$calendar_id));
		return $res->fetchRow();
	}

	function dropCalendar($calendar_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_calendars` where `calendar_id`=?";
		$this->query($query,array($calendar_id));
	}

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

	function listItems($calIds, $user_id, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		$where = array();
		$bindvars=array();
		foreach ($calIds as $calendar_id) {
			$where[] = "i.`calendar_id`=?";
			$bindvars[] = $calendar_id;
		}

		$cond = "(" . implode(" or ", $where). ")";
		$cond .= " and ((i.`start_time` > ? and i.`end_time` < ?) or (i.`start_time` < ? and i.`end_time` > ?))";
		$bindvars[] = $tstart;
		$bindvars[] = $tstop;
		$bindvars[] = $tstop;
		$bindvars[] = $tstart;

		$query = "select i.`calitem_id`, i.`name`, i.`description`, i.`start_time`, i.`end_time`, ";
		$query .= "i.`url`, i.`status`, i.`priority`, c.`name` as `calname`, i.`calendar_id` ";
		$query .= "from `".BIT_DB_PREFIX."tiki_calendar_items` i left join `".BIT_DB_PREFIX."tiki_calendars` c on i.`calendar_id`=c.`calendar_id` ";
		$query .= "where ($cond) ";
		$result = $this->query($query,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$dstart = mktime(0, 0, 0, date("m", $res['start_time']), date("d", $res['start_time']), date("Y", $res['start_time']));
			$dend = mktime(0, 0, 0, date("m", $res['end_time']), date("d", $res['end_time']), date("Y", $res['end_time']));
			$tstart = date("Hi", $res["start_time"]);
			$tend = date("Hi", $res["end_time"]);
			for ($i = $dstart; $i <= $dend; $i = ($i + (60 * 60 * 24))) {
				if ($dstart == $dend) {
					$head = date("H:i", $res["start_time"]). " - " . date("H:i", $res["end_time"]);
				} elseif ($i == $dstart) {
					$head = date("H:i", $res["start_time"]). " ...";
				} elseif ($i == $dend) {
					$head = " ... " . date("H:i", $res["end_time"]);
				} else {
					$head = " ... " . tra("continued"). " ... ";
				}

				$ret["$i"][] = array(
					"result" => $res,
					"calitem_id" => $res["calitem_id"],
					"calname" => $res["calname"],
					"time" => $tstart,
					"type" => $res["status"],
					"web" => $res["url"],
					"prio" => $res["priority"],
					"url" => CALENDAR_PKG_URL."index.php?todate=$i&amp;editmode=1&amp;calitem_id=" . $res["calitem_id"],
					"name" => $res["name"],
					"extra" => "<div align='right'>... " . tra("click to edit"),
					"head" => $head,
					"description" => str_replace("\n|\r", "", $res["description"])
				);
			}
		}
		return $ret;
	}

	function listTikiItems($tikiobj, $user_id, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		$ret = array();

		$res = $dstart = '';

		foreach ($tikiobj as $tik) {
//				$query = "select *, ( select page_id from `".BIT_DB_PREFIX."tiki_pages` where `content_id` = tc.`content_id` )" .
				$query = "select * from `".BIT_DB_PREFIX."tiki_content` tc where (`last_modified`>? and `last_modified`<?) and `content_type_guid` = '".$tik."'";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$dstart = mktime(0, 0, 0, date("m", $res['last_modified']), date("d", $res['last_modified']), date("Y", $res['last_modified']));
					$tstart = date("Hi", $res["last_modified"]);
					$quote = "<i>" . tra("by"). " " . $res["modifier_user_id"] . "</i><br/>" . addslashes(str_replace('"', "'", $res["title"]));
					$ret["$dstart"][] = array(
						"calitem_id" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"type" => $tik,
						"url" => BIT_ROOT_URL.$this->gContentTypes[$tik]['handler']."/index.php?content_id=" . $res["content_id"],
						"name" => $res["title"],
						"head" => "<b>" . date("H:i", $res["last_modified"]). "</b> " . tra("in"). " <b>$tik</b>",
						"description" => str_replace("\n|\r", "", $quote)
					);
				}
		}
		return $ret;
	}

	function getItem($calitem_id) {
		$query = "select i.`calitem_id`, i.`calendar_id`, i.`user_id`, i.`start_time`, i.`end_time`, t.`name` as `calname`, ";
		$query.= "i.`location_id` as `location_id`, l.`name` as `locationName`, i.`category_id` as `category_id`, c.`name` as `categoryName`, i.`priority` as `priority`, ";
		$query.= "i.`status` as `status`, i.`url` as `url`, i.`lang` as `lang`, i.`name` as `name`, i.`description` as `description`, i.`created` as `created`, i.`lastmodif` as `last_modified`, ";
		$query.= "t.`customlocations` as `customlocations`, t.`customcategories` as `customcategories`, t.`customlanguages` as `customlanguages`, t.`custompriorities` as `custompriorities`, ";
		$query.= "t.`customparticipants` as `customparticipants` from `".BIT_DB_PREFIX."tiki_calendar_items` i ";
		$query.= "left join `".BIT_DB_PREFIX."tiki_calendar_locations` l on i.`location_id`=l.`calloc_id` ";
		$query.= "left join `".BIT_DB_PREFIX."tiki_calendar_categories` c on i.`category_id`=c.`cal_cat_id` ";
		$query.= "left join `".BIT_DB_PREFIX."tiki_calendars` t on i.`calendar_id`=t.`calendar_id` where `calitem_id`=?";
		$result = $this->query($query,array($calitem_id));
		$res = $result->fetchRow();
		$query = "select `user_id`, `role` from `tiki_calendar_roles` where `calitem_id`=? order by `role`";
		$rezult = $this->query($query,array($calitem_id));
		$ppl = array();
		$org = array();

		while ($rez = $rezult->fetchRow()) {
			if ($rez["role"] == '6') {
				$org[] = $rez["user_id"];
			} elseif ($rez["user_id"]) {
				$ppl[] = $rez["user_id"] . ":" . $rez["role"];
			}
		}

		$res["participants"] = implode(',', $ppl);
		$res["organizers"] = implode(',', $org);
		return $res;
	}

	function setItem($user_id, $calitem_id, $data) {
		if (!$data["location_id"] and !$data["newloc"]) {
			$data["newloc"] = tra("not specified");
		}
		else if (trim($data["newloc"])) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_calendar_locations` where `calendar_id`=? and `name`=?";
			$bindvars=array($data["calendar_id"],trim($data["newloc"]));
			$this->query($query,$bindvars,-1,-1,false);
			$query = "insert into `".BIT_DB_PREFIX."tiki_calendar_locations` (`calendar_id`,`name`) values (?,?)";
			$this->query($query,$bindvars);
			$data["location_id"] = $this->GetOne("select `calloc_id` from `".BIT_DB_PREFIX."tiki_calendar_locations` where `calendar_id`=? and `name`=?",$bindvars);
		}

		if (!$data["location_id"] and !$data["newcat"]) {
			$data["newcat"] = tra("not specified");
		}
		else if (trim($data["newcat"])) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_calendar_locations` where `calendar_id`=? and `name`=?";
			$bindvars=array($data["calendar_id"],trim($data["newcat"]));
			$this->query($query,$bindvars,-1,-1,false);
			$query = "insert into `".BIT_DB_PREFIX."tiki_calendar_locations` (`calendar_id`,`name`) values (?,?)";
			$this->query($query,$bindvars);
			$data["location_id"] = $this->GetOne("select `calloc_id` from `".BIT_DB_PREFIX."tiki_calendar_locations` where `calendar_id`=? and `name`=?",$bindvars);
		}

		$roles = array();
		if ($data["organizers"]) {
			$orgs = split(',', $data["organizers"]);
			foreach ($orgs as $o) {
				$roles['6'][] = trim($o);
			}
		}
		if ($data["participants"]) {
			$parts = split(',', $data["participants"]);
			foreach ($parts as $pa) {
				$p = split('\:', trim($pa));
				if (isset($p[0])and isset($p[1])) {
					$roles["$p[1]"][] = trim($p[0]);
				}
			}
		}

		if ($calitem_id) {
			$query = "update `".BIT_DB_PREFIX."tiki_calendar_items` set `calendar_id`=?,`user_id`=?,`start_time`=?,`end_time`=? ,`location_id`=? ,`category_id`=?,`priority`=?,`status`=?,`url`=?,";
			$query.= "`lang`=?,`name`=?,`description`=?,`lastmodif`=? where `calitem_id`=?";
			$bindvars=array((int)$data["calendar_id"],$user_id,(int)$data["start"],(int)$data["end"],(int)$data["location_id"],(int)$data["category_id"],(int)$data["priority"],
			                $data["status"],$data["url"],$data["lang"],$data["name"],$data["description"],(int)time(),(int)$calitem_id);
			$result = $this->query($query,$bindvars);
		} else {
			$now=time();
			$query = "insert into `".BIT_DB_PREFIX."tiki_calendar_items` (`calendar_id`, `user_id`, `start_time`, `end_time`, `location_id`, `category_id`, ";
			$query.= " `priority`, `status`, `url`, `lang`, `name`, `description`, `created`, `lastmodif`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($data["calendar_id"],$user_id,$data["start"],$data["end"],$data["location_id"],$data["category_id"],$data["priority"],$data["status"],$data["url"],$data["lang"],$data["name"],$data["description"],$now,$now);
			$result = $this->query($query,$bindvars);
			$calitem_id = $this->GetOne("select `calitem_id` from `".BIT_DB_PREFIX."tiki_calendar_items` where `calendar_id`=? and `created`=?",array($data["calendar_id"],$now));
		}

		if ($calitem_id) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_calendar_roles` where `calitem_id`=?";
			$this->query($query,array($calitem_id));
		}

		foreach ($roles as $lvl => $ro) {
			foreach ($ro as $r) {
				$query = "insert into `".BIT_DB_PREFIX."tiki_calendar_roles` (`calitem_id`,`user_id`,`role`) values (?,?,?)";
				$this->query($queryi,array($calitem_id,$r,$lvl));
			}
		}
		return $calitem_id;
	}

	function dropItem($user_id, $calitem_id) {
		if ($calitem_id) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_calendar_items` where `calitem_id`=?";
			$this->query($query,array($calitem_id));
		}
	}

	function listLocations($calendar_id) {
		$res = array();
		if ($calendar_id > 0) {
			$query = "select `calloc_id` as location_id, `name` from `".BIT_DB_PREFIX."tiki_calendar_locations` where `calendar_id`=?";
			$result = $this->query($query,array($calendar_id));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
		}
		return $res;
	}

	function listCategories($calendar_id) {
		$res = array();
		if ($calendar_id > 0) {
			$query = "select `cal_cat_id` as `category_id`, `name` from `".BIT_DB_PREFIX."tiki_calendar_categories` where `calendar_id`=?";
			$result = $this->query($query,array($calendar_id));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
		}
		return $res;
	}
}
?>
