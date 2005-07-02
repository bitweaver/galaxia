{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin processes{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="navbar above">
  <a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;pid=0">{tr}new{/tr}</a>
</div>

<div class="body">

<h2>{tr}Add or edit a process{/tr}</h2>

{if $pid > 0}
  {include file="bitpackage:Galaxia/process_nav.tpl"}
{/if}
{if $pid > 0 and count($errors)}
<div class="boxcontent">
{tr}This process is invalid{/tr}:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div> {* end .boxcontent *}
{/if}

<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php" method="post">
<input type="hidden" name="version" value="{$info.version|escape}" />
<input type="hidden" name="pid" value="{$info.p_id|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="panel">
	<tr>
		<td>{tr}Process Name{/tr}</td>
		<td><input type="text" maxlength="80" name="name" value="{$info.name|escape}" /> {tr}ver:{/tr}{$info.version}</td>
	</tr>
	<tr>
	 	<td>{tr}Description{/tr}</td>
	 	<td><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
	</tr>
	<tr>
		<td><a {popup text="$is_active_help"}>{tr}is active?{/tr}</a></td>
		<td><input type="checkbox" name="is_active" {if $info.is_active eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="panelsubmitrow"><td colspan="2">
		<input type="submit" name="save" value="{if $pid > 0}{tr}update{/tr}{else}{tr}create{/tr}{/if}" />
	</td></tr>
</table>
</form>

<h2>{tr}Or upload a process using this form{/tr}</h2>
<form enctype="multipart/form-data" action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php" method="post">
<table class="panel">
<tr>
  <td>{tr}Upload file{/tr}:</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" />
<input size="16" name="userfile1" type="file" />&nbsp;<input type="submit" name="upload" value="{tr}upload{/tr}" /></td>
</tr>
</table>
</form>


<h2>{tr}List of processes{/tr} ({$cant})</h2>
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php" method="post">
<table class="find"><tr><td>
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}</td><td><input size="8" type="text" name="find" value="{$find|escape}" />
</td><td>{tr}Process{/tr}</td><td>
<select name="filter_name">
<option value="">{tr}All{/tr}</option>
{section loop=$all_procs name=ix}
<option  value="{$all_procs[ix].name|escape}">{$all_procs[ix].name}</option>
{/section}
</select>
</td><td>
{tr}Status{/tr}</td><td>
<select name="filter_active">
<option value="">{tr}All{/tr}</option>
<option value="y">{tr}Active{/tr}</option>
<option value="n">{tr}Inactive{/tr}</option>
</select>
</td><td>
<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td></tr></table>
</form>

<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th>&nbsp;</th>
<th style="text-align:left;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
<th><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}">{tr}Version{/tr}</a></th>
<th><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'is_active_desc'}is_active_asc{else}is_active_desc{/if}">{tr}Active{/tr}</a></th>
<th><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'is_valid_desc'}is_valid_asc{else}is_active_desc{/if}">{tr}Valid{/tr}</a></th>
<th>{tr}Action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$items}
<tr class="{cycle}">
	<td style="text-align:center;">
		<input type="checkbox" name="process[{$items[ix].p_id}]" /></td><td>
		<a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;pid={$items[ix].p_id}">{$items[ix].name}</a></td><td style="text-align:center;">
		{$items[ix].version}</td><td style="text-align:center;">
	  {if $items[ix].is_active eq 'y'}
	  {biticon ipackage="Galaxia" iname="refresh2" iclass="icon" iexplain="active process"}
	  {else}
	  &nbsp;{$items[ix].is_active}
	  {/if}
	</td><td style="text-align:center;">
	  {if $items[ix].is_valid eq 'n'}
	  {biticon ipackage="Galaxia" iname="red_dot" iexplain="invalid process" iclass="icon"}
	  {else}
	  {biticon ipackage="Galaxia" iname="green_dot" iexplain="valid process" iclass="icon"}
	  {/if}
	</td><td style="text-align:center;">
	  <a title="{tr}activities{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_activities.php?pid={$items[ix].p_id}">{biticon ipackage="Galaxia" iname="Activity" iexplain="activities" class="icon"}</a>
	  <a title="{tr}code{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_shared_source.php?pid={$items[ix].p_id}">{biticon ipackage="Galaxia" iname="book" iexplain="code" class="icon"}</a>
	  <a title="{tr}roles{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?pid={$items[ix].p_id}">{biticon ipackage="Galaxia" iname="myinfo" iexplain="roles" class="icon"}</a>
	  <a title="{tr}export{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_save_process.php?pid={$items[ix].p_id}">{biticon ipackage="Galaxia" iname="export" iexplain="export" class="icon"}</a>
<br />
	  <a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;newminor={$items[ix].p_id}">{tr}new minor{/tr}</a><br />
	  <a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;newmajor={$items[ix].p_id}">{tr}new major{/tr}</a><br />
	</td></tr>
{sectionelse}
<tr class="norecords"><td colspan="15">
	{tr}No processes defined yet{/tr}
</td></tr>	
{/section}
<tr><td colspan="15"><input type="submit" name="delete" value="Delete" /></td></tr>
</table>
</form>

</div> {* end .body *}

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
