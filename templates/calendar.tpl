{* $Header: /cvsroot/bitweaver/_bit_calendar/templates/calendar.tpl,v 1.3 2005/07/16 08:03:49 lsces Exp $ *}

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

<div id="tab" style="display:{if $smarty.cookies.tab eq 'c' or $show_navtab or $calendar_id gt 0}none{else}block{/if};">
  <div class="navbar above">
	<a href="javascript:show('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}hide('tab',1);">{tr}Calendars Panel{/tr}</a>
	{if $modifiable}
	  <a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);">{tr}Events Panel{/tr}</a>
	{/if}
  </div>
</div>

{* ----------------------------------- *}

<div id="tabcal" style="display:{if $smarty.cookies.tabcal eq 'o' and !$show_navtab}block{else}none{/if};">
  <div class="navbar above">
	<a class="highlight" href="javascript:show('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}hide('tab',1);">{tr}Calendars Panel{/tr}</a>
	{if $modifiable}
	  <a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);">{tr}Events Panel{/tr}</a>
	{/if}
	<a href="javascript:hide('tabcal',1);{if $modifiable}hide('tabnav',1);{/if}show('tab',1);">{tr}Hide{/tr}</a>
  </div>

  <div class="calendar box">
  <form method="get" action="{$gBitLoc.CALENDAR_PKG_URL}index.php" id="f">
  <table class="panel">
	<tr>

	  {if $modifiable}
	  <td valign="top" width="50%">
		<div class="boxtitle">{tr}Group Calendars{/tr}</div>
		  <div class="boxcontent">
		  <div
			onclick="document.getElementById('calswitch').click();document.getElementById('calswitch').checked=!document.getElementById('calswitch').checked;document.getElementById('calswitch').click();"
			><input name="calswitch" id="calswitch" type="checkbox" onclick="switchCheckboxes(this.form.id,'calIds[]','calswitch');this.checked=!this.checked;" /> {tr}check / uncheck all{/tr}
		  </div>
		  {foreach item=k from=$listcals}
			<div
			  onclick="document.getElementById('groupcal_{$k}').checked=!document.getElementById('groupcal_{$k}').checked;"
			  onmouseout="this.style.textDecoration='none';" 
			  onmouseover="this.style.textDecoration='underline';"
			  ><input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if}
			  onclick="this.checked=!this.checked;" />
			  {$infocals.$k.name} (id #{$k})
			</div>
		  {/foreach}
		  <span class="Cal0">{tr}Tentative{/tr}</span>
		  <span class="Cal1">{tr}Confirmed{/tr}</span>
		  <span class="Cal2">{tr}Cancelled{/tr}</span>
		  </div>
		</td>
		{/if}

	  {if $modifiable}
			<td valign="top" width="50%">
		{else}
			<td valign="top" width="100%">
		{/if}
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

{if $modifiable}
{* - Edit Event Panel - *}
<div id="tabnav" style="display:{if $smarty.cookies.tabnav eq 'o' or $show_navtab or $calendar_id gt 0}block{else}none{/if};">
	<div class="navbar above">
		<a href="javascript:show('tabcal',1);hide('tabnav',1);hide('tab',1);">{tr}Calendars Panel{/tr}</a>
		<a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);" class="highlight">{tr}Events Panel{/tr}</a>
		<a href="javascript:hide('tabcal',1);hide('tabnav',1);show('tab',1);">{tr}Hide{/tr}</a>
  	</div>

	<div class="calendar box">
		{* ······························· *}
		{if ($calitem_id > 0 and $bit_p_change_events eq 'y') or ($calendar_id > 0 and $bit_p_add_events eq 'y')}

			{if $calitem_id}
				<div class="boxtitle">{tr}Edit Calendar Item{/tr}</div>
				<h3>{$calname}: {$name|default:"new event"} (id #{$calitem_id})</h3>
				<small>
				  {tr}Created on{/tr} {$created|bit_long_date} {$created|bit_long_time}<br />
				  {tr}Modified on{/tr} {$last_modified|bit_long_date} {$last_modified|bit_long_time}<br />
				  {tr}by{/tr} <span class="user_id">{$lastUser}</span>
				</small>
			{else}
				<div class="boxtitle">{tr}New Calendar Item{/tr}</div>
			{/if}
			
			<form enctype="multipart/form-data" method="post" action="{$gBitLoc.CALENDAR_PKG_URL}index.php" id="editcalitem" id="f" style="display:block;">
				<input type="hidden" name="editmode" value="1" />
				{if $bit_p_change_events and $calitem_id}
					<input type="hidden" name="calitem_id" value="{$calitem_id}" />
				{/if}
				<table class="panel">
				{if $customcategories eq 'y'}
					<tr><td>{tr}Category{/tr}</td><td>
						<select name="category_id">
						{section name=t loop=$listcat}
						{if $listcat[t]}
							<option value="{$listcat[t].category_id|escape}" {if $category_id eq $listcat[t].category_id}selected="selected"{/if}>{$listcat[t].name}</option>
						{/if}
						{/section}
						</select>
						{tr}or create a new category{/tr} 
						<input type="text" name="newcat" value="" />
						{if $category_id}
							<small>( {$categoryName} )</small>
						{/if}
					</td></tr>
				{/if}
				
				{if $customlocations eq 'y'}
					<tr><td class="form">{tr}Location{/tr}</td><td class="form">
						<select name="location_id">
						{section name=l loop=$listloc}
						{if $listloc[l]}
							<option value="{$listloc[l].location_id|escape}" {if $location_id eq $listloc[l].location_id}selected="selected"{/if}>{$listloc[l].name}</option>
						{/if}
						{/section}
						</select>
						{tr}or create a new location{/tr} 
						<input type="text" name="newloc" value="" />
						{if $location_id}
							<span class="mini">( {$locationName} )</span>
						{/if}
					</td></tr>
				{/if}
					
				{if $customparticipants eq 'y'}
					<tr><td class="form">{tr}Organized by{/tr}</td><td class="form">
						<input type="text" name="organizers" value="{$organizers|escape}" id="organizers" />
						{tr}comma separated usernames{/tr}
					</td></tr>
					
					<tr><td class="form">{tr}Participants{/tr}</td><td class="form">
						<input type="text" name="participants" value="{$participants|escape}" id="participants" />
						{tr}comma separated username:role{/tr} 
						{tr}with roles{/tr} {tr}Chair{/tr}:0, {tr}Required{/tr}:1, {tr}Optional{/tr}:2, {tr}None{/tr}:3
					</td></tr>
				{/if}
					
				<tr><td>{tr}Start{/tr}</td><td>
					{if $gBitSystemPrefs.feature_jscalendar eq 'y'}
						<input type="hidden" name="start_date_input" value="{$start_time|date_format:"%m/%d/%Y %H:%M"}" id="start_date_input" />
						<span id="start_date_display" class="daterow">{$start_time|date_format:$daformat}</span>
						<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$start_time|date_format:"%m/%d/%Y %H:%M"}",      // initial date
