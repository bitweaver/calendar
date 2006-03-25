<div class="calendar popup box">
	<h3>{$cellHash.title|escape}</h3>
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<br />
		<strong>{tr}First created{/tr}</strong>: {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name}<br />{$cellHash.created|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
		<br />
		<strong>{tr}Last modified{/tr}</strong>: {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name}<br />{$cellHash.last_modified|cal_date_format:"%Y-%m-%d - %H:%M %Z"}
	</div>
</div>
