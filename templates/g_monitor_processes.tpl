{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Monitor processes{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="body">

<h2>{tr}List of processes{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr>
<th>{tr}Find{/tr}</th>
<th>{tr}Process{/tr}</th>
<th>{tr}Active{/tr}</th>
<th>{tr}Valid{/tr}</th>
<th>&nbsp;</th>	
</tr>
<tr>
<td><input size="8" type="text" name="find" value="{$find|escape}" /></td>
<td><select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{foreach from=$all_procs item=proc}
	<option {if $proc.p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.p_id|escape}">{$proc.name}</option>
	{/foreach}
	</select>
</td>
<td><select name="filter_active">
	<option {if '' eq $smarty.request.filter_active}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_active}selected="selected"{/if}>{tr}Active{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_active}selected="selected"{/if}>{tr}Inactive{/tr}</option>
	</select>
</td>
<td><select name="filter_valid">
	<option {if '' eq $smarty.request.filter_valid}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option {if 'y' eq $smarty.request.filter_valid}selected="selected"{/if} value="y">{tr}Valid{/tr}</option>
	<option {if 'n' eq $smarty.request.filter_valid}selected="selected"{/if} value="n">{tr}Invalid{/tr}</option>
	</select>
</td>
<td><input type="submit" name="filter" value="{tr}filter{/tr}" /></td>
</tr>
</table>
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th style="text-align:left;"><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Name{/tr}</a></th>
<th>{tr}Activities{/tr}</th>
<th><a href="{if $sort_mode eq 'is_active_desc'}{sameurl sort_mode='is_active_asc'}{else}{sameurl sort_mode='is_active_desc'}{/if}">{tr}Active{/tr}</a></th>
<th><a href="{if $sort_mode eq 'is_valid_desc'}{sameurl sort_mode='is_valid_asc'}{else}{sameurl sort_mode='is_valid_desc'}{/if}">{tr}Valid{/tr}</a></th>
<th>{tr}Instances{/tr}*</th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$items item=proc}
<tr class="{cycle}">
	<td style="text-align:left;">
	<a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?pid={$proc.p_id}">{$proc.name} {$proc.version}</a>
	</td><td style="text-align:center;">
		<a href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_activities.php?filter_process={$proc.p_id}">{$proc.activities}</a>
	</td><td style="text-align:center;">
	  {if $proc.is_active eq 'y'}
	  {biticon ipackage="Galaxia" iname="refresh2" iclass="icon" iexplain="active process"}
	  {else}
	  {$proc.is_active}
	  {/if}
	</td><td style="text-align:center;">
	  {if $proc.is_valid eq 'n'}
	  {biticon ipackage="Galaxia" iname="red_dot" iclass="icon" iexplain="invalid process"}
	  {else}
	  {biticon ipackage="Galaxia" iname="green_dot" iclass="icon" iexplain="valid process"}
	  {/if}
	</td><td style="text-align:right;">
		<table>
		<tr>
		 <td style="text-align:right;"><a style="color:green;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=active">{$proc.active_instances}</a></td>
		 <td style="text-align:right;"><a style="color:black;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=completed">{$proc.completed_instances}</a></td>
		 <td style="text-align:right;"><a style="color:grey;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=aborted">{$proc.aborted_instances}</a></td>
		 <td style="text-align:right;"><a style="color:red;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=exception">{$proc.exception_instances}</a></td>
		</tr>
		</table>
	</td>
</tr>
{foreachelse}
<tr class="norecords"><td colspan="6">{tr}No processes defined yet{/tr}</td></tr>	
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
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{sameurl offset=$selector_offset}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div> 
{* END OF PAGINATION *}

{include file="bitpackage:Galaxia/g_monitor_stats.tpl"}

</div> {* end .workflow *}
