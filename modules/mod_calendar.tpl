{strip}
{bitmodule title="$moduleTitle" name="calendar"}
	<table class="caltable">
		<caption>{$modCalNavigation.focus_date|date_format:"%B %Y"}</caption>
		<tr>
			<td style="text-align:left;">
				<a href="{$smarty.server.PHP_SELF}?todate={$modCalNavigation.before.year}&amp;{$url_string}" title="{$modCalNavigation.before.year|bit_long_date}">&laquo;{tr}y{/tr}</a>
			</td>
			<td style="text-align:left;">
				<a href="{$smarty.server.PHP_SELF}?todate={$modCalNavigation.before.month}&amp;{$url_string}" title="{$modCalNavigation.before.month|bit_long_date}">&laquo;{tr}m{/tr}</a><br />
			</td>

			<td colspan="3" style="text-align:center;">
				<a href="{$smarty.server.PHP_SELF}?todate={$smarty.now}&amp;{$url_string}" title="{$smarty.now|bit_long_date}">{tr}Today{/tr}</a>
			</td>

			<td style="text-align:right;">
				<a href="{$smarty.server.PHP_SELF}?todate={$modCalNavigation.after.month}&amp;{$url_string}" title="{$modCalNavigation.after.month|bit_long_date}">{tr}m{/tr}&raquo;</a><br />
			</td>
			<td style="text-align:right;">
				<a href="{$smarty.server.PHP_SELF}?todate={$modCalNavigation.after.year}&amp;{$url_string}" title="{$modCalNavigation.after.year|bit_long_date}">{tr}y{/tr}&raquo;</a>
			</td>
		</tr>

		<tr>
			{foreach from=$dayNames item=name}
				<th style="width:14%">{$name|truncate:"1":""}</th>
			{/foreach}
		</tr>

		{foreach from=$modCalMonth item=week}
			<tr>
				{foreach from=$week item=day}
					{if $day.day|date_format:"%m" eq $modCalNavigation.focus_month}
						{cycle values="odd,even" print=false advance=false}
					{else}
						{cycle values="notmonth" print=false advance=false}
					{/if}

					<td class="calday{if $day.day eq $navigation.today} highlight{/if} {cycle}">
						{if $day.day|date_format:"%m" eq $modCalNavigation.focus_month}
							{if $day.day eq $modCalNavigation.focus_date}<strong>{/if}
							<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$day.day}&amp;{$url_string}">{$day.day|date_format:"%d"}</a>
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
{/strip}
