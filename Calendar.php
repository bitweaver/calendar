<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.14 2005/08/24 20:34:22 squareing Exp $
 * @package calendar
 */

/**
 * @package calendar
 */
class Calendar extends LibertyContent {

	function Calendar() {
		LibertyContent::LibertyContent();
	}

	/**
	* get a full list of content for a given time period
	* return array of items
	**/
	function getList( $pListHash ) {
		global $gBitSystem, $gBitUser;
		$ret = array();
		if( $this->prepGetList( $pListHash ) ) {
			include_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
			$content = new LibertyContent();
			$content->prepGetList( $pListHash );
			$res = $content->getContentList( $pListHash );

			foreach( $res['data'] as $item ) {
				// shift all time data by user timezone offset
				$item['created']       = $item['created']       - $gBitSystem->get_display_offset();
				$item['last_modified'] = $item['last_modified'] - $gBitSystem->get_display_offset();
				$dstart = adodb_mktime( 0, 0, 0, adodb_date( "m", $item[$pListHash['calendar_sort_mode']] ), adodb_date( "d", $item[$pListHash['calendar_sort_mode']] ), adodb_date( "Y", $item[$pListHash['calendar_sort_mode']] ) );
				$ret[$dstart][] = $item;
			}
		}
		return $ret;
	}

