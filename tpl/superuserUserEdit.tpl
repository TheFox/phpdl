<!-- BEGIN OF superuserUserEdit.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<form action="?a=superuserUserEditExec&amp;id={$id}" method="post">
	<table border="0">
		<tr>
			<td width="120">id</td>
			<td>{$id}</td>
		</tr>
		<tr>
			<td>Login</td>
			<td><input type="text" name="login" maxlength="32" value="{$login}" autocomplete="off" /></td>
		</tr>
		<tr>
			<td>New password</td>
			<td><input type="password" name="password" value="" autocomplete="off" /> (To unchange leave blank.)</td>
		</tr>
		<tr>
			<td>Superuser</td>
			<td><input type="checkbox" name="superuser" value="1" {$superuserChecked} /></td>
		</tr>
		<tr>
			<td colspan="2"><a href="?a=superuserUserDelExec&amp;id={$id}">Delete</a></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Save" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF superuserUserEdit.tpl //-->