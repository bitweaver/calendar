{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.16 2005/08/21 01:14:21 squareing Exp $ *}
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

		{* this is used to keep stuff like sort_mode persistent in all links on this page *}
		{assign var=url_string value="sort_mode=`$smarty.request.sort_mode`&amp;user_id=`$smarty.request.user_id`"}

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
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
					</td>
				</tr>
			</table>
		{else}
			<table>
				<tr>
					<td rowspan="2" style="text-align:left;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$daybefore}&amp;{$url_string}" title="{$daybefore|bit_long_date}">&laquo; {tr}day{/tr}</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekbefore}&amp;{$url_string}" title="{$weekbefore|bit_long_date}">&laquo; {tr}week{/tr}</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthbefore}&amp;{$url_string}" title="{$monthbefore|bit_long_date}">&laquo; {tr}month{/tr}</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$yearbefore}&amp;{$url_string}" title="{$yearbefore|bit_long_date}">&laquo; {tr}year{/tr}</a>
					</td>

					<td style="text-align:center;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$smarty.now}&amp;{$url_string}" title="{$smarty.now|bit_long_date}">{tr}Today{/tr}: <strong>{$smarty.now|bit_long_date}</strong></a>
					</td>

					<td rowspan="2" style="text-align:right;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$dayafter}&amp;{$url_string}" title="{$dayafter|bit_long_date}">{tr}day{/tr} &raquo;</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekafter}&amp;{$url_string}" title="{$weekafter|bit_long_date}">{tr}week{/tr} &raquo;</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthafter}&amp;{$url_string}" title="{$monthafter|bit_long_date}">{tr}month{/tr} &raquo;</a><br />
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$yearafter}&amp;{$url_string}" title="{$yearafter|bit_long_date}">{tr}year{/tr} &raquo;</a>
					</td>
				</tr>

				<tr>
					<td style="text-align:center;">
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=day&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'day'}highlight{/if}">{biticon ipackage=calendar iname=day iexplain=Day}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=week&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'week'}highlight{/if}">{biticon ipackage=calendar iname=week iexplain=Week}</a>
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view_mode=month&amp;{$url_string}" class="{if $smarty.session.calendar.view_mode eq 'month'}highlight{/if}">{biticon ipackage=calendar iname=month iexplain=Month}</a>
					</td>
				</tr>
			</table>
		{/if}

		<table class="data {$smarty.session.calendar.view_mode}">
			<caption>{tr}Selection: {$focusdate|bit_long_date}{/tr}</caption>
			{if $smarty.session.calendar.view_mode eq 'day'}
				<tr>
					<th style="width:15%;">{tr}Time{/tr}</th>
					<th>{tr}Events{/tr}</th>
				</tr>
				{foreach item=t from=$dayTime}
					<tr class="{cycle values="odd,even"}">
						<td style="text-align:right; vertical-align:top; padding-right:15px;">{$t.time|date_format:"%R"}</td>
						<td>
							{foreach from=$t.items item=item}
								{assign var=over value=$item.over}
								<div class="cal cal{$item.content_type_guid}">
									{$item.last_modified|date_format:"%R"}: &nbsp;
									<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$item.content_id}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
										 {$item.title|default:"..."}
									</a>
								</div>
							{/foreach}
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<th style="width:2%;"></th>
					{foreach from=$dayNames item=name}
						<th width="14%">{$name}</th>
					{/foreach}
				</tr>

				{foreach from=$calendar key=week_num item=week}
					<tr style="height:6em;">
						<th>{$week_num}</th>
						{foreach from=$week item=day}
							{if $smarty.session.calendar.view_mode eq "month"}
								{if $day.day|date_format:"%m" eq $focusmonth}
									{cycle values="odd,even" print=false advance=false}
								{else}
									{cycle values="notmonth" print=false advance=false}
								{/if}
							{else}
								{cycle values="odd,even" print=false advance=false}
							{/if}

							<td class="calday {cycle}" style="vertical-align:top;">
								{if $day.day|date_format:"%m" eq $focusmonth or $smarty.session.calendar.view_mode eq "week"}
									{if $day.day eq $focusdate}<strong>{/if}
									<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$day.day}&amp;{$url_string}">{$day.day|date_format:"%d"}</a>
									{if $day.day eq $focusdate}</strong>{/if}
									<hr />

									{* - Calendar Content - *}
									{foreach from=$day.items item=item}
										{assign var=over value=$item.over}
										<div class="cal{$item.content_type_guid}">
											<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$item.content_id}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
												{$item.title|truncate:$trunc:"..."|default:"?"}
											</a>
										</div>
									{/foreach}
								{else}
									&nbsp;
								{/if}
							</td>
						{/foreach}
					</tr>
				{/foreach}
			{/if}
		</table>
	</div><!-- end .body -->
</div><!-- end .calendar -->
{/strip}
