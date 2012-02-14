<!-- BEGIN OF {$smarty.template} //-->
{include file="{$siteStyleTplDir}/header-guest.tpl"}
1111
<form action="?a=loginExec" method="post">
	<table border="0">
		<tr>
			<td colspan="2"><div id="status">{$status}</div></td>
		</tr>
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
<!-- END OF {$smarty.template} //-->