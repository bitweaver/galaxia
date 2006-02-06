{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Monitor activities{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="body">

<h2>{tr}List of activities{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form id="filterf" action="{$smarty.const.GALAXIA_PKG_URL}g_monitor_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr>
<th>{tr}find{/tr}</th>
<th>{tr}proc{/tr}</th>
<th>{tr}act{/tr}</th>
<th>{tr}type{/tr}</th>
<th>{tr}inter{/tr}</th>
<th>{tr}auto{/tr}</th>
<th>&nbsp;</th>
</tr>
<tr>
<td><input size="8" type="text" name="find" value="{$find|escape}" /></td>
<td><select name="filter_process" onchange="javascript:getElementById('filterf').submit();">
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
	<select name="filter_type">
	<option {if '' eq $smarty.request.filter_type}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$types name=ix}
	<option {if $types[ix] eq $smarty.request.filter_type}selected="selected"{/if} value="{$types[ix]|escape}">{$types[ix]}</option>
	{/section}
	</select>
</td><td>
	<select name="filter_is_interactive">
	<option {if '' eq $smarty.request.filter_is_interactive}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_is_interactive}selected="selected"{/if}>{tr}Interactive{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_is_interactive}selected="selected"{/if}>{tr}Automatic{/tr}</option>
	</select>
</td><td>
	<select name="filter_is_auto_routed">
	<option {if '' eq $smarty.request.filter_is_auto_routed}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_is_auto_routed}selected="selected"{/if}>{tr}Manual{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_is_auto_routed}selected="selected"{/if}>{tr}Automatic{/tr}</option>
	</select>
</td><td>	
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td></tr>
</table>
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$smarty.const.GALAXIA_PKG_URL}g_monitor_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th><a href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">{tr}Type{/tr}</a></th>
<th><a href="{if $sort_mode eq 'proc_desc'}{sameurl sort_mode='proc_asc'}{else}{sameurl sort_mode='proc_desc'}{/if}">{tr}Process{/tr}</a></th>
<th><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Name{/tr}</a></th>
<th><a href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">{tr}Type{/tr}</a></th>
<th><a href="{if $sort_mode eq 'is_interactive_desc'}{sameurl sort_mode='is_interactive_asc'}{else}{sameurl sort_mode='is_interactive_desc'}{/if}">{tr}int{/tr}</a></th>
<th><a href="{if $sort_mode eq 'is_auto_routed_desc'}{sameurl sort_mode='is_auto_routed_asc'}{else}{sameurl sort_mode='is_auto_routed_desc'}{/if}">{tr}routing{/tr}</a></th>
<th>{tr}Instances{/tr}*</th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$items item=act}
<tr>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.type|act_icon:$act.is_interactive}
	</td>

	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.procname} {$act.version}
	</td>

	<td class="{cycle advance=false}">
	  <a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?pid={$act.p_id}&amp;activity_id={$act.activity_id}">{$act.name}</a>
	  {if $act.type eq 'standalone'}
	  <a href="{$smarty.const.GALAXIA_PKG_URL}g_run_activity.php?activity_id={$act.activity_id}">{biticon ipackage="Galaxia" iname="next" iexplain="run activity"}</a>
	  {/if}
	  {if $act.type eq 'start'}
	  <a href="{$smarty.const.GALAXIA_PKG_URL}g_run_activity.php?activity_id={$act.activity_id}&amp;createInstance=1">{biticon ipackage="Galaxia" iname="next" iexplain="run activity"}</a>
	  {/if}
	</td>
  

	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.type}
	</td>
	
	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.is_interactive}
	</td>


	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.is_auto_routed}
	</td>
	
	<td class="{cycle}" style="text-align:right;">
		<table >
		<tr>
 		 <td style="text-align:right;"><a style="color:green;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$act.p_id}&amp;filter_status=active&amp;filter_activity={$act.activity_id}">{$act.active_instances}</a></td>
		 <td style="text-align:right;"><a style="color:black;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$act.p_id}&amp;filter_status=completed&amp;filter_activity={$act.activity_id}">{$act.completed_instances}</a></td>
		 <td style="text-align:right;"><a style="color:grey;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$act.p_id}&amp;filter_status=aborted&amp;filter_activity={$act.activity_id}">{$act.aborted_instances}</a></td>
		 <td style="text-align:right;"><a style="color:red;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$act.p_id}&amp;filter_status=exception&amp;filter_activity={$act.activity_id}">{$act.exception_instances}</a></td>

		</tr>
		</table>
	</td>
</tr>
{foreachelse}
<tr class="norecords">
	<td class="{cycle advance=false}" colspan="6">
	{tr}No processes defined yet{/tr}
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
[<a href="{sameurl offset=$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{sameurl offset=$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:`$gBitSystemPrefs.max_records`}
<a href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{* END OF PAGINATION *}

{include file="bitpackage:Galaxia/g_monitor_stats.tpl"}

</div> {* end .workflow *}
