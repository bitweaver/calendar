<div class="calendar popup box">
	<h3>{$cellHash.title}</h3>
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<small>
			<br />
			{tr}First created by {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name}<br /> on {$cellHash.created|cal_date_format:"%Y-%m-%d - %H:%M %Z"}{/tr}
			<br />
			{tr}Last modified by {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name}<br /> on {$cellHash.last_modified|cal_date_format:"%Y-%m-%d - %H:%M %Z"}{/tr}
		</small>
	</div>
</div>
