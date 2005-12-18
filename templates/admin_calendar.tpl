{strip}
{if !$settings}
	{assign var=settings value=$gBitSystemPrefs}
{/if}

{form legend="Calendar Settings"}
	<input type="hidden" name="page" value="{$page}" />
	<div class="row">
		{formlabel label="First day of Week" for="calendar_week_offset"}
		{forminput}
			{html_options name=calendar_week_offset output=$firstDayOutput values=$firstDayValues selected=`$settings.calendar_week_offset` id=calendar_week_offset}
			{formhelp note="Set the first day of the week."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Time Blocks" for="calendar_hour_fraction"}
		{forminput}
			{html_options name=calendar_hour_fraction output=$hourOutput values=$hourValues selected=`$settings.calendar_hour_fraction` id=calendar_hour_fraction} {tr}minutes{/tr}
			{formhelp note="Set the timeblocks for the day view."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Day View times" for="calendar_day_start"}
		{forminput}
			{tr}from{/tr} &nbsp;
			{html_options name=calendar_day_start output=$dayStart values=$dayStart selected=`$settings.calendar_day_start` id=calendar_day_start}
			&nbsp; {tr}to{/tr} &nbsp;
			{html_options name=calendar_day_end output=$dayEnd values=$dayEnd selected=`$settings.calendar_day_end` id=calendar_day_start}
			{formhelp note="Pick the start and end time of your day view."}
		{/forminput}
	</div>

	{if $gBitUser->isAdmin() }
	<div class="row">
		{formlabel label="User Override of Global Calendar Setting" for="user_pref"}
		{forminput}
			{html_checkboxes name="calendar_user_prefs" values="y" checked=`$settings.calendar_user_prefs` labels=false id=calendar_user_prefs}
			{formhelp note="Global override of the facility for users to set their own calendar preferences."}
		{/forminput}
	</div>
	{/if}
	
	<div class="row submit">
		<input type="submit" name="calendar_submit" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
