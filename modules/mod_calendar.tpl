{bitmodule title="$moduleTitle" name="calendar"}
	<table>
		<caption>{$modCalNavigation.focus_date|date_format:"%B"}</caption>
		<tr>
			<td style="text-align:left;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.month}&amp;{$url_string}" title="{$navigation.before.month|bit_long_date}">&laquo;{tr}m{/tr}</a><br />
			</td>
			<td style="text-align:left;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.before.year}&amp;{$url_string}" title="{$navigation.before.year|bit_long_date}">&laquo;{tr}y{/tr}</a>
			</td>

			<td colspan="3" style="text-align:center;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$smarty.now}&amp;{$url_string}" title="{$smarty.now|bit_long_date}">{tr}Today{/tr}</a>
			</td>

			<td style="text-align:right;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.month}&amp;{$url_string}" title="{$navigation.after.month|bit_long_date}">{tr}m{/tr}&raquo;</a><br />
			</td>
			<td style="text-align:right;">
				<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$navigation.after.year}&amp;{$url_string}" title="{$navigation.after.year|bit_long_date}">{tr}y{/tr}&raquo;</a>
			</td>
		</tr>

		<tr>
			{foreach from=$dayNames item=name}
				<th width="14%">{$name}</th>
			{/foreach}
		</tr>

		{foreach from=$modCalMonth key=week_num item=week}
			<tr>
				{foreach from=$week item=day}
					{if $smarty.session.calendar.view_mode eq "month"}
						{if $day.day|date_format:"%m" eq $modCalNavigation.focus_month}
							{cycle values="odd,even" print=false advance=false}
						{else}
							{cycle values="notmonth" print=false advance=false}
						{/if}
					{else}
						{cycle values="odd,even" print=false advance=false}
					{/if}

					<td class="{cycle}" style="text-align:right;">
						{if $day.day|date_format:"%m" eq $modCalNavigation.focus_month or $smarty.session.calendar.view_mode eq "week"}
							{if $day.day eq $modCalNavigation.focus_date}<strong>{/if}
							<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$day.day}&amp;{$url_string}">{$day.day|date_format:"%d"}</a>
							{if $day.day eq $modCalNavigation.focus_date}</strong>{/if}
						{else}
							&nbsp;
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</table>
{/bitmodule}
