<div class="calendar popup box">
	<h3 class="{if $cellprio}calprio{$cellprio}{/if}">{$cellHash.last_modified|bit_short_time} in {$cellHash.type}</h3>
	<div class="boxcontent">
		<strong>{$cellHash.name}</strong>
		{if $cellcalname}
			{tr}in {$cellcalname}{/tr}
		{/if}
		<br />
		{tr}Last modified by {displayname login=$cellHash.modifier_user real_name=$cellHash.modifier_real_name}{/tr}
	</div>
</div>
