{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}User Activities{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/user_nav.tpl"}

<div class="body">

<h2>{tr}List of activities{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_activities.php" method="post" id="fform">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr>
<th style="text-align:left;">{tr}find{/tr}</th>
<th style="text-align:left;">{tr}process{/tr}</th>
<th style="text-align:left;">&nbsp;</th>
</tr>
<tr><td>
	<input size="15" type="text" name="find" value="{$find|escape}" />
</td><td>
	<select onchange="javascript:getElementById('fform').submit();" name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].p_id|escape}">{$all_procs[ix].procname} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td><td>
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td></tr>
</table>
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th style="text-align:left;"><a href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></th>
<th style="text-align:left;"><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></th>
<th><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Instances{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$items}
<tr class="{cycle}"><td>
	{$items[ix].procname} {$items[ix].version}</td><td>
	{$items[ix].type|act_icon:"$items[ix].is_interactive"} 
		{if $items[ix].instances > 0}
			<a href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?filter_process={$items[ix].p_id}&amp;filter_activity={$items[ix].activity_id}">{$items[ix].name}</a>
		{else}
			{if $items[ix].is_interactive eq 'y' and ($items[ix].type eq 'start' or $items[ix].type eq 'standalone')}
			<a title="{tr}run activity{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_run_activity.php?activity_id={$items[ix].activity_id}">
			{/if}
			{$items[ix].name}
		{/if}
		{if $items[ix].is_interactive eq 'y' and ($items[ix].type eq 'start' or $items[ix].type eq 'standalone')}
			{biticon ipackage="Galaxia" iname="next" iexplain="run activity" iclass="icon"}</a>		  
		{/if}
	</td><td style="text-align:center;">
		{$items[ix].instances}
	</td>
</tr>
{sectionelse}
<tr class="norecords">
	<td colspan="3">{tr}No processes defined yet{/tr}</td>
</tr>
{/section}
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

</div> {* end .workflow *}