<!-- BEGIN OF packetArchive.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = document.location.href;
		}, 1000 * 900);
		
		{$jsDocumentReady}
		
	});
	
</script>

<table border="0">
	<tr><td colspan="8">Archive</td></tr>
	<tr>
		<td>id</td>
		<td>user</td>
		<td><a href="#" id="nameHelp">name</a></td>
		<td><a href="#" id="ctimeHelp">ctime</a></td>
		<td><a href="#" id="stimeHelp">stime</a></td>
		<td><a href="#" id="ftimeHelp">ftime</a></td>
		<td><a href="#" id="progressHelp">progress</a></td>
		<td>status</td>
		<td><a id="exportHelp"><u>exp</u></a></td>
	</tr>
	{$stack}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF packetArchive.tpl //-->