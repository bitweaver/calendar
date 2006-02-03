<div class="navbar">
	<ul>
		{if $smarty.request.user_id}
			<li>{smartlink ititle="Show all" sort_mode=$smarty.request.sort_mode}</li>
		{else}
			<li>{smartlink ititle="Only my items" user_id=$gBitUser->mUserId sort_mode=$smarty.request.sort_mode}</li>
		{/if}
		<li>{smartlink ititle="Creation date" isort="created"}</li>
		<li>{smartlink ititle="Modification date" idefault=1 isort="last_modified"}</li>
	</ul>
</div>

<div class="clear"></div>
<br />

{if $gBitSystem->isFeatureActive( 'feature_jscalendar' )}
	<table>
		<tr>
			<td>
				<div>
					{form method="get" id="f"}
						<input type="hidden" id="sort_mode" name="sort_mode" value="{$smarty.request.sort_mode}" />
						<input type="hidden" id="todate" name="todate" value="{$navigation.focus_date|cal_date_format:"%B %e, %Y %H:%M %Z"}" />
						<span class="highlight" style="cursor:pointer;" title="{tr}Date Selector{/tr}" id="datrigger">{$navigation.focus_date|bit_long_date}</span>
						&nbsp;&nbsp;&nbsp;<small>&laquo;&nbsp;{tr}click to navigate{/tr}</small>
					{/form}
				</div>

				<script type="text/javascript">//<![CDATA[
					function gotocal() {ldelim}
						window.location = '{$gBitLoc.CALENDAR_PKG_URL}index.php?todate='+document.getElementById('todate').value;
					{rdelim}

					Calendar.setup( {ldelim}
						date			: "{$navigation.focus_date|bit_date_format:"%m/%d/%Y %H:%M"}",	// initial date
						inputField		: "todate",				// ID of the input field
						ifFormat		: "%s",					// the date format
						displayArea 	: "datrigger",			// ID of the span where the date is to be shown
						daFormat		: "{"%d/%m/%Y %H:%M"}",	// format of the displayed date
						electric		: false,
						onUpdate		: gotocal
					{rdelim} );
				//]]></script>
			</td>
{strip}
			<td style="white-space:nowrap; width:140px; text-align:right;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=weeklist&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'weeklist'}highlight{/if}">{biticon ipackage=calendar iname=weeklist iexplain=Weeklist}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
			</td>
{/strip}
		</tr>
	</table>
{else}
{strip}
	<table>
		<tr>
			<td rowspan="2" style="text-align:left;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.day}&amp;{$url_string}" title="{$navigation.before.day|bit_long_date}">&laquo; {tr}day{/tr}</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.week}&amp;{$url_string}" title="{$navigation.before.week|bit_long_date}">&laquo; {tr}week{/tr}</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.month}&amp;{$url_string}" title="{$navigation.before.month|bit_long_date}">&laquo; {tr}month{/tr}</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.year}&amp;{$url_string}" title="{$navigation.before.year|bit_long_date}">&laquo; {tr}year{/tr}</a>
			</td>

			<td style="text-align:center;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$smarty.now}&amp;{$url_string}" title="{$smarty.now|bit_long_date}">{tr}Today{/tr}: <strong>{$smarty.now|bit_long_date}</strong></a>
			</td>

			<td rowspan="2" style="text-align:right;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.day}&amp;{$url_string}" title="{$navigation.after.dayn|bit_long_date}">{tr}day{/tr} &raquo;</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.week}&amp;{$url_string}" title="{$navigation.after.week|bit_long_date}">{tr}week{/tr} &raquo;</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.month}&amp;{$url_string}" title="{$navigation.after.month|bit_long_date}">{tr}month{/tr} &raquo;</a><br />
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.year}&amp;{$url_string}" title="{$navigation.after.year|bit_long_date}">{tr}year{/tr} &raquo;</a>
			</td>
		</tr>

		<tr>
			<td style="text-align:center;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=weeklist&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'weeklist'}highlight{/if}">{biticon ipackage=calendar iname=weeklist iexplain=Weeklist}</a>
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
			</td>
		</tr>
	</table>
{/strip}
{/if}
