{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Monitor workitems{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="body">

<h2>{tr}List of workitems{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$smarty.const.GALAXIA_PKG_URL}g_monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="filter_instance" value="{$filter_instance|escape}" />
<table class="find">
<tr><th>
	{tr}find{/tr}</th><th>
	{tr}proc{/tr}</th><th>
	{tr}act{/tr}</th><th>
	{tr}instance{/tr}</th><th>
	{tr}user{/tr}</th><th>
	&nbsp;
</th></tr>
<tr><td>
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td><td>
	<select name="filter_process">
		<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
		{foreach from=$all_procs item=proc}
			<option {if $proc.p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.p_id|escape}">{$proc.name} {$proc.version}</option>
		{/foreach}
	</select>
</td><td>
	<select name="filter_activity">
		<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
		{foreach from=$all_acts item=act}
			<option {if $act.activity_id eq $smarty.request.filter_activity}selected="selected"{/if} value="{$act.activity_id|escape}">{$act.name} {$act.version}</option>
		{/foreach}
	</select>
</td><td>
	<input type="text" name="filter_instance" value="{$smarty.request.filter_instance|escape}" size="4" />
</td><td>
	<select name="filter_user">
		{section loop=$users name=ix}
			{if $users[ix] eq ''}<option {if $smarty.request.filter_user eq ''}selected="selected"{/if} value="">{tr}All{/tr}</option>
			{else}<option {if $users[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]|escape}">{displayname user_id=$users[ix]}</option>{/if}
		{/section}
		<option {if $smarty.request.filter_user eq '*'}selected="selected"{/if} value="*">*</option>
	</select>
</td><td>	
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td></tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$smarty.const.GALAXIA_PKG_URL}g_monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
	<th><a href="{if $sort_mode eq 'item_id_desc'}{sameurl sort_mode='item_id_asc'}{else}{sameurl sort_mode='item_id_desc'}{/if}">{tr}ID{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'actname_desc'}{sameurl sort_mode='actname_asc'}{else}{sameurl sort_mode='actname_desc'}{/if}">{tr}Activity{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'instance_id_desc'}{sameurl sort_mode='instance_id_asc'}{else}{sameurl sort_mode='instance_id_desc'}{/if}">{tr}Instance{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'order_id_desc'}{sameurl sort_mode='order_id_asc'}{else}{sameurl sort_mode='order_id_desc'}{/if}">{tr}Priority{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'started_desc'}{sameurl sort_mode='started_asc'}{else}{sameurl sort_mode='started_desc'}{/if}">{tr}Start{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'duration_desc'}{sameurl sort_mode='duration_asc'}{else}{sameurl sort_mode='duration_desc'}{/if}">{tr}Time{/tr}</a></th>
	<th><a href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$items item=proc} 
<tr class="{cycle}"><td style="text-align:center;">
		<a href="{$smarty.const.GALAXIA_PKG_URL}g_view_workitem.php?item_id={$proc.item_id}">{$proc.item_id}</a></td><td style="text-align:center;">
		{$proc.procname} {$proc.version}</td style="text-align:center;"><td style="text-align:center;">
		{$proc.type|act_icon:"$proc.is_interactive"} {$proc.actname}</td><td style="text-align:center;">
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_instance.php?iid={$proc.instance_id}">{$proc.instance_id}</a></td><td style="text-align:center;">
		{$proc.order_id}</td><td style="text-align:center;">
		{$proc.started|bit_short_datetime}</td><td style="text-align:center;">
		{if $proc.duration eq 0}-{else}{$proc.duration|duration}{/if}</td><td style="text-align:center;">
		{if $proc.user_id eq ''}*{else}{displayname user_id=$proc.user_id}{/if}
</td></tr>
{foreachelse}
	<tr class="norecords"><td colspan="8">
		{tr}No instances created{/tr}
</td></tr>
{/foreach}
</table>
</form>
{* END OF LISTING *}

</div> {* end .body *}

{* PAGINATION *}
<div class="pagination">
{if $prev_offset >= 0}
[<a href="{sameurl offset=$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{sameurl offset=$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:`$gBitSystem->getPreference( 'max_records' )`}
<a href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{* END OF PAGINATION *}

{include file="bitpackage:Galaxia/g_monitor_stats.tpl"}

</div> {* end .workflow *}
