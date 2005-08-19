<div class="calendar popup box">
	<h3>{$cellHash.title}</h3>
	<div class="boxcontent">
		{tr}Content Type{/tr}: {$cellHash.content_description}
		<small>
			<br />
			{tr}First created by {displayname login=$cellHash.creator_user real_name=$cellHash.creator_real_name} on {$cellHash.created|date_format:"%d %b %Y, %H:%m"}{/tr}
			<br />
			{tr}Last modified by {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name} on {$cellHash.last_modified|date_format:"%d %b %Y, %H:%m"}{/tr}
		</small>
	</div>
</div>
