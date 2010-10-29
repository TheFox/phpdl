<!-- BEGIN OF default.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	function packetDel(id){
		var button = $('#button' + id);
		button.hide();
		$.ajax({
			type: 'GET',
			url: '?a=packetDel&id=' + id + '&noredirect=1',
			success: function(){
				$('#tr' + id).hide();
				$('#tr' + id).remove();
				
				var div = $('<div>');
				div.css('background-color', '#00cc00');
				div.html('Packet (' + id + ') successfully deleted.');
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

<table border="1">
	<tr><td colspan="8"><div id="status"></div></td></tr>
	<tr>
		<td>id</td>
		<td>user</td>
		<td>name</td>
		<td>ctime</td>
		<td>stime</td>
		<td>ftime</td>
		<td>status</td>
		<td>del</td>
	</tr>
	{$stack}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF default.tpl //-->