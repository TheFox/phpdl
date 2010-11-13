<!-- BEGIN OF superuserUsers.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<table border="0">
	<tr>
		<td colspan="2"><a href="?a=superuserUserEdit&amp;id=0">Add</a></td>
	</tr>
	<tr>
		<td width="30">id</td>
		<td width="120">login</td>
	</tr>
	{$users}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF superuserUsers.tpl //-->