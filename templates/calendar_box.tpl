{strip}
<div class="calendar popup box">
	<h3>{$cellHash.title|escape}</h3>
	{if $cellHash.content_type_guid == 'bitevents' && $gBitSystem->isPackageActive('events')}
		<div class="center">
			{include file="bitpackage:events/render_header_inc.tpl" contentHash=$cellHash}
		</div>
	{/if}
	{if $gBitUser->hasPermission('p_calendar_view_changes')}
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<br />
		<strong>{tr}First created{/tr}</strong>: {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name}<br />{$cellHash.created|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
		<br />
		<strong>{tr}Last modified{/tr}</strong>: {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name}<br />{$cellHash.last_modified|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
	</div>
	{/if}
</div>
{/strip}
