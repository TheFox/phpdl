<!-- BEGIN OF {$smarty.template} //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	var packetsReloadsInterval = 10 * 1000;
	var packetProgressbarBaseId = '{$packetProgressbarBaseId}';
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = document.location.href;
		}, 1000 * 300);
		
		setTimeout(function(){
			packetsReload();
		}, packetsReloadsInterval);
		
		$('#stack').tableDnD({
			onDrop: function(table, row){
				var ids = new Array();
				var rows = table.tBodies[0].rows;
				for(var i = 0; i < rows.length; i++){
					var id = rows[i].id;
					if(id != ''){
						id = id.substr(8);
						ids.push(id + '');
					}
				}
				$.ajaxSync({
					type: 'POST',
					url: '?a=packetMoveExec',
					data: 'ids=' + ids,
					success: function(){
						statusAddInfo("Order saved.");
					}
				});
			}
		});
		
		$('.ui-state-default.ui-icon.ui-icon-circle-minus').each(function(){
			$(this).css('cursor', 'default');
		});
		
		{$jsDocumentReady}
	});
	
	function statusAddInfo(text){
		var div = $('<div>');
		div.css('margin-top', '1px');
		div.css('padding', '0 1px');
		div.addClass('ui-state-highlight ui-corner-all');
		
		var spanicon = $('<span>');
		spanicon.css('float', 'left');
		spanicon.css('margin-right', '1px');
		spanicon.addClass('ui-icon ui-icon-info');
		div.append(spanicon);
		div.append(text);
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
	
	function packetArchiveExec(id, name){
		var button = $('#packetArchiveExecButton' + id);
		button.hide();
		
		$.ajaxSync({
			type: 'GET',
			url: '?a=packetArchiveExec&id=' + id + '&noredirect=1',
			success: function(){
				var packetTr = $('#packetTr' + id);
				packetTr.hide();
				packetTr.remove();
				
				statusAddInfo("Packet '" + name + "' (" + id + ") successfully archived.");
			}
		});
	}
	
	function packetActiveExec(id, obj){
		$.ajaxSync({
			type: 'GET',
			url: '?a=packetActiveExec&id=' + id + '&active=' + ($(obj).is(':checked') ? 1 : 0) + '&noredirect=1',
			success: function(){}
		});
	}
	
	function packetsReload(){
		$.ajaxSync({
			type: 'GET',
			url: '?a=packetsReload',
			success: function(data){
				var packets = eval('(' + data + ')');
				
				for(var packetId in packets){
					var packet = packets[packetId];
					var packetProgressBarId = '#' + packetProgressbarBaseId + packet.id;
					
					$(packetProgressBarId).progressbar({ value: packet.filesFinishedPercent });
					$(packetProgressBarId).bt(packet.filesFinishedPercent + ' %, ' + packet.filesFinished + '/' + packet.filesC + ' files', { trigger: 'hover', positions: 'top' });
					
					/*if(packet.filesDownloading)
						$('#packetStatus' + packet.id).html('downloading (' + packet.filesDownloading + ')');*/
					
				}
			}
		});
		
		setTimeout(function(){
			packetsReload();
		}, packetsReloadsInterval);
	}
	
</script>

<table border="0" id="stack">
	<tr><td colspan="{$tableColspan}">Stack</td></tr>
	<tr><td colspan="{$tableColspan}"><a href="?a=packetEdit&amp;id=0">Add</a> | <a href="?a=packetSortExec">Sort</a> | Set all packets to [<a href="?a=packetActiveAllExec&amp;active=1">active</a> or <a href="?a=packetActiveAllExec&amp;active=0">inactive</a>]</td></tr>
	<tr><td colspan="{$tableColspan}"><div id="status" class="ui-widget">{$status}</div></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td>id</td>
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
<!-- END OF {$smarty.template} //-->