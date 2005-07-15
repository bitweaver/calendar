{strip}
<ul>
{if $bit_p_view_calendar eq 'y'}
	<li><a class="item" href="{$gBitLoc.CALENDAR_PKG_URL}index.php">{tr}Display Calendar{/tr}</a></li>
{/if}
{if $bit_p_add_events eq 'y'}
	<li><a class="item" href="{$gBitLoc.CALENDAR_PKG_URL}edit.php">{tr}Create/Edit an Event{/tr}</a></li>
{/if}
</ul>
{/strip}