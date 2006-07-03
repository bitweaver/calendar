{if $gBitSystem->isFeatureActive('calendar_user_prefs') }
	{include file='bitpackage:calendar/calendar_preferences_inc.tpl' settings=$userPrefs}
{/if}