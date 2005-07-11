{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Monitor instances{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="body">

<h2>{tr}List of instances{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr>
<th>{tr}find{/tr}</th>
<th>{tr}proc{/tr}</th>
<th>{tr}act{/tr}</th>
<th>{tr}status{/tr}</th>
<th>{tr}act status{/tr}</th>
<th>{tr}creator{/tr}</th>
<th>&nbsp;</th>	
</tr>
<tr>
<td><input size="8" type="text" name="find" value="{$find|escape}" /></td>
<td><select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {foreach from=$all_procs item=proc}
	<option {if $proc.p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.p_id|escape}">{$proc.name} {$proc.version}</option>
	{/foreach}
	</select>
</td>
<td><select name="filter_activity">
	<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{foreach from=$all_acts item=act}
	<option {if $act.activity_id eq $smarty.request.filter_activity}selected="selected"{/if} value="{$act.activity_id|escape}">{$act.name}</option>
	{/foreach}
	</select>
</td>
<td>
	<select name="filter_status">
	<option {if '' eq $smarty.request.filter_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$statuses name=ix}
	<option {if $types[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]|escape}">{$statuses[ix]}</option>
	{/section}
	</select>
</td>
<td>
	<select name="filter_act_status">
	<option {if '' eq $smarty.request.filter_act_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="running" {if 'y' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}running{/tr}</option>
	<option value="completed" {if 'n' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}completed{/tr}</option>
	</select>
</td><td>
	<select name="filter_owner">
	<option {if $smarty.request.filter_owner eq ''}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$users name=ix}
	<option {if $users[ix] eq $smarty.request.filter_owner}selected="selected"{/if} value="{$users[ix]|escape}">{displayname user_id=$users[ix]}</option>
	{/section}
	</select>
</td><td><input type="submit" name="filter" value="{tr}filter{/tr}" /></td>
</tr>
</table>
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th><a href="{if $sort_mode eq 'instance_id_desc'}{sameurl sort_mode='instance_id_asc'}{else}{sameurl sort_mode='instance_id_desc'}{/if}">{tr}ID{/tr}</a></th>
<th><a href="{if $sort_mode eq 'insName_desc'}{sameurl sort_mode='insName_asc'}{else}{sameurl sort_mode='insName_desc'}{/if}">{tr}Name{/tr}</a></th>
<th><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></th>
<th><a href="{if $sort_mode eq 'started_desc'}{sameurl sort_mode='started_asc'}{else}{sameurl sort_mode='started_desc'}{/if}">{tr}Started{/tr}</a></th>
<th><a href="{if $sort_mode eq 'ended_desc'}{sameurl sort_mode='ended_asc'}{else}{sameurl sort_mode='ended_desc'}{/if}">{tr}Ended{/tr}</a></th>
<th><a href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Status{/tr}</a></th>
<th><a href="{if $sort_mode eq 'owner_desc'}{sameurl sort_mode='owner_asc'}{else}{sameurl sort_mode='owner_desc'}{/if}">{tr}User{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$items item=proc}
<tr>
	<td class="{cycle advance=false}" style="text-align:center;">
	<a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php?iid={$proc.instance_id}">{$proc.instance_id}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
	<a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_instance.php?iid={$proc.instance_id}">{$proc.insName}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.name}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.status}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.started|date_format}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{if $proc.ended eq 0} {tr}Not ended{/tr} {else} {$proc.ended|date_format} {/if}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{displayname user_id=$proc.owner_id}
	</td>
</tr>
{foreachelse}
<tr class="norecords">
	<td class="{cycle advance=false}" colspan="6">
	{tr}No instances created yet{/tr}
	</td>
</tr>	
{/foreach}
</table>
</form>
{* END OF LISTING *}

</div> {* end .body *}

{* PAGINATION *}
<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{* END OF PAGINATION *}

</div> {* end .workflow *}
