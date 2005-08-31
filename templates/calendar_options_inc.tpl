{form legend="Data Options" id="data_options"}
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
