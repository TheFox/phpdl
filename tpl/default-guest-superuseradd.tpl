<!-- BEGIN OF default-guest-superuseradd.tpl //-->
{include file="{$siteStyleTplDir}/header-guest.tpl"}

<form action="?a=superuserAddExec" method="post">
	<table border="0">
		<tr>
			<td colspan="2">
				This is your first run. Please add a superuser.
			</td>
		</tr>
		<tr>
			<td width="120">Login</td>
			<td><input type="text" name="login" maxlength="32" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Add" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer-guest.tpl"}
<!-- END OF default-guest-superuseradd.tpl //-->