<div class="display tikicalendar">
<div class="header">
<h1><a href="{$gBitLoc.CALENDAR_PKG_URL}admin/index.php">{tr}Manage Calendars{/tr}</a></h1>
</div>

<div class="body">

<h2>{tr}Create/edit Calendars{/tr}</h2>

<form action="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php" method="post">
	<input type="hidden" name="calendar_id" value="{$calendar_id|escape}" />
	<table class="panel">
		<tr><td>
			{tr}Name{/tr}:</td><td>
			<input type="text" name="name" value="{$name|escape}" />
		</td></tr>
		<tr><td>
			{tr}Description{/tr}:</td><td>
			<textarea name="description" rows="5" cols="40">{$description|escape}</textarea>
		</td></tr>
		<tr><td>
			{tr}Custom Locations{/tr}:</td><td>
			<select name="customlocations">
				<option value="y" {if $customlocations eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
				<option value="n" {if $customlocations eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
			</select>
		</td></tr>
		<tr><td>
			{tr}Custom Categories{/tr}:</td><td>
			<select name="customcategories">
				<option value="y" {if $customcategories eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
				<option value="n" {if $customcategories eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
			</select>
		</td></tr>
		<tr><td>
			{tr}Custom Languages{/tr}:</td><td>
			<select name="customlanguages">
				<option value="y" {if $customlanguages eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
				<option value="n" {if $customlanguages eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
			</select>
		</td></tr>
		<tr><td>
			{tr}Custom Priorities{/tr}:</td><td>
			<select name="custompriorities">
				<option value="y" {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
				<option value="n" {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
			</select>
		</td></tr>
		<tr class="panelsubmitrow"><td colspan="2">
			<input type="submit" name="save" value="{tr}Save{/tr}" />
		</td></tr>
</table>
</form>

<div class="navbar">
	<a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php">{tr}Create new calendar{/tr}</a>
</div>

<h2>{tr}List of Calendars{/tr}</h2>
{if count($calendars) gt 0}
<form method="get" action="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php">
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
     <input type="text" name="find" value="{$find|escape}" />
   </td><td>
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </td>
</tr>
</table>
</form>

<table class="data">
<tr>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendar_id_desc'}calendar_id_asc{else}calendar_id_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">{tr}location{/tr}</a></th>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">{tr}category{/tr}</a></th>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">{tr}language{/tr}</a></th>
<th><a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">{tr}priority{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{foreach key=id item=cal from=$calendars}
<tr class="{cycle}">
<td>{$id}</td>
<td><a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?calIds[]={$id}">{$cal.name}</a></td>
<td align="center">{$cal.customlocations}</td>
<td align="center">{$cal.customcategories}</td>
<td align="center">{$cal.customlanguages}</td>
<td align="center">{$cal.custompriorities}</td>
<td align="right">
   &nbsp;&nbsp;<a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;drop={$id}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this calendar?{/tr}')" title="Click here to delete this calendar"><img class="icon" alt="{tr}Remove{/tr}" src="{$gBitLoc.LIBERTY_PKG_URL}icons/delete.gif" /></a>&nbsp;&nbsp;
   <a href="{$gBitLoc.CALENDAR_PKG_URL}add_calendar.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;calendar_id={$id}"><img class="icon" alt="{tr}Edit{/tr}" src="{$gBitLoc.LIBERTY_PKG_URL}icons/edit.gif" /></a>
</td></tr>
{/foreach}
</table>

</div> {* end .body *}

{*not working just now*}
{*include file="bitpackage:kernel/pagination.tpl"*}

{else}
<div class="norecords">{tr}No records found{/tr}</div>
{/if}

</div> {* end .tikicalendar *}
