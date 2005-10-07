<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.25 2005/10/07 07:25:31 squareing Exp $
 * @package calendar
 */

/**
 * Required setup
 */
include_once( KERNEL_PKG_PATH . 'BitDate.php' );

/**
 * @package calendar
 */

// set week offset - start with a day other than monday
define( 'WEEK_OFFSET', !empty( $gBitUser->mUserPrefs['week_offset'] ) ? $gBitUser->mUserPrefs['week_offset'] : $gBitSystem->getPreference( 'week_offset', 0 ) );

class Calendar extends LibertyContent {

	function Calendar() {
		LibertyContent::LibertyContent();
		global $gBitSystem;
		$this->mDate = new BitDate(0);
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
		if( $this->prepGetList( $pListHash ) ) {
			include_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
			$content = new LibertyContent();
			$content->prepGetList( $pListHash );
			$res = $content->getContentList( $pListHash );
			$offset = $this->mDate->get_display_offset();
			foreach( $res['data'] as $item ) {
				// shift all time data by user timezone offset
				// and then display as a simple UTC time
				$item['timestamp']     = $item[$pListHash['calendar_sort_mode']] + $offset;
				$item['created']       = $item['created']       + $offset;
				$item['last_modified'] = $item['last_modified'] + $offset;
				$dstart = $this->mDate->gmmktime( 0, 0, 0, $this->mDate->date( "m", $item['timestamp'], true ), $this->mDate->date( "d", $item['timestamp'], true ), $this->mDate->date( "Y", $item['timestamp'], true ) );
				$ret[$dstart][] = $item;
			}
		}
		return $ret;
	}

	/**
	* calculate the start and stop time for the current display page
	**/
	function doRangeCalculations( $pDateHash ) {
		$focus = $this->mDate->getdate( $pDateHash['focus_date'] );

		if( $pDateHash['view_mode'] == 'month' ) {
			$view_start = $this->mDate->mktime( 0, 0, 0, $focus['mon'],     1, $focus['year'] );
			$view_end   = $this->mDate->mktime( 0, 0, 0, $focus['mon'] + 1, 1, $focus['year'] ) - 1;
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

			// for some very odd reason, which i can't work out, we need to add a day here
			$view_start = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - $wd + 1, $focus['year'] );
			$view_end   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] - $wd + 8, $focus['year'] ) - 1;
		} else {
			$view_start = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday']    , $focus['year'] );
			$view_end   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 1, $focus['year'] ) - 1;
		}

		// this is where we adjust the start and stop times to user local time settings
		// The range is adjusted backwards so that it covers the correct window
		$view_start = $view_start - $this->mDate->get_display_offset();
		$view_end   = $view_end   - $this->mDate->get_display_offset();
		$start_year  = $this->mDate->date( 'Y', $view_start );
		if ( $start_year < 1902 ) {
			$view_start_iso = $view_start  = $this->mDate->date( 'Y-m-d', $view_start );
			$view_end_iso = $view_end  = $this->mDate->date( 'Y-m-d', $view_start );
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
		if( !empty( $pListHash['focus_date'] ) ) {
			$calDates = $this->doRangeCalculations( $pListHash );
			$pListHash['start'] = $calDates['view_start'];
			$pListHash['stop'] = $calDates['view_end'];
		}

		if( !empty( $pListHash['sort_mode'] ) ) {
			$pListHash['calendar_sort_mode'] = preg_replace( "/(_asc$|_desc$)/i", "", $pListHash['sort_mode'] );
		}

		return TRUE;
	}

	function buildDay( $pDateHash ) {
		global $gBitSystem, $gBitUser;
		$focus = $this->mDate->getdate( $pDateHash['focus_date'] );

		$ret = array();
		if( $pDateHash['view_mode'] == 'day' ) {
			// calculare what the visible day view range is
			$day_start   = isset( $gBitUser->mUserPrefs['day_start'] ) ? $gBitUser->mUserPrefs['day_start'] : $gBitSystem->getPreference( 'day_start', 0 );
			$day_end     = isset( $gBitUser->mUserPrefs['day_end'] ) ? $gBitUser->mUserPrefs['day_end'] : $gBitSystem->getPreference( 'day_end', 24 );
			$start_time  = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'], $focus['year'] ) + ( 60 * 60 * $day_start );
			$stop_time   = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'], $focus['mday'] + 1, $focus['year'] ) - ( 60 * 60 * ( 24 - $day_end ) );
			$hours_count = ( $stop_time - $start_time ) / ( 60 * 60 );

			// allow for custom time intervals
			$hour_fraction = !empty( $gBitUser->mUserPrefs['hour_fraction'] ) ? $gBitUser->mUserPrefs['hour_fraction'] : $gBitSystem->getPreference( 'hour_fraction', 1 );
			$row_count = $hours_count * $hour_fraction;
			$start_time_info = $this->mDate->getdate( $start_time );
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
		$focus = $this->mDate->getdate( $pDateHash['focus_date'] );

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
	function buildCalendar( $pDateHash ) {
		global $gBitSmarty;

		$focus = $this->mDate->getdate( $pDateHash['focus_date'] );

		$prev_month_end	  = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'],     0, $focus['year'] );
		$next_month_begin = $this->mDate->gmmktime( 0, 0, 0, $focus['mon'] + 1, 1, $focus['year'] );

		$prev_month_end_info = $this->mDate->getdate( $prev_month_end );
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
			$week[]['day'] = $this->mDate->gmmktime( 0, 0, 0, $prev_month, $_day, $prev_month_year );
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
}
?>
