{strip}
{assign var=calendar_url value="`$smarty.const.CALENDAR_PKG_URL`index.php?todate="}
{assign var=calendar_day_url value="`$smarty.const.CALENDAR_PKG_URL`index.php?view_mode=day&amp;todate="}
<table class="minical" border="0" cellpadding="1" cellspacing="1">
	<tr>
		<th class="month odd" colspan="7">
    			{$month_name}&nbsp;{$year}
		</th>
	</tr>
	<tr>
		<td colspan="3" class="last">
			<a href="{$calendar_url}{$last_month}"> &lt;&nbsp;&lt; </a>
		</td>
		<td colspan="4" class="next">
			<a href="{$calendar_url}{$next_month}"> &gt;&nbsp;&gt; </a>
		</td>
	</tr>
	<tr>
		{section name="dow" loop=$dow_abbrevs}
			<th class="dow">{$dow_abbrevs[dow]}</th>
		{/section}
	</tr>
	{section name="row" loop=$calendar}
		<tr>
			{section name="col" loop=$calendar[row]}
			        {assign var="date" value=$calendar[row][col]}
					<td class="day {cycle name=daycolor values="even,odd"}{if $date.today} selected{/if}{if $date.dim} dim{/if}">
						<a href="{$calendar_day_url}{$date.time}">
							{$date.time|date_format:"%e"}
						</a>
					</td>
			{/section}
		</tr>
	{/section}
	<tr>
		<td class="today odd" colspan="7">
			<a href="{$calendar_url}{$today}">Today</a>
		</td>
	</tr>
</table>
{/strip}
