{popup_init src="`$smarty.const.THEMES_PKG_URL`js/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin instance{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}
<h3>{tr}Instance Name{/tr}: {$ins_info.name} {$ins_info.instance_id} (Process: {$proc_info.name} {$proc_info.version})</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<input type="hidden" name="aid" value="{$aid|escape}" />
<table class="panel">
<tr>
	<td>{tr}Instance{/tr}</td>
	<td><input type="text" name="name" value="{$ins_info.name}"></td>
</tr>
<tr>
	<td>{tr}Created{/tr}</td>
	<td>{$ins_info.started|bit_long_datetime}</td>
</tr>
<tr>
	<td>{tr}Workitems{/tr}</td>
	<td><a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_monitor_workitems.php?filter_instance={$ins_info.instance_id}">{$ins_info.workitems}</a></td>
</tr>
<tr>
	<td>{tr}Status{/tr}</td>
	<td>
	<select name="status">
		<option value="active" {if $ins_info.status eq 'active'}selected="selected"{/if}>{tr}active{/tr}</option>
		<option value="exception" {if $ins_info.status eq 'exception'}selected="selected"{/if}>{tr}exception{/tr}</option>
		<option value="completed" {if $ins_info.status eq 'completed'}selected="selected"{/if}>{tr}completed{/tr}</option>
		<option value="aborted" {if $ins_info.status eq 'aborted'}selected="selected"{/if}>{tr}aborted{/tr}</option>
	</select>
</td></tr>
<tr><td>
{tr}Owner{/tr}</td><td>
	<select name="owner">
	{section name=ix loop=$users}
	<option value="{$users[ix].user_id|escape}" {if $users[ix].user_id eq $ins_info.owner_id}selected="selected"{/if}>{displayname user_id=$users[ix].user_id}</option>
	{/section}
	</select>
</td></tr>
<tr><td>
{tr}Send all to{/tr}</td><td>
	<select name="sendto">
	  <option value="">{tr}Don't move{/tr}</option>
	  {section loop=$activities name=ix}
	  <option value="{$activities[ix].activity_id|escape}">{$activities[ix].name}</option>
	  {/section}
	</select>
</td></tr>
<tr><td>
{tr}Activities{/tr}</td><td>
		{if count($acts)}
		<table>
		<tr><th>
			{tr}Name{/tr}</th><th>
			{tr}Started{/tr}</th><th>
			{tr}Act status{/tr}</th><th>
			{tr}Expiration Date{/tr}</th><th>
			{tr}Ended{/tr}</th><th>
			{tr}User{/tr}</th>
		</tr>
		{foreach item=act from=$acts}
		<tr class="odd"><td>
			<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance_activity.php?iid={$iid}&aid={$act.activity_id}">{$act.name}</a></td><td>
			{$act.ia_started|bit_long_datetime}</td><td>
			{$act.actstatus}</td><td>
			{if $act.exptime eq 0 && $act.type eq 'activity' && $act.isInteractive eq 'y'}{tr}Not Defined{/tr}{elseif $act.type != 'activity'}&lt;{$act.type}&gt;{elseif $act.isInteractive eq 'n'}{tr}Not Interactive{/tr}{else}{$act.exptime|bit_long_datetime}{/if}</td><td>
			{if $act.ended eq 0}{tr}Not Ended{/tr}{else}{$act.ended|bit_long_datetime}{/if}</td><td>
			<select name="acts[{$act.activity_id}]">
				<option value="" {if $act.user_id eq ''}selected='selected'{/if}>{tr}Any{/tr}</option>
			{section name=ix loop=$users}
				<option value="{$users[ix].user_id|escape}" {if $users[ix].user_id eq $act.user_id}selected="selected"{/if}>{displayname user_id=$users[ix].user_id}</option>
			{/section}
			</select>
		</td></tr>
		{/foreach}
		</table>
		{else}
		-
		{/if}
	</td>
</tr>
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" name="save" value="{tr}update{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Properties{/tr}</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<input type="hidden" name="aid" value="{$aid|escape}" />
<table>
<tr><th>
	{tr}Property{/tr}</th><th>
	{tr}Value{/tr}
</th></tr>
{foreach from=$props item=item key=key}
<tr class="odd"><td>
	<a title="{tr}delete{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance.php?iid={$iid}&amp;unsetprop={$key}">{biticon ipackage="Galaxia" iname="trash" iexplain="{tr}delete{/tr}"}</a>
	 <b>{$key}</b>
	 </td><td>
	{if strlen($item)>80}
	<textarea name="props[$key]" cols="80" rows="{$item|div:80:20}">{$item|escape}</textarea>
	{else}
	<input type="text" name="props[{$key}]" value="{$item|escape}" size="80" />
	{/if}
	</td>
</tr>
{/foreach}
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" name="saveprops" value="{tr}update{/tr}" />
</td></tr>
</table>
</form>

<h3>{tr}Add property{/tr}</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<input type="hidden" name="aid" value="{$aid|escape}" />
<table class="panel">
<tr>
	<td>{tr}name{/tr}</td>
	<td><input type="text" name="name" /></td>
</tr>
<tr>
	<td>{tr}value{/tr}</td>
	<td><textarea name="value" rows="4" cols="80"></textarea></td>
</tr>
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" name="addprop" value="{tr}add{/tr}" />
</td></tr>
</table>
</form>
</div>
</div>
