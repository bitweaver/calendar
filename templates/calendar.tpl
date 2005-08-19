{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.10 2005/08/19 11:44:54 squareing Exp $ *}
{strip}

{if !$gBitSystem->isFeatureActive( 'feature_helppopup' )}
	{popup_init src="`$smarty.const.THEMES_PKG_URL`js/overlib.js"}
{/if}

<div class="display calendar">
	<div class="header">
		<h1>{tr}Calendar{/tr}</h1>
	</div>

	<div class="body">
		{include file="bitpackage:calendar/calendar_options_inc.tpl"}

		{if $gBitSystemPrefs.feature_jscalendar eq 'y'}
			<table>
				<tr>
					<td>
						<form action="{$gBitLoc.CALENDAR_PKG_URL}index.php" method="get" id="f">
							<input type="hidden" id="todate" name="todate" value="{$focusdate|date_format:"%B %e, %Y %H:%M"}" />
							<span title="{tr}Date Selector{/tr}" id="datrigger">{$focusdate|bit_long_date}</span>
							&lt;- {tr}click to navigate{/tr}
						</form>

						<script type="text/javascript">
							function gotocal() {ldelim}
								window.location = '{$gBitLoc.CALENDAR_PKG_URL}index.php?todate='+document.getElementById('todate').value;
							{rdelim}

							Calendar.setup( {ldelim}
								date			: "{$focusdate|date_format:"%m/%d/%Y %H:%M"}",			// initial date
								inputField		: "todate",												// ID of the input field
								ifFormat		: "%s",													// the date format
								displayArea 	: "datrigger",											// ID of the span where the date is to be shown
								daFormat		: "{"%d/%m/%Y %H:%M"}",									// format of the displayed date
								electric		: false,
								onUpdate		: gotocal
							{rdelim} );
						</script>
					</td>

					<td nowrap="nowrap" width="120" align="right">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
					</td>
				</tr>
			</table>
		{else}
			<table>
				<tr>
					<td rowspan="2" style="text-align:left;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$daybefore}" title="{$daybefore|bit_long_date}">&laquo; {tr}day{/tr}</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekbefore}" title="{$weekbefore|bit_long_date}">&laquo; {tr}week{/tr}</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthbefore}" title="{$monthbefore|bit_long_date}">&laquo; {tr}month{/tr}</a>
					</td>

					<td style="text-align:center;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$now}" title="{$nowt|bit_short_date}">{tr}Today{/tr}: <strong>{$now|bit_short_date}</strong></a>
					</td>

					<td rowspan="2" style="text-align:right;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$dayafter}" title="{$dayafter|bit_long_date}">{tr}day{/tr} &raquo;</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekafter}" title="{$weekafter|bit_long_date}">{tr}week{/tr} &raquo;</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthafter}" title="{$monthafter|bit_long_date}">{tr}month{/tr} &raquo;</a>
					</td>
				</tr>

				<tr>
					<td style="text-align:center;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
					</td>
				</tr>
			</table>
		{/if}

	<table class="data">
		<caption>{tr}Selection: {$focusdate|bit_long_date}{/tr}</caption>
			{if $smarty.session.calendar.view_mode eq 'day'}
				<tr>
					<th style="width:15%;">{tr}Hours{/tr}</th>
					<th>{tr}Events{/tr}</th>
				</tr>
				{cycle values="odd,even" print=false}
				{section name=h loop=$hours}
					<tr class="{cycle}">
						<td style="text-align:right; vertical-align:top; padding-right:15px;">{$hours[h]}</td>
						<td>
							{section name=hr loop=$hrows[h]}
								{assign var=over value=$hrows[h][hr].over}
								<div class="cal{$hrows[h][hr].content_type_guid}">
									{$hrows[h][hr].last_modified|date_format:"%H:%M"}:

									&nbsp; <a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$cell[w][d].items[items].content_id}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
										{$hrows[h][hr].title|default:"..."}
									</a>

									{if $hrows[h][hr].web}
										<a href="{$hrows[h][hr].web}" title="{$hrows[h][hr].web}">w</a>
									{/if}
{* - Omit description for moment - need to strip <CR> $hrows[h][hr].description *}
								</div>
							{/section}
						</td>
					</tr>
				{/section}
			{else}
				<tr>
					<th style="width:2%;"></th>
					{section name=dn loop=$daysnames}
						<th width="14%">{$daysnames[dn]}</th>
					{/section}
				</tr>

				{section name=w loop=$cell}
					<tr style="height:6em;">
						<th>{$weeks[w]}</th>

						{section name=d loop=$weekdays}
							{if $smarty.session.calendar.view_mode eq "month"}
								{if $cell[w][d].day|date_format:"%m" eq $focusmonth}
									{cycle values="odd,even" print=false advance=false}
								{else}
									{cycle values="notmonth" print=false advance=false}
								{/if}
							{else}
								{cycle values="odd,even" print=false advance=false}
							{/if}

							<td class="calday {cycle}" style="vertical-align:top;">
								{if $cell[w][d].day|date_format:"%m" eq $focusmonth or $smarty.session.calendar.view_mode eq "week"}
									{if $cell[w][d].day eq $focusdate}<strong>{/if}
										<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$cell[w][d].day}">{$cell[w][d].day|date_format:"%d/%m"}</a>
									{if $cell[w][d].day eq $focusdate}</strong>{/if}
									<hr />

									{* - Calendar Content - *}
									{section name=items loop=$cell[w][d].items}
										{assign var=over value=$cell[w][d].items[items].over}
										<div class="cal{$cell[w][d].items[items].content_type_guid}">

											<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$cell[w][d].items[items].content_id}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
												{$cell[w][d].items[items].title|truncate:$trunc:"..."|default:"?"}
											</a>

											{if $cell[w][d].items[items].web}
												<a href="{$cell[w][d].items[items].web}" title="{$cell[w][d].items[items].web}">w</a>
											{/if}
											<br />
										</div>
									{/section}
								{else}
									&nbsp;
								{/if}
							</td>
						{/section}
					</tr>
				{/section}
			{/if}
		</table>
	</div><!-- end .body -->
</div><!-- end .calendar -->
{/strip}
