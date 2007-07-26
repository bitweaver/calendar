{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.48 2007/07/26 12:51:26 lsces Exp $ *}
{strip}
{if !$gBitSystem->isFeatureActive( 'site_help_popup' )}
	{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
{/if}

<div class="display calendar">
	<div class="header">
		<h1>{tr}Calendar{/tr}</h1>
	</div>
	<div class="body">
		{jstabs}
			{jstab title="Calendar"}
				{* this is used to keep stuff like sort_mode persistent in all links on this page *}
				{assign var=url_string value="sort_mode=`$smarty.request.sort_mode`&amp;user_id=`$smarty.request.user_id`"}
				{include file="bitpackage:calendar/calendar_nav_inc.tpl"}

				<table class="data caltable {$smarty.session.calendar.view_mode}">
					<caption>{tr}Selection: {$navigation.focus_date|cal_date_format:"%A %d of %B, %Y %Z"}{/tr}</caption>
					{if $smarty.session.calendar.view_mode eq 'day'}
						<tr>
							<th style="width:15%;">{tr}Time{/tr}</th>
							<th>{tr}Events{/tr}</th>
						</tr>
						{foreach item=time from=$calDay}
							<tr class="{cycle values="odd,even"}">
								<th>{$time.time|cal_date_format:"%H:%M"}</th>
								<td class="calitems">
									{foreach from=$time.items item=item}
										{assign var=over value=$item.over}
										<div class="cal{$item.content_type_guid}">
	<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$item.content_id}">
		{capture assign=itemurl}{$smarty.const.CALENDAR_PKG_URL}box.php?content_id={$item.content_id}{/capture}
		<img style="padding:0px 4px;" src="{biticon ipackage="icons" iname="list-add" iexplain="Detailed Information" url=true}" title="{tr}Detailed Information{/tr}" {if $gBitSystem->isFeatureActive('calendar_ajax_popups')}{popup fullhtml=1 sticky=1 closeclick=1 target=$itemurl}{else}{popup fullhtml=1 text=$over|escape:"javascript"|escape:"html"}{/if} /> {$item.title|escape|default:"?"}
	</a>
										</div>
									{/foreach}
								</td>
							</tr>
						{/foreach}
					{elseif $smarty.session.calendar.view_mode eq 'weeklist'}
						{foreach from=$calMonth item=week}
							{counter assign=weekday print=false start=0}
							{foreach from=$week item=day}
								<tr>
									<th style="width:10%">
										<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=day&amp;todate={$day.day}&amp;{$url_string}">
											{$dayNames.$weekday} - {$day.day|cal_date_format:"%d"}
										</a>
										{counter assign=weekday print=false}
									</th>
								</tr>
								<tr>
									<td class="calitems {if $day.day eq $navigation.display_focus_date} current{/if}{if $day.day eq $navigation.today} highlight{/if} {cycle values="odd,even"}">
										{if $day.day|cal_date_format:"%m" eq $navigation.focus_month or $smarty.session.calendar.view_mode eq "week"}
											{foreach from=$day.items item=item}
												{assign var=over value=$item.over}
		{capture assign=itemurl}{$smarty.const.CALENDAR_PKG_URL}box.php?content_id={$item.content_id}{/capture}
												<div class="cal{$item.content_type_guid}" style="float:left;width:50%;">
													<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$item.content_id}">
				<img style="padding:0px 4px;" src="{biticon ipackage="icons" iname="list-add" iexplain="Detailed Information" url=true}" title="{tr}Detailed Information{/tr}" {if $gBitSystem->isFeatureActive('calendar_ajax_popups')}{popup fullhtml=1 sticky=1 closeclick=1 target=$itemurl}{else}{popup fullhtml=1 text=$over|escape:"javascript"|escape:"html"}{/if} /> {$item.title|escape|default:"?"}
			</a>
												</div>
											{/foreach}
										{else}
											&nbsp;
										{/if}
									</td>
								</tr>
							{/foreach}
						{/foreach}
					{else}
						<tr>
							<th style="width:2%;"></th>
							{foreach from=$dayNames item=dayName}
								<th style="width:14%">{$dayName}</th>
							{/foreach}
						</tr>

						{foreach from=$calMonth key=week_num item=week}
							<tr>
								<th><a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=week&amp;todate={$week.6.day}">{$week_num}</a></th>
								{foreach from=$week item=day}
									{if $smarty.session.calendar.view_mode eq "month"}
										{if $day.day|cal_date_format:"%m" eq $navigation.focus_month}
											{cycle values="odd,even" print=false advance=false}
										{else}
											{cycle values="notmonth" print=false advance=false}
										{/if}
									{else}
										{cycle values="odd,even" print=false advance=false}
									{/if}

									<td class="calitems {if $day.day eq $navigation.display_focus_date} current{/if}{if $day.day eq $navigation.today} highlight{/if} {cycle values="odd,even"}">
										{if $day.day|cal_date_format:"%m" eq $navigation.focus_month or $smarty.session.calendar.view_mode eq "week"}
											<div class="calnumber">
												<a href="{$smarty.const.CALENDAR_PKG_URL}index.php?view_mode=day&amp;todate={$day.day}&amp;{$url_string}">{$day.day|cal_date_format:"%d"}</a>
											</div>

											{* - Cell Content - *}
											{foreach from=$day.items item=item}
												{assign var=over value=$item.over}
		{capture assign=itemurl}{$smarty.const.CALENDAR_PKG_URL}box.php?content_id={$item.content_id}{/capture}
												<div class="cal{$item.content_type_guid}">
													<a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$item.content_id}">
				<img style="padding:0px 4px;" src="{biticon ipackage="icons" iname="list-add" iexplain="Detailed Information" url=true}" title="{tr}Detailed Information{/tr}" {if $gBitSystem->isFeatureActive('calendar_ajax_popups')}{popup fullhtml=1 target=$itemurl sticky=1 closeclick=1}{else}{popup fullhtml=1 text=$over|escape:"javascript"|escape:"html"}{/if} /> {$item.title|escape|truncate:$trunc:"..."|default:"?"}
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
			{/jstab}
			{if $gBitUser->hasPermission('p_calendar_view')}
				{jstab title="Display Options"}
					{include file="bitpackage:calendar/calendar_options_inc.tpl"}
				{/jstab}
			{/if}
		{/jstabs}
	</div><!-- end .body -->
</div><!-- end .calendar -->
{/strip}