	/**
	* calculate the start and stop time for the current display page
	**/
	function doRangeCalculations( $pDateHash ) {
		global $gBitSystem, $gBitUser;
		$year  = adodb_date( 'Y', $pDateHash['focus_date'] );
		$month = adodb_date( 'm', $pDateHash['focus_date'] );
		$day   = adodb_date( 'd', $pDateHash['focus_date'] );

		if( $pDateHash['view_mode'] == 'month' ) {
			$view_start = adodb_mktime( 0, 0, 0, $month,     1, $year );
			$view_end   = adodb_mktime( 0, 0, 0, $month + 1, 1, $year ) - 1;
		} elseif( $pDateHash['view_mode'] == 'week') {
			$wd  = adodb_date( 'w', $pDateHash['focus_date'] );
			$wd += $gBitSystem->getPreference( 'week_offset', 1 );
			// if we are moving out from the selected week, move us back in
			if( $wd > 7 ) {
				$wd -= 7;
			}

			// for some very odd reason, which i can't work out, we need to add a day here
			$view_start = adodb_mktime( 0, 0, 0, $month, $day - $wd + 1 , $year );
			$view_end   = adodb_mktime( 0, 0, 0, $month, $day - $wd + 8, $year ) - 1;
		} else {
			$view_start = adodb_mktime( 0, 0, 0, $month, $day    , $year );
			$view_end   = adodb_mktime( 0, 0, 0, $month, $day + 1, $year ) - 1;
		}

		// this is where we adjust the start and stop times to user local time settings
		$view_start = $view_start + $gBitSystem->get_display_offset();
		$view_end   = $view_end   + $gBitSystem->get_display_offset();
		$start_year  = adodb_date( 'Y', $view_start );
//		vd( 'start: '.adodb_strftime( '%d %m %Y, %T', $view_start ) );
//		vd( 'end: '.  adodb_strftime( '%d %m %Y, %T', $view_end   ) );
		if ( $start_year < 1902 ) {
			$view_start_iso = $view_start  = adodb_date( 'Y-m-d', $view_start );
			$view_end_iso = $view_end  = adodb_date( 'Y-m-d', $view_start );
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
		global $gBitSystem;
		$year  = adodb_date( 'Y', $pDateHash['focus_date'] );
		$month = adodb_date( 'm', $pDateHash['focus_date'] );
		$day   = adodb_date( 'd', $pDateHash['focus_date'] );

		$ret = array();
		if( $pDateHash['view_mode'] == 'day' ) {
			// calculations in preparation of custom dayview range setting
			// all that needs to be done is adjust start and stop times accordingly
			$start_time  = $pDateHash['focus_date'];
			$stop_time   = adodb_mktime( 0, 0, 0, $month, $day + 1, $year );
			$hours_count = ( $stop_time - $start_time ) / ( 60 * 60 );

			// allow for custom time intervals
			$hour_fraction = $gBitSystem->getPreference( 'hour_fraction', 1 );
			$row_count = $hours_count * $hour_fraction;
			$hour = adodb_strftime( '%H', $start_time ) - 1;
			$mins = 0;
			for( $i = 0; $i < $row_count; $i++ ) {
				if( !( $i % $hour_fraction ) ) {
					// set vars
					$hour++;
					$mins = 0;
				}
				$ret[$i]['time'] = adodb_mktime( $hour, $mins, 0, $month, $day, $year );
				$mins += 60 / $hour_fraction;
			}

			// calendar data is added below
		}
		return $ret;
	}

	function buildCalendarNavigation( $pDateHash ) {
		$year  = adodb_date( 'Y', $pDateHash['focus_date'] );
		$month = adodb_date( 'm', $pDateHash['focus_date'] );
		$day   = adodb_date( 'd', $pDateHash['focus_date'] );

		$ret = array(
			'before' => array(
				'day'   => adodb_mktime( 0, 0, 0, $month, $day - 1, $year ),
				'week'  => adodb_mktime( 0, 0, 0, $month, $day - 7, $year ),
				'month' => adodb_mktime( 0, 0, 0, $month - 1, $day, $year ),
				'year'  => adodb_mktime( 0, 0, 0, $month, $day, $year - 1 ),
			),
			'after' => array(
				'day'   => adodb_mktime( 0, 0, 0, $month, $day + 1, $year ),
				'week'  => adodb_mktime( 0, 0, 0, $month, $day + 7, $year ),
				'month' => adodb_mktime( 0, 0, 0, $month + 1, $day, $year ),
				'year'  => adodb_mktime( 0, 0, 0, $month, $day, $year + 1 ),
			),
			'focus_month' => $month,
			'focus_date' => $pDateHash['focus_date'],
		);

		return $ret;
	}

	/**
	* build a two dimensional array of unix timestamps
	**/
	function buildCalendar( $pDateHash ) {
		global $gBitSmarty, $gBitSystem;

		$focus = adodb_getdate( $pDateHash['focus_date'] );

		// set week offset - start with a day other than monday
		$week_offset = $gBitSystem->getPreference( 'week_offset', 0 );

		$prev_month_end	  = adodb_mktime( 0, 0, 0, $focus['mon'],     0, $focus['year'] );
		$next_month_begin = adodb_mktime( 0, 0, 0, $focus['mon'] + 1, 1, $focus['year'] );

		$prev_month_end_info = adodb_getdate( $prev_month_end );
		$prev_month = $prev_month_end_info['mon'];
		$prev_month_year = $prev_month_end_info['year'];

		// Build a two-dimensional array of UNIX timestamps.
		$cal = array();

		// Start the first row with the final day( s ) of the previous month.
		$week = array();
		$month_begin = adodb_mktime( 0, 0, 0, $focus['mon'], $week_offset, $focus['year'] );
		$month_begin_day_of_week = adodb_dow( $focus['year'], $focus['mon'], $week_offset );
		$days_in_prev_month = $this->daysInMonth( $prev_month, $prev_month_year );

		// Fill out the first row with the last day( s ) of the previous month.
		for( $day_of_week = 0; $day_of_week < $month_begin_day_of_week; $day_of_week++ ) {
			$_day = $days_in_prev_month - $month_begin_day_of_week + $day_of_week;
			$week[]['day'] = adodb_mktime( 0, 0, 0, $focus['mon'] - 1, $_day, $focus['year'] );
		}

		// Fill in the days of the selected month.
		$days_in_month = $this->daysInMonth( $focus['mon'], $focus['year'] );
		for( $i = 1; $i <= $days_in_month; $i++ ) {
			if( $day_of_week == 7 ) {
				$calendar[adodb_woy( $focus['year'], $focus['mon'], $i )] = $week;
				
				// re-initialize $day_of_week and $week
				$day_of_week = 0;
				$week = array();
			}
			$week[]['day'] = adodb_mktime( 0, 0, 0, $focus['mon'], $i, $focus['year'] );
			$day_of_week++;
		}

		// Fill out the last row with the first day( s ) of the next month.
		for( $j = 1; $day_of_week < 7; $j++, $day_of_week++ ) {
			$week[]['day'] = adodb_mktime( 0, 0, 0, $focus['mon'] + 1, $j, $focus['year'] );
		}
		$calendar[adodb_woy( $focus['year'], $focus['mon'], $i + 7 + $week_offset )] = $week;

		// this week number has to be calculated, since the cal start can be configured
		$week_num = adodb_woy( $focus['year'], $focus['mon'], $focus['mday'] );

		// this is needed to display the correct week.
		if( ( $focus['wday'] + $week_offset  ) > 7 ) {
			$week_num++;
		}

		// if we only want to see a weeks / days worth of data, nuke all xs data
		if( $pDateHash['view_mode'] == 'week' ) {
			$cal = $calendar[$week_num];
			$calendar = array();
			$calendar[$week_num] = $cal;
		} elseif( $pDateHash['view_mode'] == 'day' ) {
			$calendar = array();
			$calendar[$week_num][]['day'] = $pDateHash['focus_date'];
		}

		return $calendar;
	}

	function daysInMonth( $month, $year ) {
		switch( $month ) {
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
			case 0: // == 12
				return 31;

			case 4:
			case 6:
			case 9:
			case 11:
				return 30;

			case 2:
				return adodb_is_leap_year( $year ) ? 29 : 28;

			default:
				assert( FALSE );
		}
	}
}
?>
