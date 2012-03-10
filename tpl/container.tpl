<!-- BEGIN OF {$smarty.template} //-->
{include file="{$siteStyleTplDir}/header.tpl"}


<table border="0" cellpadding="0" cellspacing="4" width="100%">
	{$contentPlain}
	<form action="?a=container&amp;sa=exec&amp;c={$container}" method="post" enctype="multipart/form-data">
	{$error}
	<tr>
		<td valign="top">File</td>
		<td><input type="file" name="file" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Open" /></td>
	</tr>
	</form>
</table>


{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF {$smarty.template} //-->