{if $gBitSystem->isFeatureActive('calendar_user_prefs') }
	{jstab title="User Calendar"}
		{include file='bitpackage:calendar/calendar_preferences_inc.tpl' settings=$userPrefs}
	{/jstabs}
{/if}
