<!-- BEGIN OF default-guest.tpl //-->
{include file="{$siteStyleTplDir}/header-guest.tpl"}

<form action="?a=loginExec" method="post">
	<table border="0">
		<tr>
			<td width="120">Login</td>
			<td><input type="text" name="login" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer-guest.tpl"}
<!-- END OF default-guest.tpl //-->