inputField  : "start_date_input",      // ID of the input field
ifFormat    : "%m/%d/%Y %H:%M",    // the date format
displayArea : "start_date_display",       // ID of the span where the date is to be shown
daFormat    : "{$daformat}",  // format of the displayed date
showsTime   : true,
electric    : true,
align       : "bR"
{literal} } );{/literal}
						</script>
					{else}
	  					{html_select_date time=$start_time prefix="start_" end_year="+4" field_order=DMY}
	  					{html_select_time minute_interval=10 time=$start_time prefix="starth_" display_seconds=false use_24_hours=true}
					{/if}
				</td></tr>
					
				<tr><td>{tr}End{/tr}</td><td>
					{if $gBitSystemPrefs.feature_jscalendar eq 'y'}
						<input type="hidden" name="end_date_input" value="{$end_time|date_format:"%m/%d/%Y %H:%M"}" id="end_date_input" />
						<span id="end_date_display" class="daterow">{$end_time|date_format:$daformat}</span>
						<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$end_time|date_format:"%m/%d/%Y %H:%M"}",      // initial date
inputField  : "end_date_input",      // ID of the input field
ifFormat    : "%m/%d/%Y %H:%M",    // the date format
displayArea : "end_date_display",       // ID of the span where the date is to be shown
daFormat    : "{$daformat}",  // format of the displayed date
showsTime   : true,
electric    : true,
align       : "bR"
{literal} } );{/literal}
	  					</script>
					{else}
						{html_select_date time=$end_time prefix="end_" end_year="+4" field_order=DMY}
						{html_select_time minute_interval=10 time=$end_time prefix="endh_" display_seconds=false use_24_hours=true}
					{/if}
				</td></tr>
					
				<tr><td>{tr}Name{/tr}</td><td><input type="text" name="name" value="{$name|escape}" />
					{if $name}<span class="mini">( {$name} )</span>{/if}
				</td></tr>
				<tr><td>{tr}Description{/tr}</td><td>
					<textarea name="description" rows="8" cols="80" id="description">{$description|escape}</textarea>
					{if $description}<div class="description">({$description})</div>{/if}
				</td></tr>
					
				<tr><td>{tr}URL{/tr}</td><td><input type="text" name="url" value="{$url|escape}" />
					{if $url}<span class="url">(<a href="{$url}">{$url}</a>)</span>{/if}
				</td></tr>
				
				{if $custompriorities eq 'y'}
					<tr>
						<td>{tr}Priority{/tr}</td>
						<td>
							<select name="priority">
							<option value="1" {if $priority eq 1}selected="selected"{/if} class="cal prio1">1</option>
							<option value="2" {if $priority eq 2}selected="selected"{/if} class="cal prio2">2</option>
							<option value="3" {if $priority eq 3}selected="selected"{/if} class="cal prio3">3</option>
							<option value="4" {if $priority eq 4}selected="selected"{/if} class="cal prio4">4</option>
							<option value="5" {if $priority eq 5}selected="selected"{/if} class="cal prio5">5</option>
							<option value="6" {if $priority eq 6}selected="selected"{/if} class="cal prio6">6</option>
							<option value="7" {if $priority eq 7}selected="selected"{/if} class="cal prio7">7</option>
							<option value="8" {if $priority eq 8}selected="selected"{/if} class="cal prio8">8</option>
							<option value="9" {if $priority eq 9}selected="selected"{/if} class="cal prio9">9</option>
							</select>
							{if $priority}<span class="mini">( <span class="cal prio{$priority}">{$priority}</span> )</span>{/if}
	  					</td>
					</tr>
				{/if}
					
				<tr>
					<td>{tr}Status{/tr}</td>
					<td>
						<select name="status">
						<option value="0" {if $status eq 0}selected="selected"{/if}>0:{tr}Tentative{/tr}</option>
						<option value="1" {if $status eq 1}selected="selected"{/if}>1:{tr}Confirmed{/tr}</option>
						<option value="2" {if $status eq 2}selected="selected"{/if}>2:{tr}Cancelled{/tr}</option>
	  					</select>
						{if $calitem_id}<span class="Cal{$status}"><span class="mini">( {$status} )</span></span>{/if}
					</td>
				</tr>
					
				{if $customlanguages eq 'y'}
					<tr>
						<td>{tr}Language{/tr}</td>
						<td>
							<select name="lang">
							{section name=ix loop=$languages}
								<option value="{$languages[ix].value|escape}"
									{$languages[ix].name}
									{if $lang eq $languages[ix].value}selected="selected"{/if}>
								</option>
							{/section}
							</select>
							{if $lang}<span class="mini">( {$lang} )</span>{/if}
	  					</td>
					</tr>
				{/if}
					
				<tr class="panelsubmitrow">
					<td colspan="2">
						<input type="submit" name="save" value="{tr}save{/tr}" />
						{if $calitem_id and $bit_p_change_events}
							<input type="submit" name="copy" value="{tr}duplicate{/tr}" />
						{/if}
						{tr}to{/tr}
						<select name="calendar_id">
						{foreach item=lc from=$listcals}
							<option value="{$lc|escape}" {if $calendar_id eq $lc}selected="selected"{/if} onchange="document.forms[f].submit();">{$infocals.$lc.name}</option>
						{/foreach}
						</select>
						{tr}or{/tr}
						<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?calitem_id={$calitem_id}&amp;delete=1">{tr}delete{/tr}</a>
					</td>
				</tr>
				
				</table>
	    	</form>
		{else}
		{* - Add New Event - *}
			<h2>{tr}Add Calendar Item{/tr}</h2>
			<ul>
			{foreach name=licals item=k from=$listcals}
				{if $infocals.$k.bit_p_add_events eq 'y'}
					<li>{tr}in{/tr} <a href="{$gBitLoc.CALENDAR_PKG_URL}index.php?todate={$focusdate}&amp;calendar_id={$k}&amp;editmode=add" class="link">{$infocals.$k.name}</a></li>
				{/if}
			{/foreach}
			</ul>
		{* ······························· *}
        {/if}
	</div>
</div>
{/if}

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
window.location = '{$gBitLoc.CALENDAR_PKG_URL}index.php?todate='+document.getElementById('todate').value+'{if $calendar_id}&amp;calendar_id={$calendar_id}&amp;editmode=add{/if}';
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
				<div class="Cal{$hrows[h][hr].type}">
					{$hours[h]}:{$hrows[h][hr].mins} : 
					<a href="{$hrows[h][hr].url}">{$hrows[h][hr].name}</a>
					{$hrows[h][hr].description}
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
					<div class="Cal{$cell[w][d].items[items].type}{if $cell[w][d].items[items].calitem_id eq $calitem_id and $calitem_id|string_format:"%d" ne 0} highlight{/if}">
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
