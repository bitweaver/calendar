<table class="calendar">
	<caption>{$month_name}&nbsp;{$year}</caption>
	<tr>
		<td style="text-align:left;" colspan="2">
			<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$prev_month_end}">
				&laquo; {$prev_month_abbrev}
			</a>
		</td>

		<td style="text-align:center;" colspan="3">
			<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$smarty.now}">{tr}Today{/tr}</a>
		</td>

		<td style="text-align:right;" colspan="2">
			<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$next_month_begin}">
				{$next_month_abbrev} &raquo;
			</a>
		</td>
	</tr>
	<tr>
		{section name="day_of_week" loop=$day_of_week_abbrevs}
			<th>{$day_of_week_abbrevs[day_of_week]}</th>
		{/section}
	</tr>
		{section name="row" loop=$calendar}
			<tr>
				{section name="col" loop=$calendar[row]}
					{assign var="date" value=$calendar[row][col]}
					{if $date == $selected_date}
						<td style="text-align:right;"><strong>{$date|date_format:"%e"}</strong></td>
					{elseif $date|date_format:"%m" == $month}
						<td style="text-align:right;">
							<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$date}">
								{$date|date_format:"%e"}
							</a>
						</td>
					{else}
						<td></td>
					{/if}
				{/section}
			</tr>
		{/section}
</table>
