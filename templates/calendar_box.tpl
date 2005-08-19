<div class="calendar popup box">
	<h3>{$cellHash.title}</h3>
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<br />
		{tr}First created by {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name} at {$cellHash.created|bit_short_time}{/tr}
		<br />
		{tr}Last modified by {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name} at {$cellHash.last_modified|bit_short_time}{/tr}
	</div>
</div>
