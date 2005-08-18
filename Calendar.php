<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.3 2005/08/18 19:05:03 squareing Exp $
 * @package calendar
 */

/**
 * @package calendar
 * @subpackage Calendar
 */
class Calendar extends LibertyContent {

	function Calendar() {
		LibertyContent::LibertyContent();
	}

	/**
	* This method generates a calendar entry record which is displayed as a fly over pop-up.
	* The Liberty items to be displayed are defined in the $bitObjects array
	* At present no filtering is provided on $user_id
	* It a full array of items between $tstart and $tstop 
	**/
	function getList( $pListHash ) {
		$ret = array();
		include_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
		$content = new LibertyContent();
		$content->prepGetList( $pListHash );
		$res = $content->getContentList( $pListHash );

		foreach( $res['data'] as $item ) {
			$dstart = mktime( 0, 0, 0, date( "m", $item['last_modified'] ), date( "d", $item['last_modified'] ), date( "Y", $item['last_modified'] ) );
			$ret[$dstart][] = $item;
		}

/*		this version of getList doens't use liberty

		global $gLibertySystem, $gBitUser;
		$ret = array();

		if( !empty( $pListHash['bitObjects'] ) ) {
			$order = '';
			$bindVals[] = $pListHash['start'];
			$bindVals[] = $pListHash['stop'];

			$where = "WHERE ( `last_modified`>? AND `last_modified`<? )";
			$where .= " AND ";
			$and = '';
			foreach( $pListHash['bitObjects'] as $bit ) {
				$and .= ( empty( $and ) ? " " : " OR " )."tc.`content_type_guid`=?";
				$bindVals[] = $bit;
			}
			$where .= " ( $and ) ";

			if( !empty( $pListHash['user_id'] ) ) {
				$where .= " AND tc.`user_id`=?";
				$bindVals[] = $pListHash['user_id'];
			}

			if( !empty( $pListHash['sort_mode'] ) ) {
				$order .= " ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] )." ";
			} else {
				$order .= " ORDER BY tc.`last_modified` ASC";
			}

			$query = "SELECT tc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name
				FROM `".BIT_DB_PREFIX."tiki_content` tc
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON ( uue.`user_id` = tc.`modifier_user_id` )
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON ( uuc.`user_id` = tc.`user_id` )
				$where $order";
			$result = $this->mDb->query( $query, $bindVals );

			while( $res = $result->fetchRow() ) {
				$aux = $res;
				$aux['url'] = BIT_ROOT_URL.$gLibertySystem->mContentTypes[$bit]['handler_package']."/index.php?content_id=" . $res["content_id"];
				$aux['name'] = $res["title"];

				$dstart = mktime( 0, 0, 0, date( "m", $res['last_modified'] ), date( "d", $res['last_modified'] ), date( "Y", $res['last_modified'] ) );
				$ret[$dstart][] = $aux;
			}
		}
*/
		return $ret;
	}
}
?>
