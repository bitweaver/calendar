<div class="navbar">
	<ul>
		<script type="text/javascript">//<![CDATA[
			document.write("<li><a href=\"javascript:toggle('tabcal');\">{tr}Show / Hide Options{/tr}</a></li>");
		//]]></script>
		{if $smarty.request.user_id}
			<li>{smartlink ititle="Show all"}</li>
		{else}
			<li>{smartlink ititle="Only my items" user_id=$gBitUser->mUserId}</li>
		{/if}
		<li>{smartlink ititle="Creation date" isort="created"}</li>
		<li>{smartlink ititle="Modification date" idefault=1 isort="last_modified"}</li>
	</ul>
</div>

<div class="clear"></div>

<script type="text/javascript">//<![CDATA[
	document.write("<div id=\"tabcal\" style=\"display:{if $smarty.cookies.tabcal eq 'o'}block{else}none{/if};\">");
//]]></script>

{form legend="Display Options" id="display_options"}
	<div class="row">
		{forminput}
			{html_checkboxes values=$contentTypes options=$contentTypes name=content_type_guid selected=$smarty.session.calendar.content_type_guid separator="<br />"}
			<script type="text/javascript">//<![CDATA[
				document.write("<label><input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'content_type_guid[]','switcher')\" />{tr}Select all{/tr}</label><br />");
			//]]></script>
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="refresh" value="{tr}Update Calendar{/tr}" />
	</div>
{/form}

<script type="text/javascript">//<![CDATA[
	document.write("</div>");
//]]></script>
