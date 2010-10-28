<!-- BEGIN OF dlpacketEdit.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<form action="?a=dlpacketEditSave&amp;id={$id}" method="post">
	<table border="1" cellpadding="0" cellspacing="4" width="100%">
		{$error}
		<tr>
			<td width="120">Packet Name</td>
			<td><input type="text" name="name" value="{$nameValue}" {$nameDisabled} /></td>
		</tr>
		<tr>
			<td>Links</td>
			<td><textarea name="urls" rows="20" cols="60">{$files}</textarea></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Save" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF dlpacketEdit.tpl //-->