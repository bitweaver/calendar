<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.49 2008/07/21 13:37:05 lsces Exp $
 * @package calendar
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 * Required setup
 */
include_once( KERNEL_PKG_PATH . 'BitDate.php' );
// set week offset - start with a day other than monday
define( 'WEEK_OFFSET', !empty( $gBitUser->mUserPrefs['calendar_week_offset'] ) ? $gBitUser->mUserPrefs['calendar_week_offset'] : $gBitSystem->getConfig( 'calendar_week_offset', 0 ) );

/**
 * @package calendar
 */
class Calendar extends LibertyContent {

	var $display_offset;
	
	function Calendar() {
		LibertyContent::LibertyContent();
		global $gBitUser;
		$this->mDate = new BitDate(0);
		$this->display_offset = BitDate::get_display_offset();
	}

	/**
	* get a full list of content for a given time period
	* return array of items
	* 
	* The output array will be a set of UTC tagged pages covering the period
	* defined in the $pListHash. Items identified from the list will be tagged to 
	* the day identified in the selected timestamp from which the list was built.
	* If the user has selected a local time display, then the day will be the actual
	* UTC day that the item is in, rather than the UTC day of the item. In this way
	* the display view provides a list of locally correct entries for each day. 
	**/
	function getList( $pListHash ) {
		$ret = array();
		$res = $this->getContentList( $pListHash );

		foreach( $res['data'] as $item ) {
			// shift all time data by user timezone offset
			// and then display as a simple UTC time
			$item['timestamp']     = $item[$pListHash['time_limit_column']] + $this->display_offset;
			$item['created']       = $item['created']       + $this->display_offset;
			$item['last_modified'] = $item['last_modified'] + $this->display_offset;
			$item['event_time']	   = $item['event_time'] + $this->display_offset;
 			$item['parsed'] = $this->parseData($item['data'], $item['format_guid']);
			$dstart = $this->mDate->gmmktime( 0, 0, 0, $this->mDate->date( "m", $item['timestamp'], true ), $this->mDate->date( "d", $item['timestamp'], true ), $this->mDate->date( "Y", $item['timestamp'], true ) );
			$ret[$dstart][] = $item;
		}
	return $ret;
	}

