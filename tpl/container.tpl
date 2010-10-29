<!-- BEGIN OF container.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}


<table border="1" cellpadding="0" cellspacing="4" width="100%">
	<form action="?a=container&amp;sa=exec&amp;c={$container}" method="post" enctype="multipart/form-data">
	{$error}
	<tr>
		<td width="120">Container</td>
		<td>{$container}</td>
	</tr>
	<tr>
		<td valign="top">File</td>
		<td><input type="file" name="file" /></td>
	</tr>
	<tr>
		<td valign="top">Crypted content</td>
		<td><textarea name="content" rows="20" cols="60"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Open" /></td>
	</tr>
	</form>
	{$contentPlain}
</table>


{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF container.tpl //-->