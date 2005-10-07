{form legend="Data Options" id="data_options"}
	<div class="row caloptions">
		{forminput}
			{foreach from=$contentTypes key=value item=type}
				<label>
					<div class="cal{$value}">
						<input type="checkbox" value="{$value}" name="content_type_guid[]"
							{foreach from=$smarty.session.calendar.content_type_guid item=selected}
								{if $selected eq $value}
									checked="checked"
								{/if}
							{/foreach}
						/> {$type}</label><br />
					</div>
			{/foreach}
			<script type="text/javascript">//<![CDATA[
				document.write("<label><input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'content_type_guid[]','switcher')\" /> {tr}Select all{/tr}</label><br />");
			//]]></script>
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="refresh" value="{tr}Update Calendar{/tr}" />
	</div>
{/form}
