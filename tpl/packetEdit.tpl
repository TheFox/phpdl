<!-- BEGIN OF packetEdit.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

{$formBegin}
	<table border="0" cellpadding="0" cellspacing="4" width="100%">
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
		{$filesError}
		<tr>
			<td>Source (URL, ...)</td>
			<td><input type="text" name="source" value="{$source}" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="text" name="password" value="{$password}" /></td>
		</tr>
		<tr>
			<td>Speed</td>
			<td><input type="text" name="speed" value="{$speed}" /> kb/s (0 = unlimited)</td>
		</tr>
		<tr>
			<td>Sortnr</td>
			<td><input type="text" name="sortnr" value="{$sortnr}" maxlength="2" /></td>
		</tr>
		{$reset}
		<tr>
			<td colspan="2">{$save}</td>
		</tr>
	</table>
{$formEnd}

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF packetEdit.tpl //-->