{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}User processes{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/user_nav.tpl"}

<div class="body">

<h2>{tr}List of processes{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr><th style="text-align:left;">{tr}Find{/tr}</th>
<th>&nbsp;</th>
</tr><tr>
<td><input size="8" type="text" name="find" value="{$find|escape}" /></td>
<td><input type="submit" name="filter" value="{tr}filter{/tr}" /></td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th><a href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></th>
<th>{tr}Activities{/tr}</th>
<th>{tr}Instances{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$items}
<tr class="{cycle}">
	<td style="text-align:left;">
	  <a href="{$gBitLoc.GALAXIA_PKG_URL}g_user_activities.php?filter_process={$items[ix].p_id}">{$items[ix].procname} {$items[ix].version}</a>
	</td>
	<td style="text-align:center;">
		<a href="{$gBitLoc.GALAXIA_PKG_URL}g_user_activities.php?filter_process={$items[ix].p_id}">{$items[ix].activities}</a>
	</td>
	<td style="text-align:center;">
		<a href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?filter_process={$items[ix].p_id}">{$items[ix].instances}</a>
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

</div> {* end .workflow *}
