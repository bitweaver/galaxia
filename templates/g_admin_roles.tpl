{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin process roles{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/process_nav.tpl"}
{if count($errors) > 0}
<div class="wikibody">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

<h3>{tr}Add or edit a role{/tr}</h3>
<div class="navbar above">
  <a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?pid={$pid}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;sort_mode2={$sort_mode2}&amp;find={$find}&amp;role_id=0">{tr}new{/tr}</a>
</div>

<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="role_id" value="{$info.role_id|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />

<table class="panel">
<tr>
  <td>{tr}name{/tr}</td>
  <td><input type="text" name="name" value="{$info.name|escape}" /></td>
</tr>
<tr>
  <td>{tr}description{/tr}</td>
  <td><textarea name="description" rows="4" cols="60">{$info.description|escape}</textarea></td>
</tr>
<tr class="panelsubmitrow">
  <td colspan="2"><input type="submit" name="save" value="{tr}save{/tr}" /> </td>
</tr>
</table>
</form>

<h3>{tr}Process roles{/tr}</h3>
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php" method="post">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="role_id" value="{$info.role_id|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />

<table>
<tr>
<th style="text-align: center;"><input type="submit" name="delete" value="{tr}Delete{/tr} " /></th>
<th style="text-align: center;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></th>
<th style="text-align: center;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$items item=proc}
<tr class="{cycle}">
	<td style="text-align: center;"><input type="checkbox" name="role[{$proc.role_id}]" /></td>
	<td style="text-align: center;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?sort_mode={$sort_mode}&amp;offset={$offset}&amp;find={$find}&amp;pid={$pid}&amp;sort_mode2={$sort_mode2}&amp;role_id={$proc.role_id}">{$proc.name}</a></td>
	<td style="text-align: center;">{$proc.description}</td>
</tr>
{foreachelse}
<tr class="norecords">
	<td class="{cycle advance=false}" colspan="3">
	{tr}No roles defined yet{/tr}
	</td>
</tr>	
{/foreach}
</table>
</form>	

{if count($roles) > 0}
	<h3>{tr}Map groups to roles{/tr}</h3>
	<form method="post" action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php">
	<input type="hidden" name="pid" value="{$pid|escape}" />
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<table class="panel">
	<tr>
		<td>{tr}Map{/tr}</td>
		<td>
		  <table border="1">
		  	<tr>
		  		<td>
		  		{tr}Groups{/tr}:
				<input type="text" size="10" name="find_groups" value="{$find_groups|escape}" />&nbsp;
				<input type="submit" name="findgroups" value="{tr}filter{/tr}" />	  
		  		</td>
		  		<td>
	  			{tr}Roles{/tr}:<br />		  		
		  		</td>
		  	</tr>
		  	<tr>
		  		<td>
					<select name="group[]" multiple="multiple" size="10">
					{foreach from=$groups key=group_id item=group}
					<option value="{$group_id|escape}">{$group.group_name|adjust:30}</option>
					{/foreach}
					</select>
		  		</td>
		  		<td>

					<select name="role[]" multiple="multiple" size="10">
					{section name=ix loop=$roles}
					<option value="{$roles[ix].role_id|escape}">{$roles[ix].name|adjust:30}</option>
					{/section}
					</select>	  		
		  		</td>
		  	</tr>
		  </table>
		</td>
	</tr>
	
	<tr class="panelsubmitrow">
		<td colspan="2">
			<input type="submit" name="save_map" value="{tr}map{/tr}" />
		</td>
	</tr>
	</table>
	</form>
{else}
	<h3>{tr}Warning{/tr}</h3>
	<div class="norecords">{tr}No roles are defined yet so no roles can be mapped{/tr}</div><br />
{/if}

<h3>{tr}List of mappings{/tr}</h3>
<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find|escape}" />
<input type="submit" name="filter" value="{tr}find{/tr}" />
</form>

<form action="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />

<table>
<tr>
<th style="text-align: center;"><input type="submit" name="delete_map" value="{tr}Delete{/tr}" /></th>
<th style="text-align: center;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></th>
<th style="text-align: center;"><a href="{$gBitLoc.GALAXIA_PKG_URL}admin/g_admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'group_name_desc'}group_name_asc{else}group_name_desc{/if}">{tr}Group{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$mapitems}
<tr>
	<td class="{cycle advance=false}" style="text-align: center;">
		<input type="checkbox" name="map[{$mapitems[ix].group_id}:::{$mapitems[ix].role_id}]" />
	</td>
	<td class="{cycle advance=false}" style="text-align: center;">
	  {$mapitems[ix].name}
	</td>
	<td class="{cycle}" style="text-align: center;">
	  {$mapitems[ix].group_name}
	</td>
</tr>
{sectionelse}
<tr class="norecords">
	<td colspan="3">
	{tr}No mappings defined yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>

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
<a href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div> {* end .workflow *}
