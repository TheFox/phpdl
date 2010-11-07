<!-- BEGIN OF scheduler.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<form action="?a=schedulerEditExec&amp;id={$id}" method="post">
	<table border="1">
		<tr>
			<td>Name (optional)</td>
			<td><input type="text" name="name" value="{$name}" maxlength="255" /></td>
		</tr>
		<tr>
			<td>Active</td>
			<td><input type="checkbox" name="active" value="1" {$activeChecked} /></td>
		</tr>
		<tr>
			<td>Download</td>
			<td><input type="checkbox" name="download" value="1" {$downloadChecked} /></td>
		</tr>
		<tr>
			<td>Day time invert</td>
			<td><input type="checkbox" name="activeDayTimeInvert" value="1" {$activeDayTimeInvertChecked} /></td>
		</tr>
		<tr>
			<td>Day time begin (hh:mm[:ss])</td>
			<td><input type="text" name="activeDayTimeBegin" value="{$activeDayTimeBegin}" maxlength="8" /></td>
		</tr>
		<tr>
			<td>Day time end (hh:mm[:ss])</td>
			<td><input type="text" name="activeDayTimeEnd" value="{$activeDayTimeEnd}" maxlength="8" /></td>
		</tr>
		<tr>
			<td>Sortnr</td>
			<td><input type="text" name="sortnr" value="{$sortnr}" maxlength="2" /></td>
		</tr>
		{$del}
		<tr>
			<td colspan="2"><input type="submit" value="Save" /></td>
		</tr>
	</table>
</form>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF scheduler.tpl //-->