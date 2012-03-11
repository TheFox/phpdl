<!-- BEGIN OF {$smarty.template} //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<style type="text/css">
	
	.msgBlack{
		background-color: #000000;
	}
	
</style>

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
		
		statusAddInfo('test');
		
		{$jsDocumentReady}
	});
	
	function statusAddInfo(text){
		$.gritter.add({
			title: 'Notification',
			text: text,
			sticky: false,
			time: 3000,
			class_name: 'msgBlack'
		});
	}
	
	function packetArchiveExec(id, name){
		var button = $('#packetArchiveExecButton' + id);
		
		$.ajaxSync({
			type: 'GET',
			url: '?a=packetArchiveExec&id=' + id + '&noredirect=1',
			success: function(){
				var packetTr = $('#packetTr' + id);
				packetTr.fadeOut('slow', function(){ $(this).remove(); });
				
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

<table border="0">
	<tr><td>Stack</td></tr>
	<tr><td><a href="?a=packetEdit&amp;id=0">Add</a> | <a href="?a=packetSortExec">Sort</a> | Set all packets to [<a href="?a=packetActiveAllExec&amp;active=1">active</a> or <a href="?a=packetActiveAllExec&amp;active=0">inactive</a>]</td></tr>
	<tr><td colspan="{$tableColspan}"><div id="status" class="ui-widget">{$status}</div></td></tr>
	<tr>
		<td>
			<table border="0" id="stack">
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
				
				{foreach from=$stackArray item=packet}
					<tr id="packetTr{$packet.id}">
						<td class="{$packet.trClass}"><input id="packetActive{$packet.id}" type="checkbox" value="1" onChange="packetActiveExec('.$packetId.', this)" tabindex="{$packet.counter}" {$packet.activeChecked} /></td>
						<td class="{$packet.trClass}">{$packet.id}</td>
						<td class="{$packet.trClass}">{$packet.sortnr}</td>
						<td class="{$packet.trClass}">{$packet.userLogin}</td>
						<td class="{$packet.trClass}"><a href="?a=packetEdit&amp;id={$packet.id}"><b>{$packet.name}</b></a>{if $packet.isFinished && $packet.finishDirExists} [<a href="{$finishDir}" target="_blank">dir</a>]{/if}</td>
						<td class="{$packet.trClass}">{$packet.ctime}</td>
						<td class="{$packet.trClass}">{$packet.stime}</td>
						<td class="{$packet.trClass}">{$packet.ftime}</td>
						<td class="{$packet.trClass}"><div id="{$packet.progressBarId}" class="progressBar"></div></td>
						<td class="{$packet.trClass}"><div id="packetStatus{$packet.id}">{$packet.status}</div></td>
						<td class="{$packet.trClass}"><a href="?a=packetExportTxt&amp;id={$packet.id}">txt</a> <a href="?a=packetExportXml&amp;id={$packet.id}">xml</a></td>
						<td class="{$packet.trClass}" align="center">{if $packet.isOwnedByUser || $packet.userIsSuperuser}<span id="packetArchiveExecButton{$packet.id}" class="ui-state-default ui-icon ui-icon-circle-minus" onClick="packetArchiveExec({$packet.id}, '{$packet.name}');"></span>{else}&nbsp;{/if}</td>
					</tr>
				{/foreach}
				
			</table>
		</td>
	</tr>
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF {$smarty.template} //-->