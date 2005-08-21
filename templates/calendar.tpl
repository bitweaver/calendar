{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.19 2005/08/21 12:38:05 squareing Exp $ *}
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

		{include file="bitpackage:calendar/calendar_nav_inc.tpl"}

		<table class="data caltable {$smarty.session.calendar.view_mode}">
			<caption>{tr}Selection: {$navigation.focus_date|bit_long_date}{/tr}</caption>
			{if $smarty.session.calendar.view_mode eq 'day'}
				<tr>
					<th style="width:15%;">{tr}Time{/tr}</th>
					<th>{tr}Events{/tr}</th>
				</tr>
				{foreach item=t from=$calDay}
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

				{foreach from=$calMonth key=week_num item=week}
					<tr style="height:6em;">
						<th>{$week_num}</th>
						{foreach from=$week item=day}
							{if $smarty.session.calendar.view_mode eq "month"}
								{if $day.day|date_format:"%m" eq $navigation.focus_month}
									{cycle values="odd,even" print=false advance=false}
								{else}
									{cycle values="notmonth" print=false advance=false}
								{/if}
							{else}
								{cycle values="odd,even" print=false advance=false}
							{/if}

							<td class="calday {cycle}" style="vertical-align:top;">
								{if $day.day|date_format:"%m" eq $navigation.focus_month or $smarty.session.calendar.view_mode eq "week"}
									{if $day.day eq $navigation.focus_date}<strong>{/if}
									<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?todate={$day.day}&amp;{$url_string}">{$day.day|date_format:"%d"}</a>
									{if $day.day eq $navigation.focus_date}</strong>{/if}
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
