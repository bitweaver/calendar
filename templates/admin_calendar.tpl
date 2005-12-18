{strip}
{if !$settings}
	{assign var=settings value=$gBitSystemPrefs}
{/if}

{form legend="Calendar Settings"}
	<input type="hidden" name="page" value="{$page}" />
	<div class="row">
		{formlabel label="First day of Week" for="week_offset"}
		{forminput}
			{html_options name=week_offset output=$firstDayOutput values=$firstDayValues selected=`$settings.week_offset` id=week_offset}
			{formhelp note="Set the first day of the week."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Time Blocks" for="hour_fraction"}
		{forminput}
			{html_options name=hour_fraction output=$hourOutput values=$hourValues selected=`$settings.hour_fraction` id=hour_fraction} {tr}minutes{/tr}
			{formhelp note="Set the timeblocks for the day view."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Day View times" for="day_start"}
		{forminput}
			{tr}from{/tr} &nbsp;
			{html_options name=day_start output=$dayStart values=$dayStart selected=`$settings.day_start` id=day_start}
			&nbsp; {tr}to{/tr} &nbsp;
			{html_options name=day_end output=$dayEnd values=$dayEnd selected=`$settings.day_end` id=day_start}
			{formhelp note="Pick the start and end time of your day view."}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="calendar_submit" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
