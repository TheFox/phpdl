<!-- BEGIN OF packetEdit.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

{$formBegin}
	<table border="0" cellpadding="0" cellspacing="4" width="100%">
		<tr>
			<td colspan="{$tableColspan}">{$save}</td>
		</tr>
		<tr>
			<td colspan="{$tableColspan}" class="packetHasError">{$error}</td>
		</tr>
		<tr>
			<td width="120">Packet Name</td>
			<td><input type="text" name="name" value="{$nameValue}" {$nameDisabled} /></td>
		</tr>
		<tr>
			<td valign="top">Links</td>
			<td><textarea name="urls" rows="20" cols="80">{$files}</textarea></td>
		</tr>
		{$filesError}
		<tr><td colspan="{$tableColspan}">&nbsp;</td></tr>
		<tr><td colspan="{$tableColspan}">Optional</td></tr>
		<tr>
			<td>Source (URL, ...)</td>
			<td><input type="text" name="source" value="{$source}" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="text" name="password" value="{$password}" /></td>
		</tr>
		<tr>
			<td>Sortnr</td>
			<td><input type="text" name="sortnr" value="{$sortnr}" maxlength="2" /></td>
		</tr>
		<tr>
			<td><a href="#" id="httpAuthHelp">HTTP</a></td>
			<td>User <input type="text" name="httpUser" value="{$httpUser}" maxlength="256" autocomplete="off" /> Password <input type="password" name="httpPassword" value="{$httpPassword}" maxlength="256" autocomplete="off" /></td>
		</tr>
		{$reset}
		<tr>
			<td colspan="{$tableColspan}">{$save}</td>
		</tr>
	</table>
{$formEnd}

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF packetEdit.tpl //-->