<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.4 2005/08/19 11:46:31 squareing Exp $
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
		if( $this->prepGetList( $pListHash ) ) {
			include_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
			$content = new LibertyContent();
			$content->prepGetList( $pListHash );
			$res = $content->getContentList( $pListHash );

			foreach( $res['data'] as $item ) {
				$dstart = mktime( 0, 0, 0, date( "m", $item[$pListHash['calendar_sort_mode']] ), date( "d", $item[$pListHash['calendar_sort_mode']] ), date( "Y", $item[$pListHash['calendar_sort_mode']] ) );
				$ret[$dstart][] = $item;
			}
		}
		return $ret;
	}

	function prepGetList( &$pListHash ) {
		if( !empty( $pListHash['focus_date'] ) ) {
			if( !empty( $pListHash['view_mode'] ) ) {
				$pListHash['view_mode'] = 'month';
			}

			$year  = date( 'Y', $pListHash['focus_date'] );
			$month = date( 'm', $pListHash['focus_date'] );
			$day   = date( 'd', $pListHash['focus_date'] );

			$day_secs    = 60 * 60 * 24;
			$currentweek = date( "W", $pListHash['focus_date'] );
			$weekday     = date( 'w', $pListHash['focus_date'] );

			if( $pListHash['view_mode'] == 'day' ) {
				$firstweek = $currentweek;
				$lastweek = $currentweek;
				$viewstart = mktime( 0, 0, 0, $month, $day, $year);
				$viewend = $viewstart + ( $d - 1 );
				$weekdays = array( date( 'w',$focusdate ) );
			} elseif( $pListHash['view_mode'] == 'week' ) {
				// start by putting $viewstart at midnight starting focusdate
				$viewstart = mktime( 0, 0, 0, $month, $day, $year);
				// then back up to the preceding Sunday;
				$viewstart -= $wd * $day_secs;
				// then go to the end of the week for $viewend
				$viewend = $viewstart + ((7 * $d) - 1);
			} else {
				$viewstart = mktime( 0, 0, 0, $month    , 1, $year);
				$viewend   = mktime( 0, 0, 0, $month + 1, 0, $year);
			}
			$pListHash['start'] = $viewstart;
			$pListHash['stop'] = $viewend;
		}

		if( !empty( $pListHash['sort_mode'] ) ) {
			$pListHash['calendar_sort_mode'] = preg_replace( "/(_asc$|_desc$)/i", "", $pListHash['sort_mode'] );
		}

		return TRUE;
	}

	function getCalendarList( $pListHash ) {
		$calendarHash = $this->buildCalendar( $pListHash['focus_date'] );

		$ret = array();
		if( !empty( $pListHash['content_type_guid'] ) ) {
			$year  = date( 'Y', $pListHash['focus_date'] );
			$month = date( 'm', $pListHash['focus_date'] );
			$day   = date( 'd', $pListHash['focus_date'] );

			$day_secs    = 60 * 60 * 24;
			$currentweek = date( "W", $pListHash['focus_date'] );
			$weekday     = date( 'w', $pListHash['focus_date'] );

			if( $pListHash['calendar_view_mode'] == 'month' ) {
				$viewstart = mktime( 0, 0, 0, $month    , 1, $year);
				$viewend   = mktime( 0, 0, 0, $month + 1, 0, $year);
			} elseif( $pListHash['calendar_view_mode'] == 'week' ) {
				// start by putting $viewstart at midnight starting focusdate
				$viewstart = mktime( 0, 0, 0, $month, $day, $year);
				// then back up to the preceding Sunday;
				$viewstart -= $wd * $day_secs;
				// then go to the end of the week for $viewend
				$viewend = $viewstart + ((7 * $d) - 1);
			} else {
				$firstweek = $currentweek;
				$lastweek = $currentweek;
				$viewstart = mktime( 0, 0, 0, $month, $day, $year);
				$viewend = $viewstart + ( $d - 1 );
				$weekdays = array( date( 'w',$focusdate ) );
			}
			$pListHash['start'] = $viewstart;
			$pListHash['stop'] = $viewend;

			$bitEvents = $this->getList( $pListHash );

			foreach( $calendarHash['calendar'] as $week_num => $week ) {
				foreach( $week as $day_num => $day ) {
					$calendar[date( 'W', $day )][$day] = !empty( $bitEvents[$day] ) ? $bitEvents[$day] : NULL;
				}
			}
		}

		$retval['calendar'] = $calendar;
		$retval['navigation'] = $calendarHash['navigation'];

		return $retval;
	}

	// Build a two-dimensional array of UNIX timestamps.
	function buildCalendar( $pTimeStamp ) {
		global $gBitSmarty;

		$year  = date( 'Y', $pTimeStamp );
		$month = date( 'm', $pTimeStamp );
		$day   = date( 'd', $pTimeStamp );

		$prev_month_end   = mktime( 0, 0, 0, $month    , 0, $year );
		$next_month_begin = mktime( 0, 0, 0, $month + 1, 1, $year );

		//$prev_month_end = mktime( 0, 0, 0, $month - 1, $day, $year );
		//$next_month_begin = mktime( 0, 0, 0, $month + 1, $day, $year );

		$prev_month_end_info = getdate( $prev_month_end );
		$prev_month = $prev_month_end_info['mon'];
		$prev_month_year = $prev_month_end_info['year'];

		// TODO: make "week starts on" configurable: Monday vs. Sunday
		$daysOfWeek = array();
		for( $i = 0; $i < 7; $i++ ) {
			$daysOfWeek[] = $this->dayOfWeek( $i );
		}

		// Build a two-dimensional array of UNIX timestamps.
		$calendar = array();

		// Start the first row with the final day( s ) of the previous month.
		$week = array();
		$month_begin = mktime( 0, 0, 0, $month, 1, $year );
		$month_begin_day_of_week = strftime( '%w', $month_begin );
		$days_in_prev_month = $this->daysInMonth( $prev_month, $prev_month_year );
		for( $day_of_week = 0; $day_of_week < $month_begin_day_of_week; $day_of_week++ ) {
			$day = $days_in_prev_month - $month_begin_day_of_week + $day_of_week;
			$week[] = mktime( 0, 0, 0, $month - 1, $day, $year );
		}

		// Fill in the days of the selected month.
		$days_in_month = $this->daysInMonth( $month, $year );
		for( $i = 1; $i <= $days_in_month; $i++ ) {
			if( $day_of_week == 7 ) {
				$calendar[] = $week;

				// re-initialize $day_of_week and $week
				$day_of_week = 0;
				unset( $week );
				$week = array();
			}
			$week[] = mktime( 0, 0, 0, $month, $i, $year );
			$day_of_week++;
		}

		// Fill out the last row with the first day( s ) of the next month.
		for( $i = 1; $day_of_week < 7; $i++, $day_of_week++ ) {
			$week[] = mktime( 0, 0, 0, $month + 1, $i, $year );
		}
		$calendar[] = $week;

		// Generate the URL for today, which will be null if $selected_date is
		// today.
		$today = getdate();
		$today_date = mktime( 0, 0, 0, $today['mon'], $today['mday'], $today['year'] );

		$navigation = array(
			'day' => strftime( '%d', mktime( 0, 0, 0, $month, $day, $year ) ),
			'month' => $month,
			'year' => $year,
			'month_name' => strftime( '%B', mktime( 0, 0, 0, $month, 1, $year ) ),
			'prev_month_end' => $prev_month_end,
			'prev_month_abbrev' => strftime( '%b', $prev_month_end ),
			'next_month_begin' => $next_month_begin,
			'next_month_abbrev' => strftime( '%b', $next_month_begin ),
			'days_of_week' => $daysOfWeek,
		);

		$ret['calendar'] = $calendar;
		$ret['navigation'] = $navigation;

		return $ret;
	}

	function isLeapYear( $year ) {
		return( ( $year % 4 == 0 && $year % 100 != 0 ) || $year % 400 == 0 );
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
				return isLeapYear( $year ) ? 29 : 28;

			default:
				assert( FALSE );
		}
	}

	/**
	 * @param int $pDayOfWeek Sunday is 0, Monday is 1, etc.
	 * @return string
	 */
	function dayOfWeek( $pDayOfWeek ) {
		// January 2, 2000 is an arbitrary Sunday that serves as the basis for
		// using strftime() to get the localized name (or abbreviation) of the
		// specified day of week.
		$day = 2 + $pDayOfWeek;
		$timestamp = mktime( 0, 0, 0, 1, $day, 2000 );

		return strftime( '%A', $timestamp );
	}

	// copied from index.php
	// untested( by me, anyway! ) function grabbed from the php.net site:
	// [2004/01/05:rpg]
	function m_weeks( $y, $m ){
		// monthday array
		$monthdays = array( 1=>31, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31 );
		// weekdays remaining in a week starting on 7 - sunday...( could be changed )
		$weekdays = array( 7=>7, 1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1 );
		$date = mktime( 0, 0, 0, $m, 1, $y );
		$leap = date( "l", $date );
		// if it is a leap year set february to 29 days, otherwise 28
		$monthdays[2] =( $leap ? 29 : 28 );
		// get the weekday of the first day of the month
		$wn = strftime( "%u",$date );
		$days = $monthdays[$m] - $weekdays[$wn];
		return( ceil( $days/7 )+1 );
	}
}

?>
