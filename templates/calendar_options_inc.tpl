{strip}
{form legend="Data Options" id="data_options"}
	<div class="row caloptions">
		{forminput}
			{foreach from=$calContentTypes key=value item=type}
				<div class="cal{$value}">
					<label>
						<input type="checkbox" value="{$value}" name="content_type_guid[]"
							{foreach from=$smarty.session.calendar.content_type_guid item=selected}
								{if $selected eq $value}
									checked="checked"
								{/if}
							{/foreach}
						/> {$type}
					</label>
				</div>
			{/foreach}
			<script type="text/javascript">/* <![CDATA[ */
				document.write("<label><input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.switchCheckboxes(this.form.id,'content_type_guid[]','switcher')\" /> {tr}Select all{/tr}</label><br />");
			/* ]]> */</script>
		{/forminput}
	</div>

	<div class="control-group submit">
		<input type="submit" name="refresh" value="{tr}Update Calendar{/tr}" />
	</div>
{/form}
{/strip}
