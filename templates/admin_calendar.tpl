{strip}
{form legend="Home Calendar"}
	<input type="hidden" name="page" value="{$page}" />
	<div class="row">
		{formlabel label="Home Calendar" for="homeCalendar"}
		{forminput}
			<select name="homeCalendar" id="homeCalendar">
				{section name=ix loop=$blogs}
					<option value="{$blogs[ix].blog_id|escape}" {if $blogs[ix].blog_id eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"...":true}</option>
				{sectionelse}
					<option>{tr}No records found{/tr}</option>
				{/section}
			</select>
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="calendarset" value="{tr}Change preference{/tr}" />
	</div>
{/form}

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