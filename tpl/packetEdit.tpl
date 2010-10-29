<!-- BEGIN OF packetEdit.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

{$formBegin}
	<table border="1" cellpadding="0" cellspacing="4" width="100%">
		<tr>
			<td colspan="2" class="packetHasError">{$error}</td>
		</tr>
		<tr>
			<td width="120">Packet Name</td>
			<td><input type="text" name="name" value="{$nameValue}" {$nameDisabled} /></td>
		</tr>
		<tr>
			<td valign="top">Links</td>
			<td><textarea name="urls" rows="20" cols="60">{$files}</textarea></td>
		</tr>
		<tr>
			<td width="120">Source (URL, ...)</td>
			<td><input type="text" name="source" value="{$source}" /></td>
		</tr>
		<tr>
			<td width="120">Password</td>
			<td><input type="text" name="password" value="{$password}" /></td>
		</tr>
		<tr>
			<td colspan="2">{$save}</td>
		</tr>
	</table>
{$formEnd}

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF packetEdit.tpl //-->