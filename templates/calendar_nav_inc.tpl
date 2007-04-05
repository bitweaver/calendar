{strip}
<div class="navbar">
	<ul>
		{if $smarty.request.user_id}
			<li>{smartlink ititle="Show all" sort_mode=$smarty.request.sort_mode}</li>
		{else}
			<li>{smartlink ititle="Only my items" user_id=$gBitUser->mUserId sort_mode=$smarty.request.sort_mode}</li>
		{/if}
		{if $gBitUser->hasPermission('p_calendar_view_changes')}
		<li>{smartlink ititle="Creation date" isort="created"}</li>
		<li>{smartlink ititle="Modification date" isort="last_modified"}</li>
		<li>{smartlink ititle="Event time" idefault=1 isort="event_time"}</li>
		{/if}
	</ul>
</div>

<div class="clear"></div>
<br />

{if $gBitSystem->isFeatureActive( 'site_use_jscalendar' )}
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

				<script type="text/javascript">/* <![CDATA[ */
					function gotocal() {ldelim}
						document.getElementById('f').submit();
					{rdelim}
				/* ]]> */</script>

				{jscalendar inputField=todate time=$navigation.focus_date onUpdate=gotocal displayArea=datrigger daFormat=$gBitSystem->getConfig('site_long_date_format')}
			</td>
			<td style="white-space:nowrap; width:140px; text-align:right;">
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=weeklist&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'weeklist'}highlight{/if}">{biticon ipackage=calendar iname=weeklist iexplain=Weeklist}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
			</td>
		</tr>
	</table>
{else}
	<table>
		<tr>
			<td rowspan="2" style="text-align:left;">
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.before.day}&amp;{$url_string}" title="{$navigation.before.day|bit_long_date}">&laquo; {tr}day{/tr}</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.before.week}&amp;{$url_string}" title="{$navigation.before.week|bit_long_date}">&laquo; {tr}week{/tr}</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.before.month}&amp;{$url_string}" title="{$navigation.before.month|bit_long_date}">&laquo; {tr}month{/tr}</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.before.year}&amp;{$url_string}" title="{$navigation.before.year|bit_long_date}">&laquo; {tr}year{/tr}</a>
			</td>

			<td style="text-align:center;">
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$smarty.now}&amp;{$url_string}" title="{$navigation.today|bit_long_date}">{tr}Today{/tr}: <strong>{$navigation.today|bit_long_date} {$navigation.tz_flag}</strong></a>
			</td>

			<td rowspan="2" style="text-align:right;">
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.after.day}&amp;{$url_string}" title="{$navigation.after.day|bit_long_date}">{tr}day{/tr} &raquo;</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.after.week}&amp;{$url_string}" title="{$navigation.after.week|bit_long_date}">{tr}week{/tr} &raquo;</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.after.month}&amp;{$url_string}" title="{$navigation.after.month|bit_long_date}">{tr}month{/tr} &raquo;</a><br />
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$navigation.after.year}&amp;{$url_string}" title="{$navigation.after.year|bit_long_date}">{tr}year{/tr} &raquo;</a>
			</td>
		</tr>

		<tr>
			<td style="text-align:center;">
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=weeklist&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'weeklist'}highlight{/if}">{biticon ipackage=calendar iname=weeklist iexplain=Weeklist}</a>
				<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
			</td>
		</tr>
	</table>
{/if}
{/strip}
