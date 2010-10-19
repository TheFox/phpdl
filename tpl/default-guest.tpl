<!-- BEGIN OF default.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<form action="?a=loginExec" method="post"
	<table border="1">
		<tr>
			<td>Login</td>
			<td><input type="text" name="login" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF default.tpl //-->