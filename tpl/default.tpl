<!-- BEGIN OF default.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = document.location.href;
		}, 1000 * 60);
		
		{$jsDocumentReady}
		
	});
	
	function packetArchiveExec(id){
		var button = $('#packetArchiveExecButton' + id);
		button.hide();
		
		$.ajax({
			type: 'GET',
			url: '?a=packetArchiveExec&id=' + id + '&noredirect=1',
			success: function(){
				var packetTr = $('#packetTr' + id);
				packetTr.hide();
				packetTr.remove();
				
				var div = $('<div>');
				div.css('background-color', '#00ff00');
				div.html('Packet (' + id + ') successfully archived.');
				div.hide();
				$('#status').append(div);
				div.slideDown('slow', function(){
					setTimeout(function(){
						div.slideUp('slow', function(){
							$(this).remove();
						});
					}, 3000);
				});
			}
		});
	}
	
	function packetActiveExec(id, obj){
		$.ajax({
			type: 'GET',
			url: '?a=packetActiveExec&id=' + id + '&active=' + ($(obj).is(':checked') ? 1 : 0) + '&noredirect=1',
			success: function(){}
		});
	}
	
</script>

<table border="0">
	<tr><td colspan="{$tableColspan}">Stack</td></tr>
	<tr><td colspan="{$tableColspan}"><a href="?a=packetEdit&amp;id=0">Add</a> | <a href="?a=packetSortExec">Sort</a> | Set all packets to [<a href="?a=packetActiveAllExec&amp;active=1">active</a> or <a href="?a=packetActiveAllExec&amp;active=0">inactive</a>]</td></tr>
	<tr><td colspan="{$tableColspan}"><div id="status">{$status}</div></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td>id</td>
		<td>move</td>
		<td>sortnr</td>
		<td>user</td>
		<td><a href="#" id="nameHelp">name</a></td>
		<td><a href="#" id="ctimeHelp">ctime</a></td>
		<td><a href="#" id="stimeHelp">stime</a></td>
		<td><a href="#" id="ftimeHelp">ftime</a></td>
		<td><a href="#" id="progressHelp">progress</a></td>
		<td>status</td>
		<td><a href="#" id="exportHelp">exp</a></td>
		<td><a href="#" id="archiveHelp">archive</a></td>
	</tr>
	{$stack}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF default.tpl //-->