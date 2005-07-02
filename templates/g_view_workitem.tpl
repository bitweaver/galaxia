{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Browsing Workitem{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl}
<h3>{tr}Workitem information{/tr}</h3>
<table class="panel">
<tr>
	<td><b>id</b></td>
	<td>{$wi.item_id}</td>
</tr>
<tr>
	<td><b>#</b></td>
	<td>{$wi.order_id}</td>
</tr>
<tr>
	<td><b>Process</b></td>
	<td>{$wi.procname} {$wi.version}</td>
</tr>
<tr>
	<td><b>Activity</b></td>
	<td>{$wi.type|act_icon:"$wi.is_interactive"} {$wi.name}</td>
</tr>
<tr>
	<td><b>User</b></td>
	<td>{$wi.user}</td>
</tr>
<tr>
	<td><b>Started</b></td>
	<td>{$wi.started|bit_long_datetime}</td>
</tr>
<tr>
	<td><b>Duration</b></td>
	<td>{$wi.duration|duration}</td>
</tr>

</table>
<h3>{tr}Properties{/tr}</h3>
<table class="panel">
<tr>
	<th>{tr}Property{/tr}</th>
	<th>{tr}Value{/tr}</th>
</tr>
{foreach from=$wi.properties item=item key=key}
<tr>
	<td><b>{$key}</b></td>
	<td>{$item}</td>
</tr>
{/foreach}
</table>