	/**
	* calculate the start and stop time for the current display page
	**/
	function doRangeCalculations( $pDateHash ) {
		$focus = $this->mDate->_getdate( $pDateHash['focus_date'], false, true );

		if( $pDateHash['view_mode'] == 'month' ) {
			$view_start = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'],     1, $focus['year'] );
			$view_end   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] + 1, 1, $focus['year'] ) - 1;
		} elseif( $pDateHash['view_mode'] == 'week' or $pDateHash['view_mode'] == 'weeklist') {
			if ( $focus['wday'] == 0 ) {
				$wd = 7 + WEEK_OFFSET;
			} else {
				$wd  = $focus['wday'] + WEEK_OFFSET;
			}
			// if we are moving out from the selected week, move us back in
			if( $wd > 7 ) {
				$wd -= 7;
			}

			$view_start = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - $wd, $focus['year'] );
			$view_end   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - $wd + 7, $focus['year'] ) - 1;
		} else {
			$view_start = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday']    , $focus['year'] );
			$view_end   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 1, $focus['year'] ) - 1;
		}

		$start_year  = $this->mDate->date( 'Y', $view_start, true );
		if ( $start_year < 1902 ) {
			$view_start_iso = $view_start  = $this->mDate->date( 'Y-m-d', $view_start, true );
			$view_end_iso = $view_end  = $this->mDate->date( 'Y-m-d', $view_start, true );
			$view_start = 0;
			$view_end = 0;
		}

		$ret = array(
			'view_start' => $view_start,
			'view_end' => $view_end,
		);
		// Insert ISO dates if they are set - Used for dates pre 1902
		if ( !empty($view_start_iso) ) {
			$ret['view_start_iso'] = $view_start_iso;
			$ret['view_end_iso'] = $view_end_iso;
		}
		return $ret;
	}

	/**
	* prepare ListHash to ensure errorfree usage
	**/
	function prepGetList( &$pListHash ) {
		$pListHash['include_data'] = TRUE;
		if( !empty( $pListHash['focus_date'] ) ) {
			$calDates = $this->doRangeCalculations( $pListHash );
			$pListHash['time_limit_start'] = $calDates['view_start'] - $this->display_offset;
			$pListHash['time_limit_stop'] = $calDates['view_end'] - $this->display_offset;
		}
		if (  empty( $pListHash['sort_mode'] ) ) {
			$pListHash['sort_mode'] = !empty( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : 'event_time_asc';
		}
		$pListHash['time_limit_column'] = preg_replace( "/(_asc$|_desc$)/i", "", $pListHash['sort_mode'] );

		if ( empty( $pListHash['user_id'] ) ) {
			$pListHash['user_id'] = !empty( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : NULL;
		}
		if ( !empty( $_REQUEST['order_table'] ) ) {
			$pListHash['order_table'] = $_REQUEST['order_table'];
		}

		// Don't think this is required.
		$pListHash['offset'] = 0;
		// There should at least be a preference for this.
		$pListHash['max_records'] = 500;

		LibertyContent::prepGetList( $pListHash );
		return TRUE;
	}

	function buildDay( $pDateHash ) {
		global $gBitSystem, $gBitUser;
		$focus = $this->mDate->getdate( $pDateHash['focus_date'], false, true );

		$ret = array();
		if( $pDateHash['view_mode'] == 'day' ) {
			// calculare what the visible day view range is
			$day_start   = isset( $gBitUser->mUserPrefs['calendar_day_start'] ) ? $gBitUser->mUserPrefs['calendar_day_start'] : $gBitSystem->getConfig( 'calendar_day_start', 0 );
			$day_end     = isset( $gBitUser->mUserPrefs['calendar_day_end'] ) ? $gBitUser->mUserPrefs['calendar_day_end'] : $gBitSystem->getConfig( 'calendar_day_end', 24 );
			$start_time  = $this->mDate->mktime( 0, 0, 0, $focus['mon'], $focus['mday'], $focus['year'] ) + ( 60 * 60 * $day_start );
			$stop_time   = $this->mDate->mktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 1, $focus['year'] ) - ( 60 * 60 * ( 24 - $day_end ) );
			$hours_count = ( $stop_time - $start_time ) / ( 60 * 60 );

			// allow for custom time intervals
			$hour_fraction = !empty( $gBitUser->mUserPrefs['calendar_hour_fraction'] ) ? $gBitUser->mUserPrefs['calendar_hour_fraction'] : $gBitSystem->getConfig( 'calendar_hour_fraction', 1 );
			$row_count = $hours_count * $hour_fraction;
			$start_time_info = $this->mDate->getdate( $start_time, false, true );
			$hour = $start_time_info['hours'] - 1;
			$mins = 0;
			for( $i = 0; $i < $row_count; $i++ ) {
				if( !( $i % $hour_fraction ) ) {
					// set vars
					$hour++;
					$mins = 0;
				}
				$ret[$i]['time'] = $this->mDate->gmmktime( $hour, $mins, 0, $focus['mon'], $focus['mday'], $focus['year'] );
				$mins += 60 / $hour_fraction;
			}
			// calendar data is added below
		}
		return $ret;
	}

	/**
	 * build an array of unix UTC timestamps relating to the current
	 * calendar focus. This provides a fixed basis from which to apply local
	 * offsets provided by tz_offset information. Daylight saving information
	 * is not available via this route! 
	 **/
	function buildCalendarNavigation( $pDateHash ) {
		global $gBitUser, $gBitSystem;
		$today = $this->mDate->getdate( gmmktime(), false, true );
		$focus = $this->mDate->getdate( $pDateHash['focus_date'], false, true );

		$ret = array(
			'before' => array(
				'day'   => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - 1, $focus['year'] ),
				'week'  => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - 7, $focus['year'] ),
				'month' => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] - 1, $focus['mday'], $focus['year'] ),
				'year'  => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'], $focus['year'] - 1 ),
			),
			'after' => array(
				'day'   => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 1, $focus['year'] ),
				'week'  => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 7, $focus['year'] ),
				'month' => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] + 1, $focus['mday'], $focus['year'] ),
				'year'  => $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'], $focus['year'] + 1 ),
			),
			'focus_month' => $focus['mon'],
			'focus_year' => $focus['year'],
			'focus_date' => $focus[0],
			'today'   => $this->mDate->gmmktime( 0, 0, 0, $today['mon'], $today['mday'], $today['year'] ),
			'tz_flag' => $gBitUser->getPreference('site_display_utc', "Local"),
			'display_focus_date' => $focus[0],
		);

		return $ret;
	}

	/**
	 * build a two dimensional array of unix timestamps
	 * The timestamps are either UTC or display local time depending on the
	 * setting of the current users display time offset
	 * mktime SHOULD NOT BE USED since it offsets the times based on server 
	 * timezone and daylight saving, but the USERS daylight saving information
	 * is not available, which will cause some problems!
	 **/
	function buildMonth( $pDateHash ) {
		global $gBitSmarty;

		$focus = $this->mDate->getdate( $pDateHash['focus_date'], false, true );

		$prev_month_end	  = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'],     0, $focus['year'] );
		$next_month_begin = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] + 1, 1, $focus['year'] );

		$prev_month_end_info = $this->mDate->getdate( $prev_month_end, false, true );
		$prev_month = $prev_month_end_info['mon'];
		$prev_month_year = $prev_month_end_info['year'];

		// Build a two-dimensional array of UNIX timestamps.
		$cal = array();

		// Start the first row with the final day( s ) of the previous month.
		$week = array();
		$month_begin = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], WEEK_OFFSET, $focus['year'] );
		$month_begin_day_of_week = $this->mDate->dayOfWeek( $focus['year'], $focus['mon'], WEEK_OFFSET );
		$days_in_prev_month = $this->mDate->daysInMonth( $prev_month, $prev_month_year );

		// Fill out the first row with the last day( s ) of the previous month.
		for( $day_of_week = 0; $day_of_week < $month_begin_day_of_week; $day_of_week++ ) {
			$_day = $days_in_prev_month - $month_begin_day_of_week + $day_of_week + 1;
			$week[]['day'] = $this->mDate->mktime( 0, 0, 0, $prev_month, $_day, $prev_month_year );
		}

		// Fill in the days of the selected month.
		$days_in_month = $this->mDate->daysInMonth( $focus['mon'], $focus['year'] );
		for( $i = 1; $i <= $days_in_month; $i++ ) {
			if( $day_of_week == 7 ) {
				$calendar[$this->mDate->weekOfYear( $focus['year'], $focus['mon'], $i )] = $week;

				// re-initialize $day_of_week and $week
				$day_of_week = 0;
				$week = array();
			}
			$week[]['day'] = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $i, $focus['year'] );
			$day_of_week++;
		}

		// Fill out the last row with the first day( s ) of the next month.
		for( $j = 1; $day_of_week < 7; $j++, $day_of_week++ ) {
			$week[]['day'] = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] + 1, $j, $focus['year'] );
		}
		$week_num = $this->mDate->weekOfYear( $focus['year'], $focus['mon'], $days_in_month + $j );
		$calendar[$week_num] = $week;

		// Modify offset to fix roll over on week numbers
		// This is required because the week numbers are calculated for Sunday
		// Offseting the result in BitDate is the real solution
		if ( WEEK_OFFSET == 7 ) $offset = $focus['mday'] + 1;
		else $offset = $focus['mday'] + 1 + WEEK_OFFSET;
		// this week number has to be calculated, since the cal start can be configured
		$week_num = $this->mDate->weekOfYear( $focus['year'], $focus['mon'], $offset );

		// if we only want to see a weeks / days worth of data, nuke all xs data
		if( $pDateHash['view_mode'] == 'week' or $pDateHash['view_mode'] == 'weeklist' ) {
			$cal = $calendar[$week_num];
			$calendar = array();
			$calendar[$week_num] = $cal;
		} elseif( $pDateHash['view_mode'] == 'day' ) {
			$calendar = array();
			$calendar[$week_num][]['day'] = $pDateHash['focus_date'];
		}

		return $calendar;
	}

	// Setup the content types for use in the calendar.
	function setupContentTypes() {
		global $gLibertySystem, $gBitSmarty, $gBitSystem;
		foreach( $gLibertySystem->mContentTypes as $cName => $cType ) {
			if ( $gBitSystem->isPackageActive( $cType['handler_package'] ) ) {
				$contentTypes[$cType['content_type_guid']] = $cType['content_description'];
			}
		}
		asort($contentTypes);
		$gBitSmarty->assign( 'calContentTypes', $contentTypes );
	}

	// Setup the day names for use in the calendar
	function setupDayNames() {
		global $gBitSmarty;

		// set up daynames for the calendar
		$dayNames = array(
			tra( "Monday" ),
			tra( "Tuesday" ),
			tra( "Wednesday" ),
			tra( "Thursday" ),
			tra( "Friday" ),
			tra( "Saturday" ),
			tra( "Sunday" ),
			);

		// depending on what day we want to view first, we need to adjust the dayNames array
		for( $i = 0; $i < WEEK_OFFSET; $i++ ) {
			$pop = array_pop( $dayNames );
			array_unshift( $dayNames, $pop );
		}
		$gBitSmarty->assign( 'dayNames', $dayNames );
	}

	function processRequestHash(&$pRequest, &$pStore) {
		global $gBitUser;
		if( !empty( $pRequest["content_type_guid"] ) ) {
			if( $gBitUser->isRegistered() ) {
				$gBitUser->storePreference( 'calendar_default_guids', serialize( $pRequest['content_type_guid'] ) );
			}
			$pStore['content_type_guid'] = $pRequest["content_type_guid"];
		} elseif( !isset( $pStore['content_type_guid'] ) && $gBitUser->getPreference( 'calendar_default_guids' ) && $gBitUser->isRegistered() ) {
			$pStore['content_type_guid'] = unserialize( $gBitUser->getPreference( 'calendar_default_guids' ) );
		} elseif( !isset( $pStore['content_type_guid'] ) ) {
			$pStore['content_type_guid'] = array();
		} elseif( isset( $pRequest["refresh"] ) && !isset( $pRequest["content_type_guid"] ) ) {
			$pStore['content_type_guid'] = array();
		}

		// set up the todate
		if( !empty( $pRequest["todate"] ) ) {
			// clean up todate. who knows where this has come from
			if ( is_numeric( $pRequest['todate'] ) ) {
				$pStore['focus_date'] = $pRequest['todate'] = $this->mDate->gmmktime( 0, 0, 0, $this->mDate->date( 'm', $pRequest['todate'], true ), $this->mDate->date( 'd', $pRequest['todate'], true ), $this->mDate->date( 'Y', $pRequest['todate'], true ) );
			} else {
				$pStore['focus_date'] = $pRequest['todate'] = $this->mDate->gmmktime( 0, 0, 0, $this->mDate->date2( 'm', $pRequest['todate'], true ), $this->mDate->date2( 'd', $pRequest['todate'], true ), $this->mDate->date2( 'Y', $pRequest['todate'], true ) );
			}
		} elseif( !empty( $pStore['focus_date'] ) ) {
			$pRequest["todate"] = $pStore['focus_date'];
		} else {
			$pStore['focus_date'] = $this->mDate->gmmktime( 0, 0, 0, $this->mDate->date( 'm' ), $this->mDate->date( 'd' ), $this->mDate->date( 'Y' ) );
			$pRequest["todate"] = $pStore['focus_date'];
		}

		$focus = $pRequest['todate'];
		if( !empty( $pRequest["view_mode"] ) ) {
			$pStore['view_mode'] = $pRequest["view_mode"];
		} elseif( empty( $pStore['view_mode'] ) ) {
			$pStore['view_mode'] = 'month';
		}
	}

	function getEvents(&$pListHash) {
		global $gBitSystem, $gLibertySystem;

		$bitEvents = array();
		if ( !empty( $pListHash['content_type_guid'] ) ) {
			// Verify that the type is still active
			foreach ( $pListHash['content_type_guid'] as $index => $type ) {
				if ( !$gBitSystem->isPackageActive( $gLibertySystem->mContentTypes[$type]['handler_package'] ) ) {
					unset( $pListHash['content_type_guid'][$index] );
				}
				if ( !empty( $pListHash['content_type_guid'] ) ) {
					$bitEvents = $this->getList( $pListHash );
				}
			}
		}

		return $bitEvents;
	}

	function buildCalendar(&$pListHash, &$pDateHash) {
		global $gBitSmarty, $gBitSystem;

		if( isset($pDateHash['focus_date']) && !isset($pListHash['focus_date']) ) {
			$pListHash['focus_date'] = $pDateHash['focus_date'];
		}
		if( isset($pDateHash['view_mode']) && !isset($pListHash['view_mode']) ) {
			$pListHash['view_mode'] = $pDateHash['view_mode'];
		}
		$bitEvents = $this->getEvents($pListHash);

		$gBitSmarty->assign( 'navigation', $this->buildCalendarNavigation( $pDateHash ) );
		$calMonth = $this->buildMonth( $pDateHash );
		$calDay = $this->buildDay( $pDateHash );

		foreach( $calMonth as $w => $week ) {
			foreach( $week as $d => $day ) {
				$dayEvents = array();
				if( !empty( $bitEvents[$day['day']] ) ) {
					$i = 0;
					foreach( $bitEvents[$day['day']] as $bitEvent ) {
						$bitEvent['parsed_data'] = $this->parseData($bitEvent);
						$dayEvents[$i] = $bitEvent;
						if (!$gBitSystem->isFeatureActive('calendar_ajax_popups')) {
							$gBitSmarty->assign( 'cellHash', $bitEvent );
							$dayEvents[$i]["over"] = $gBitSmarty->fetch( "bitpackage:calendar/calendar_box.tpl" );
						}

						// populate $calDay array with events
						if( !empty ( $bitEvent ) && $pDateHash['view_mode'] == 'day' ) {
							foreach( $calDay as $key => $t ) {
								// special case - last item entry in array - check this first

								if( $bitEvent['timestamp'] >= $calDay[$key]['time']  && empty( $calDay[$key + 1]['time'] ) ) {
									$calDay[$key]['items'][] = $dayEvents[$i];
								} elseif( $bitEvent['timestamp'] >= $calDay[$key]['time'] && $bitEvent['timestamp'] < $calDay[$key + 1]['time'] ) {
									$calDay[$key]['items'][] = $dayEvents[$i];
								}
							}
						}

						$i++;
					}
				}
				if( !empty( $dayEvents ) ) {
					$calMonth[$w][$d]['items'] = array_values( $dayEvents );
				}
			}
		}
		$gBitSmarty->assign_by_ref( 'calDay', $calDay );
		$gBitSmarty->assign_by_ref( 'calMonth', $calMonth );
	}

	function setupCalendar($pShowContentOptions = TRUE) {
		global $gBitThemes, $gBitSmarty, $gBitSystem;
		if ( $pShowContentOptions ) {
			$this->setupContentTypes();
		}
		$this->setupDayNames();

		if ($gBitSystem->isFeatureActive('calendar_ajax_popups')) {
			$gBitThemes->loadAjax( 'mochikit' );
		}

		// TODO: make this a pref
		$gBitSmarty->assign( 'trunc', $gBitSystem->getConfig( 'title_truncate', 32 ) );
	}

	// Display the actual calendar doing any other work required for the template
	function display($pTitle, $pShowContentOptions = TRUE, $pBaseUrl=NULL) {
		global $gBitSystem, $gBitSmarty;

		$this->setupCalendar($pShowContentOptions);

		// A default base for the calendar
		if( empty($pBaseUrl) ){
			$pBaseUrl = CALENDAR_PKG_URL.'index.php';
		}
		// Asssign it so templates see it.
		$gBitSmarty->assign('baseCalendarUrl', $pBaseUrl);

		$gBitSystem->display( 'bitpackage:calendar/calendar.tpl', $pTitle , array( 'display_mode' => 'display' ));

	}
}
?>
