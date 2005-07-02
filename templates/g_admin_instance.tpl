{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin instance{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}
<h3>{tr}Instance{/tr}: {$ins_info.instance_id} (Process: {$proc_info.name} {$proc_info.version})</h3>
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="panel">
<tr>
	<td>{tr}Created{/tr}</td>
	<td>{$ins_info.started|bit_long_date}</td>
</tr>
<tr>
	<td>{tr}Workitems{/tr}</td>
	<td><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_monitor_workitems.php?filter_instance={$ins_info.instance_id}">{$ins_info.workitems}</a></td>
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
	<option value="{$users[ix].user|escape}" {if $users[ix].user eq $ins_info.owner}selected="selected"{/if}>{$users[ix].user}</option>
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
			{tr}Activity{/tr}</th><th>
			{tr}Status{/tr}</th><th>
			{tr}User{/tr}</th>
		</tr>
		{section name=ix loop=$acts}
		<tr class="odd"><td>
			{$acts[ix].name}
			{if $acts[ix].is_interactive eq 'y'}
			<a title="{tr}run{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_run_activity.php?activity_id={$acts[ix].activity_id}&amp;iid={$iid}">{biticon ipackage="Galaxia" iname="next" iexplain="{tr}run{/tr}"}</a>
			{/if}
			</td><td>
{$acts[ix].actstatus}</td><td>
			<select name="acts[{$acts[ix].activity_id}]">
				<option value="*" value="*" {if $acts[ix].user eq '*'}selected='selected'{/if}>*</option>
			{section name=ix loop=$users}
				<option value="{$users[ix].user|escape}" {if $users[ix].user eq $acts[ix].user}selected="selected"{/if}>{$users[ix].user}</option>
			{/section}
			</select>
		</td></tr>
		{/section}
		</table>
		{else}
		&nbsp;
		{/if}
	</td>
</tr>
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" name="save" value="{tr}update{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Properties{/tr}</h3>
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table>
<tr><th>
	{tr}Property{/tr}</th><th>
	{tr}Value{/tr}
</th></tr>
{foreach from=$props item=item key=key}
<tr class="odd"><td>
	<a title="{tr}delete{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php?iid={$iid}&amp;unsetprop={$key}">{biticon ipackage="Galaxia" iname="trash" iexplain="{tr}delete{/tr}"}</a>
	 <b>{$key}</b>
	 </td><td>
	{if strlen($item)>80}
	<textarea name="props[$key]" cols="80" rows="{$item|div:80:20}">{$item|escape}</textarea>
	{else}
	<input type="text" name="props[{$key}]" value="{$item|escape}" />
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
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
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
