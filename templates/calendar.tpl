{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.5 2005/07/21 09:39:30 lsces Exp $ *}

{popup_init src="`$gBitLoc.THEMES_PKG_URL`js/overlib.js"}
<div class="floaticon">
{if $bit_p_admin_calendar eq 'y' or $bit_p_admin eq 'y'}
  <a href="{$gBitLoc.CALENDAR_PKG_URL}admin/index.php"><img class="icon" src="{$gBitLoc.LIBERTY_PKG_URL}icons/config.gif"  alt="{tr}admin{/tr}" /></a>
{/if}
</div>

<div class="display calendar">
<div class="header">
<h1><a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?view={$view}">{tr}Calendar{/tr}</a></h1>
</div>

<div class="body">

{* ----------------------------------- *}

<div id="tab" style="display:{if $smarty.cookies.tab eq 'c' or $show_navtab}none{else}block{/if};">
  <div class="navbar above">
	<a href="javascript:show('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}hide('tab',1);">{tr}Calendars Panel{/tr}</a>
  </div>
</div>

{* ----------------------------------- *}

<div id="tabcal" style="display:{if $smarty.cookies.tabcal eq 'o' and !$show_navtab}block{else}none{/if};">
  <div class="navbar above">
	<a class="highlight" href="javascript:show('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}hide('tab',1);">{tr}Calendars Panel{/tr}</a>
	<a href="javascript:hide('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}show('tab',1);">{tr}Hide{/tr}</a>
  </div>

  <div class="calendar box">
  <form method="get" action="{$gBitLoc.CALENDAR_PKG_URL}index.php" id="f">
  <table class="panel">
	<tr>
		<td valign="top" width="100%">
		  <div class="boxtitle">{tr}Tools Calendars{/tr}</div>
		  <div class="boxcontent">
		  <div
			onclick="document.getElementById('calswitch').click();document.getElementById('calswitch').checked=!document.getElementById('calswitch').checked;document.getElementById('calswitch').click();"
			><input name="calswitch" id="calswitch" type="checkbox" onclick="switchCheckboxes(this.form.id,'bitcals[]','calswitch');this.checked=!this.checked;" /> {tr}check / uncheck all{/tr}
		  </div>
			{foreach from=$bitItems key=ki item=vi}
			  {if $vi.feature eq 'y' and $vi.right eq 'y'}
				<div
				  onclick="document.getElementById('bitcal_{$ki}').checked=!document.getElementById('bitcal_{$ki}').checked;"
				  onmouseout="this.style.textDecoration='none';"  
				  onmouseover="this.style.textDecoration='underline';" 
				  ><input type="checkbox" name="bitcals[]" value="{$ki|escape}" id="bitcal_{$ki}" {if $bitcal.$ki}checked="checked"{/if} onclick="this.checked=!this.checked;"/>
				  <span class="Cal{$ki}">{$vi.label}</span>
				</div>
			  {/if}
			{/foreach}
		  </div>
		  </td>
		</tr>
		<tr class="panelsubmitrow">
		  <td colspan="2">
			<input type="submit" name="refresh" value="{tr}Refresh{/tr}" />
		  </td>
		</tr>
  </table>
  </form>
  </div>
</div>


