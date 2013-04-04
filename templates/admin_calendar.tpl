{strip}
{if !$userPrefs}
	{assign var=settings value=$gBitSystem->mConfig}
{else}
	{assign var=settings value=$userPrefs}
{/if}

{form legend="Calendar Settings"}
	<input type="hidden" name="view_user" value="{$view_user}" />
	<input type="hidden" name="page" value="{$page}" />
	<div class="control-group">
		{formlabel label="First day of Week" for="calendar_week_offset"}
		{forminput}
			{html_options name=calendar_week_offset output=$firstDayOutput values=$firstDayValues selected=`$settings.calendar_week_offset` id=calendar_week_offset}
			{formhelp note="Set the first day of the week."}
		{/forminput}
	</div>

	<div class="control-group">
		{formlabel label="Time Blocks" for="calendar_hour_fraction"}
		{forminput}
			{html_options name=calendar_hour_fraction output=$hourOutput values=$hourValues selected=`$settings.calendar_hour_fraction` id=calendar_hour_fraction} {tr}minutes{/tr}
			{formhelp note="Set the timeblocks for the day view."}
		{/forminput}
	</div>

	<div class="control-group">
		{formlabel label="Day View" for="calendar_day_start"}
		{forminput}
			{tr}from{/tr} &nbsp;
			{html_options name=calendar_day_start output=$dayStart values=$dayStart selected=`$settings.calendar_day_start` id=calendar_day_start}
			&nbsp; {tr}to{/tr} &nbsp;
			{html_options name=calendar_day_end output=$dayEnd values=$dayEnd selected=`$settings.calendar_day_end` id=calendar_day_start}
			{formhelp note="Pick the start and end time of your day view."}
		{/forminput}
	</div>

	{if $gBitUser->isAdmin() and $page eq "calendar"}
		<div class="control-group">
			{formlabel label="Individual Calendar Settings" for="calendar_user_prefs"}
			{forminput}
				{html_checkboxes name="calendar_user_prefs" values="y" checked=`$settings.calendar_user_prefs` labels=false id=calendar_user_prefs}
				{formhelp note="Allow users to set their own calendar preferences."}
			{/forminput}
		</div>

		<div class="control-group">
			{formlabel label="Ajax Popups" for="calendar_ajax_popups"}
			{forminput}
				{html_checkboxes name="calendar_ajax_popups" values="y" checked=`$settings.calendar_ajax_popups` labels=false id=calendar_ajax_popups}
				{formhelp note="Use ajax for calendar popups. This saves page load time and bandwidth at the expense of requiring javascript."}
			{/forminput}
		</div>

		<div class="control-group">
			{formlabel label="Default Content Types"}
			{forminput}
				{html_checkboxes name="defaultTypes" options=$calendarTypeDefaults selected=$calendarTypesSelected separator="<br />"}
				{formhelp note="Default content types to show on the calendar when users do not have permission to change types of content viewed."}
			{/forminput}
		</div>
	{/if}
	
	<div class="control-group submit">
		<input type="submit" class="btn" name="calendar_submit" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
