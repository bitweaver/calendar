{strip}
<div class="calendar popup box">
{if $cellHash.content_id}
	{if $gBitSystem->isFeatureActive('calendar_ajax_popups')}
		<div class="floaticon"><a onclick="javascript:return cClick();">{biticon ipackage=icons iname=window-close iexplain="Close Popup"}</a></div>
	{/if}
	<h3>{$cellHash.title|escape}</h3>
	{if !empty($cellHash.rendered)}
		{$cellHash.rendered}
	{/if}
	{if $gBitUser->hasPermission('p_calendar_view_changes') && !empty($cellHash.creator_real_name) }

	<div class="boxcontent">
		{tr}Content Type{/tr}: {$gLibertySystem->getContentTypeName($cellHash.content_type_guid)}
		<br />
		<strong>{tr}First created{/tr}</strong>: {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name}<br />{$cellHash.created|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
		<br />
		<strong>{tr}Last modified{/tr}</strong>: {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name}<br />{$cellHash.last_modified|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
	</div>

	{/if}
{else}
<div class=error>{tr}No such content.{/tr}</div>
{/if}
</div>
{/strip}
