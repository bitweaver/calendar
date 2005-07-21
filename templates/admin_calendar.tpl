{strip}
{form legend="Calendar Features"}
	<input type="hidden" name="page" value="{$page}" />

	{foreach from=$formCalendarFeatures key=item item=output}
		<div class="row">
			{formlabel label=`$output.label` for=$item}
			{forminput}
				{html_checkboxes name="$item" values="y" checked=`$gBitSystemPrefs.$item` labels=false id=$item}
			{/forminput}
			{formhelp note=`$output.help` page=`$output.page`}
		</div>
	{/foreach}

	<div class="row submit">
		<input type="submit" name="calendarfeatures" value="{tr}Change preferences{/tr}" />
	</div>
{/form}

{/strip}