<!-- BEGIN OF scheduler.tpl //-->
{include file="{$siteStyleTplDir}/header.tpl"}

<script type="text/javascript">
	
	$(document).ready(function(){
		
		// autorefresh
		setTimeout(function(){
			document.location.href = '?a=scheduler';
		}, 1000 * 60);
		
	});
	
	function active(id){
		var checkbox = $('#active' + id);
		$.ajax({
			type: 'GET',
			url: '?a=schedulerActive&id=' + id + '&active=' + (checkbox.is(':checked') ? 1 : 0),
			success: function(){
				//alert('ok');
			}
		});
	}
	
</script>

<table border="0">
	<tr><td colspan="{$tableColspan}"><div id="status">{$status}</div></td></tr>
	<tr><td colspan="{$tableColspan}"><a href="?a=schedulerEdit&amp;id=0">Add</a> | <a href="?a=schedulerSort">Sort</a></td></tr>
	<tr><td colspan="{$tableColspan}">{$dateLong}</td></tr>
	<tr>
		<td>active</td>
		<td>move</td>
		<td>sortnr</td>
		<td>name</td>
		<!--<td>repeat</td>//-->
		<td>download</td>
		<td>invert</td>
		<td>activeDayTimeBegin</td>
		<td>activeDayTimeEnd</td>
	</tr>
	{$scheduler}
</table>

{include file="{$siteStyleTplDir}/footer.tpl"}
<!-- END OF scheduler.tpl //-->