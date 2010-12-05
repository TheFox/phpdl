<!-- BEGIN OF default.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = document.location.href;
		}, 1000 * 60);
		
		$('#exportHelp').bt('Export the packet as XML or TXT.', { trigger: 'hover', positions: 'top' });
		
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
				
				var packetTrFilesErrors = $('#packetTrFilesErrors' + id);
				packetTrFilesErrors.hide();
				packetTrFilesErrors.remove();
				
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
	
</script>

<table border="0">
	<tr><td colspan="11"><div id="status">{$status}</div></td></tr>
	<tr><td colspan="11"><a href="?a=packetEdit&amp;id=0">Add</a> | <a href="?a=packetSortExec">Sort</a></td></tr>
	<tr>
		<td>id</td>
		<td>move</td>
		<td>sortnr</td>
		<td>user</td>
		<td>name</td>
		<td>ctime</td>
		<td>stime</td>
		<td>ftime</td>
		<td>status</td>
		<td><a id="exportHelp"><u>exp</u></a></td>
		<td>archive</td>
	</tr>
	{$stack}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF default.tpl //-->