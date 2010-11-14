<!-- BEGIN OF packetArchive.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = document.location.href;
		}, 1000 * 900);
		
	});
	
</script>

<table border="0">
	<tr><td colspan="8">Archive</td></tr>
	<tr>
		<td>id</td>
		<td>user</td>
		<td>name</td>
		<td>ctime</td>
		<td>stime</td>
		<td>ftime</td>
		<td>status</td>
		<td>info</td>
	</tr>
	{$stack}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF packetArchive.tpl //-->