{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin instance activity{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}
<h3>{tr}Instance Name{/tr}: {$ins_info.name} {$ins_info.instance_id} (Process: {$proc_info.name} {$proc_info.version})</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance_activity.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<input type="hidden" name="aid" value="{$aid|escape}" />
<table class="panel">
<tr>
	<td>{tr}Activity{/tr}</td>
	<td><a href="{$smarty.const.GALAXIA_PKG_URL}g_run_activity.php?iid={$iid}&amp;activity_id={$aid}">{$acts.name} {if $acts.actstatus eq 'running'}{biticon ipackage="Galaxia" iname="next" iexplain="run activity"}</a>{/if}</td>
</tr>
<tr>
	<td>{tr}Instance{/tr}</td>
	<td>{if $acts.actstatus eq 'running'}<input type="text" name="name" value="{$ins_info.name}">{else}{$ins_info.name}{/if}</td>
</tr>
<tr>
	<td>{tr}Created{/tr}</td>
	<td>{$acts.ia_started|bit_long_datetime}</td>
</tr>
<tr>
	<td>{tr}Expiration Date{/tr}</td>
	<td>{if $acts.exptime eq 0 && $acts.type eq 'activity' && $acts.isInteractive eq 'y'}{tr}Not Defined{/tr}{elseif $acts.type != 'activity'}&lt;{$acts.type}&gt;{elseif $acts.isInteractive eq 'n'}{tr}Not Interactive{/tr}{else}{$acts.exptime|bit_long_datetime}{/if}</td>
</tr>
<tr>
	<td>{tr}Ended{/tr}</td>
	<td>{if $acts.ended eq 0}-{else}{$acts.ended|bit_long_datetime}{/if}</td>
<tr>
	<td>{tr}Status{/tr}</td>
	<td>{tr}{$acts.actstatus}{/tr}</td>
</tr>
<tr>
	<td>{tr}User{/tr}</td>
	<td>
	{if $acts.actstatus eq 'running'}
	<select name="owner">
	{section name=ix loop=$users}
	<option value="{$users[ix].user_id|escape}" {if $users[ix].user_id eq $acts.user_id}selected="selected"{/if}>{displayname user_id=$users[ix].user_id}</option>
	{/section}
	</select>
	{else}
	{displayname user_id=$acts.user_id}
	{/if}
	</td>
</tr>
<tr class="panelsubmitrow"><td colspan="2">
	{if $acts.actstatus eq 'running'}<input type="submit" class="btn" name="save" value="{tr}update{/tr}" />{/if}
</td></tr>
</table>
</form>

<h3>{tr}Comments{/tr}</h3>
{section name=ix loop=$comments}
<table class="box">
<tr>
    	<td>{tr}From{/tr}:</td>
	<td>{displayname user_id=$comments[ix].user_id}</td>
</tr><tr>
    	<td>{tr}Date{/tr}:</td>
	<td colspan="3">{$comments[ix].com_timestamp|bit_long_datetime}</td>
</tr><tr>
	<td>{tr}Subject{/tr}:</td>
	<td colspan="3">{$comments[ix].title}</td>
</tr><tr>
	<td>{tr}Body{/tr}:</td>
	<td colspan="3">{$comments[ix].comment}</td>
</tr><tr>
	<td class="panelsubmitrow">
	<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance_activity.php" method="post">
		<input type="submit" class="btn" name="eraser" value="{tr}erase{/tr}" />
		<input type="hidden" name="__removecomment" value="{$comments[ix].cId}" />
		<input type="hidden" name="iid" value="{$iid|escape}" />
		<input type="hidden" name="aid" value="{$aid|escape}" />
	</form></td>
</tr>
</table>
{/section}

<h3>{tr}Answer{/tr}:</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance_activity.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<input type="hidden" name="aid" value="{$aid|escape}" />
<table class="panel">
<tr>
	<td>{tr}Subject{/tr}:</td>
	<td><input type="text" name="__title"></td>
</tr>
<tr>
	<td>{tr}Body{/tr}:</td>
	<td><textarea rows="5" cols="50" name="__comment"></textarea></td>
</tr>
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" class="btn" name="answer" value="{tr}Save{/tr}"/></td>
</tr>
</table>
<input type="hidden" name="__post" value="y" />
</form>
</div>
</div>
