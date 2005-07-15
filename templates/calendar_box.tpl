<div class="tikicalendar popup box">
	<div class="boxtitle">{$cellhead}
		{if $cellprio}
			<span class="calprio{$cellprio}">{$cellprio}</span>
		{/if}
	</div>
	<div class="boxcontent"><b>{$cellname}</b>
		{if $cellcalname}
			{tr}in{/tr} <b>{$cellcalname}</b>
		{/if}<br />
		{$celldescription}
	</div>
</div>
