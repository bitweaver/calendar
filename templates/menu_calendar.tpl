{strip}
<ul>
	<li><a class="item" href="{$smarty.const.CALENDAR_PKG_URL}index.php">{booticon iname="icon-calendar" iexplain="Display Calendar" ilocation=menu}</a></li>
	{if $gBitSystem->isPackageActive( 'minical' )}
		<li><a class="item" href="{$smarty.const.MINICAL_PKG_URL}index.php">{biticon ipackage=liberty iname=spacer iexplain="Mini Calendar" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
