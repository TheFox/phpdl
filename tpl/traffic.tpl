<!-- BEGIN OF traffic.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<table border="0">
	<tr>
		<td colspan="{$listColspan}"><a href="?a=traffic&amp;type=days">Days</a> | <a href="?a=traffic&amp;type=months">Months</a> | <a href="?a=traffic&amp;type=years">Years</a> | <a href="?a=smartyCacheClear&amp;tpl={$smartyTpl}&amp;cacheId={$smartyCacheId}">Clear cache</a></td>
	</tr>
	<tr>
		<td colspan="{$listColspan}">{$type}: <b>{$itemsNum}</b></td>
	</tr>
	<tr>
		<td colspan="{$listColspan}">Traffic total: <b>{$trafficTotal}</b></td>
	</tr>
	<tr><td colspan="{$listColspan}">&nbsp;</td></tr>
	<tr>
		<td>{$type}</td>
		<td>Traffic</td>
	</tr>
	{$list}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF traffic.tpl //-->