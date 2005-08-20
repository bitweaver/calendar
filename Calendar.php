<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.6 2005/08/20 23:46:32 squareing Exp $
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

	function doDateCalculations( $pDateHash ) {
		$d = 60 * 60 * 24;
		$currentweek = date( "W", $pDateHash['focus_date'] );
		$wd = date( 'w', $pDateHash['focus_date'] );

		$year  = date( 'Y', $pDateHash['focus_date'] );
		$month = date( 'm', $pDateHash['focus_date'] );
		$day   = date( 'd', $pDateHash['focus_date'] );

		if( $pDateHash['view_mode'] == 'month' ) {
			$viewstart = mktime( 0, 0, 0, $month,     1, $year );
			// this is the last day of $month
			$viewend   = mktime( 0, 0, 0, $month + 1, 0, $year );
			// move viewstart back to Sunday....
			$viewstart -= date( "w", $viewstart ) * $d;
			$viewend += ( 6 - date( "w", $viewend ) ) * $d -1;

			// ISO weeks --- kinda mangled because ours begin on Sunday...
			$firstweek = date( "W", $viewstart + $d );
			$lastweek  = date( "W", $viewend );
			if( $lastweek < $firstweek ) {
				if( $currentweek < $firstweek ) {
					$firstweek -= 52;
				} else {
					$lastweek += 52;
				}
			}
			$numberofweeks = $lastweek - $firstweek;
		} elseif( $pDateHash['view_mode'] == 'week') {
			$firstweek = $currentweek;
			$lastweek = $currentweek;
			// start by putting $viewstart at midnight starting focusdate
			$viewstart = mktime( 0, 0, 0, $month, $day, $year);
			// then back up to the preceding Sunday;
			$viewstart -= $wd * $d;
			// then go to the end of the week for $viewend
			$viewend = $viewstart + ( ( 7 * $d ) - 1 );
			$numberofweeks = 0;
		} else {
			$firstweek = $currentweek;
			$lastweek = $currentweek;
			$viewstart = mktime( 0, 0, 0, $month, $day, $year);
			$viewend = $viewstart + ( $d - 1 );
			$weekdays = array( date( 'w', $pDateHash['focus_date'] ) );
			$numberofweeks = 0;
		}

		$ret = array(
			'first_week' => $firstweek,
			'last_week' => $lastweek,
			'view_start' => $viewstart,
			'view_end' => $viewend,
			'number_of_weeks' => $numberofweeks,
		);

		return $ret;
	}

	function prepGetList( &$pListHash ) {
		if( !empty( $pListHash['focus_date'] ) ) {
			$calDates = $this->doDateCalculations( $pListHash );
			$pListHash['start'] = $calDates['view_start'];
			$pListHash['stop'] = $calDates['view_end'];
		}

		if( !empty( $pListHash['sort_mode'] ) ) {
			$pListHash['calendar_sort_mode'] = preg_replace( "/(_asc$|_desc$)/i", "", $pListHash['sort_mode'] );
		}

		return TRUE;
	}

	// Build a two-dimensional array of UNIX timestamps.
	function buildCalendar( $pDateHash ) {
		global $gBitSmarty;

		$year  = date( 'Y', $pDateHash['focus_date'] );
		$month = date( 'm', $pDateHash['focus_date'] );
		$day   = date( 'd', $pDateHash['focus_date'] );

		// set week offset - start with a day other than monday
		$week_offset = 1;

		$prev_month_end	  = mktime( 0, 0, 0, $month,     0, $year );
		$next_month_begin = mktime( 0, 0, 0, $month + 1, 1, $year );

		//$prev_month_end = mktime( 0, 0, 0, $month - 1, $day, $year );
		//$next_month_begin = mktime( 0, 0, 0, $month + 1, $day, $year );

		$prev_month_end_info = getdate( $prev_month_end );
		$prev_month = $prev_month_end_info['mon'];
		$prev_month_year = $prev_month_end_info['year'];

		// Build a two-dimensional array of UNIX timestamps.
		$cal = array();

		// Start the first row with the final day( s ) of the previous month.
		$week = array();
		$month_begin = mktime( 0, 0, 0, $month, 1, $year );
		$month_begin_day_of_week = strftime( '%w', $month_begin );
		$days_in_prev_month = $this->daysInMonth( $prev_month, $prev_month_year );
		for( $day_of_week = 0; $day_of_week < $month_begin_day_of_week; $day_of_week++ ) {
			$_day = $days_in_prev_month - $month_begin_day_of_week + $day_of_week;
			$week[]['day'] = mktime( 0, 0, 0, $month - 1, $_day, $year );
		}

		// Fill in the days of the selected month.
		$days_in_month = $this->daysInMonth( $month, $year );
		for( $i = 1; $i <= $days_in_month; $i++ ) {
			if( $day_of_week == 7 ) {
				$cal[] = $week;

				// re-initialize $day_of_week and $week
				$day_of_week = 0;
				unset( $week );
				$week = array();
			}
			$week[]['day'] = mktime( 0, 0, 0, $month, $i, $year );
			$day_of_week++;
		}

		// Fill out the last row with the first day( s ) of the next month.
		for( $i = 1; $day_of_week < 7; $i++, $day_of_week++ ) {
			$week[]['day'] = mktime( 0, 0, 0, $month + 1, $i, $year );
		}
		$cal[] = $week;

		// apply weeknumber to calendar array
		foreach( $cal as $week ) {
			$calendar[date( 'W', $week[$week_offset]['day'] )] = $week;
		}

		// this wieek number has to be calculated, since the cal starts with sunday
		$week_num = date( 'W', mktime( 0, 0, 0, $month, $day + $week_offset, $year ) );
		// if we only want to see a weeks / days worth of data, nuke all xs data
		if( $pDateHash['view_mode'] == 'week' ) {
			$cal = $calendar[$week_num];
			$calendar = array();
			$calendar[$week_num] = $cal;
		} elseif( $pDateHash['view_mode'] == 'day' ) {
			$calendar = array();
			$calendar[$week_num][]['day'] = $pDateHash['focus_date'];
		}

		// Generate the URL for today, which will be null if $selected_date is
		// today.
		$today = getdate();
		$today_date = mktime( 0, 0, 0, $today['mon'], $today['mday'], $today['year'] );

		return $calendar;
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
}
?>