{* - Date Selection Row - *}
<div class="navigation">
<table>
	<tr><td>
	{if $gBitSystemPrefs.feature_jscalendar eq 'y'}
		<form action="{$gBitLoc.CALENDAR_PKG_URL}index.php" method="get" id="f">
			<input type="hidden" id="todate" name="todate" value="{$focusdate|date_format:"%B %e, %Y %H:%M"}" />
			<span title="{tr}Date Selector{/tr}" id="datrigger" class="daterow" >{$focusdate|bit_long_date}</span>
			&lt;- {tr}click to navigate{/tr}
		</form>
		<script type="text/javascript">
{literal}function gotocal()  { {/literal}
window.location = '{$gBitLoc.CALENDAR_PKG_URL}index.php?todate='+document.getElementById('todate').value;
{literal} } {/literal}
{literal}Calendar.setup( { {/literal}
date        : "{$focusdate|date_format:"%m/%d/%Y %H:%M"}",      // initial date
inputField  : "todate",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "datrigger",       // ID of the span where the date is to be shown
daFormat    : "{"%d/%m/%Y %H:%M"}",  // format of the displayed date
electric    : false,
onUpdate    : gotocal
{literal} } );{/literal}
		</script>
		</td>
		<td nowrap="nowrap" width="120" align="right">
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=day" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/day.gif" width="30" height="24" border="0" alt="{tr}day{/tr}" align="top" /></a>
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=week" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/week.gif" width="30" height="24" border="0" alt="{tr}week{/tr}" align="top" /></a>
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=month" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/month.gif" width="30" height="24" border="0" alt="{tr}month{/tr}" align="top" /></a>
	{else}
		<table><tr><td rowspan="2" align="left">
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$daybefore}" title="{$daybefore|bit_long_date}">&laquo; {tr}day{/tr}</a><br />
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekbefore}" title="{$weekbefore|bit_long_date}">&laquo; {tr}week{/tr}</a><br />
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthbefore}" title="{$monthbefore|bit_long_date}">&laquo; {tr}month{/tr}</a>
		</td>
		<td align="center">
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$now}" title="{$now|bit_short_date}"><b>{tr}today{/tr}:</b> {$now|bit_short_date}</a>
		</td>
		<td rowspan="2" align="right">
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$dayafter}" title="{$dayafter|bit_long_date}">{tr}day{/tr} &raquo;</a><br />
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$weekafter}" title="{$weekafter|bit_long_date}">{tr}week{/tr} &raquo;</a><br />
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$monthafter}" title="{$monthafter|bit_long_date}">{tr}month{/tr} &raquo;</a>
		</td>
		</tr><tr>
		<td align="center">
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=day" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/day.gif" width="30" height="24" border="0" alt="{tr}day{/tr}" align="top" /></a>
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=week" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/week.gif" width="30" height="24" border="0" alt="{tr}week{/tr}" align="top" /></a>
			<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?viewmode=month" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/month.gif" width="30" height="24" border="0" alt="{tr}month{/tr}" align="top" /></a>
		</td>
		</tr></table>
	{/if}
	</td>
</tr></table>
</div>



{* - Calendar Grid - *}
<table class="calendar">
<caption>{tr}selection{/tr}: {$focusdate|bit_long_date}</caption>
{if $viewmode eq 'day'}
{* - Single Day - *}
	<tr>
		<th width="42">{tr}Hours{/tr}</th>
		<th>{tr}Events{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=h loop=$hours}
	<tr class="{cycle}">
		<td width="42">{$hours[h]}{tr}h{/tr}</td>
		<td>
			{section name=hr loop=$hrows[h]}
			{assign var=over value=$hrows[h][hr].over}
			<div class="Cal{$hrows[h][hr].type}">
				{$hours[h]}:{$hrows[h][hr].mins} : 
				<span class="cal prio{$hrows[h][hr].prio}"><a href="{$hrows[h][hr].url}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
					{$hrows[h][hr].name|default:"..."}</a>
				</span>
				{if $hrows[h][hr].web}
					<a href="{$hrows[h][hr].web}" class="calweb" title="{$hrows[h][hr].web}">w</a>
				{/if}
{* - Omit description for moment - need to strip <CR> $hrows[h][hr].description *}
			</div>
			{/section}
		</td>
	</tr>
	{/section}
{else}
{* - Calendar Headings - *}
	<tr>
		<th width="2%"></th>
		{section name=dn loop=$daysnames}
			<th width="14%">{$daysnames[dn]}</th>
		{/section}
	</tr>
	{section name=w loop=$cell}
		<tr>
			<th class="weeknumber">{$weeks[w]}</th>
		{section name=d loop=$weekdays}

			{if $viewmode eq "month"}
				{if $cell[w][d].day|date_format:"%m" eq $focusmonth}
					{cycle values="odd,even" print=false advance=false}
				{else}
					{cycle values="notmonth" print=false advance=false}
				{/if}
			{else}
				{cycle values="odd,even" print=false advance=false}
			{/if}

			<td class="{cycle}">
			{if $cell[w][d].day|date_format:"%m" eq $focusmonth or $viewmode eq "week"}
				<div class="calday{if $cell[w][d].day eq $focusdate} highlight{/if}">
					<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$cell[w][d].day}">{$cell[w][d].day|date_format:"%d/%m"}</a>
				</div>

				{* - Calendar Content - *}
				<div class="calcontent">
					{section name=items loop=$cell[w][d].items}
					{assign var=over value=$cell[w][d].items[items].over}
					<div class="Cal{$cell[w][d].items[items].type}">
						<span class="cal prio{$cell[w][d].items[items].prio}"><a href="{$cell[w][d].items[items].url}" {popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}>
							{$cell[w][d].items[items].name|truncate:$trunc:".."|default:"..."}</a>
						</span>
						{if $cell[w][d].items[items].web}
							<a href="{$cell[w][d].items[items].web}" class="calweb" title="{$cell[w][d].items[items].web}">w</a>
						{/if}
						<br />
					</div>
					{/section}
				</div>
			{else}
				&nbsp;
			{/if}
			</td>
		{/section}
		</tr>
	{/section}
{/if}
</table>

</div>
</div>
