<div class="calendar popup box">
	<h3>{$cellHash.title}</h3>
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<br />
		{tr}Last modified by {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name} at {$cellHash.last_modified|bit_short_time}{/tr}
	</div>
</div>
