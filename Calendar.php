<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_calendar/Calendar.php,v 1.8 2005/08/21 09:54:50 squareing Exp $
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
	* get a full list of content for a given time period
	* return array of items
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

	/**
	* calculate the start and stop time for the current display page
	**/
	function doDateCalculations( $pDateHash ) {
		global $gBitSystem;
		$year  = date( 'Y', $pDateHash['focus_date'] );
		$month = date( 'm', $pDateHash['focus_date'] );
		$day   = date( 'd', $pDateHash['focus_date'] );

		if( $pDateHash['view_mode'] == 'month' ) {
			$view_start = mktime( 0, 0, 0, $month,     1, $year );
			$view_end   = mktime( 0, 0, 0, $month + 1, 1, $year ) - 1;
		} elseif( $pDateHash['view_mode'] == 'week') {
			$wd  = date( 'w', $pDateHash['focus_date'] );
			$wd += $gBitSystem->getPreference( 'week_offset', 4 );
			// if we are moving out from the selected week, move us back in
			if( $wd > 7 ) {
				$wd -= 7;
			}

			// for some very odd reason, which i can't work out, we need to add a day here
			$view_start = mktime( 0, 0, 0, $month, $day - $wd + 1 , $year );
			$view_end   = mktime( 0, 0, 0, $month, $day - $wd + 8, $year ) - 1;
		} else {
			$view_start = mktime( 0, 0, 0, $month, $day    , $year );
			$view_end   = mktime( 0, 0, 0, $month, $day + 1, $year ) - 1;
		}

//		vd( 'start: '.strftime( '%d %m %Y, %T', $view_start ) );
//		vd( 'end: '.  strftime( '%d %m %Y, %T', $view_end   ) );

		$ret = array(
			'view_start' => $view_start,
			'view_end' => $view_end,
		);

		return $ret;
	}

	/**
	* prepare ListHash to ensure errorfree usage
	**/
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

	/**
	* build a two dimensional array of unix timestamps
	**/
	function buildCalendar( $pDateHash ) {
		global $gBitSmarty, $gBitSystem;

		$year  = date( 'Y', $pDateHash['focus_date'] );
		$month = date( 'm', $pDateHash['focus_date'] );
		$day   = date( 'd', $pDateHash['focus_date'] );

		// set week offset - start with a day other than monday
		$week_offset = $gBitSystem->getPreference( 'week_offset', 4 );

		$prev_month_end	  = mktime( 0, 0, 0, $month,     0, $year );
		$next_month_begin = mktime( 0, 0, 0, $month + 1, 1, $year );

		$prev_month_end_info = getdate( $prev_month_end );
		$prev_month = $prev_month_end_info['mon'];
		$prev_month_year = $prev_month_end_info['year'];

		// Build a two-dimensional array of UNIX timestamps.
		$cal = array();

		// Start the first row with the final day( s ) of the previous month.
		$week = array();
		$month_begin = mktime( 0, 0, 0, $month, $week_offset, $year );
		$month_begin_day_of_week = strftime( '%w', $month_begin );
		$days_in_prev_month = $this->daysInMonth( $prev_month, $prev_month_year );

		// Fill out the first row with the last day( s ) of the previous month.
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

		// this week number has to be calculated, since the cal start can be configured
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
