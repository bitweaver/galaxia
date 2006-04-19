{*Smarty template*}
<a class="pagetitle" href="tiki-g-map_roles.php?pid={$pid}">{tr}Map process roles{/tr}</a><br /><br />

<a href="{$smarty.const.GALAXIA_PKG_URL}admin/admin_processes.php">{tr}admin processes{/tr}</a>
<a href="tiki-g-admin_activities.php?pid={$pid}">{tr}admin activities{/tr}</a>
<a href="tiki-g-admin_roles.php?pid={$pid}">{tr}admin roles{/tr}</a>
<a href="{$smarty.const.GALAXIA_PKG_URL}admin/admin_processes.php?pid={$pid}">{tr}edit this process{/tr}</a><br /><br />

{tr}Process:{/tr} {$proc_info.name} {$proc_info.version}<br />

process graph<br />

{if count($errors) > 0}
<div class="wikibody">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

{if count($roles) > 0}
	<h3>{tr}Map users to roles{/tr}</h3>
	<form method="post" action="tiki-g-map_roles.php">
	<input type="hidden" name="pid" value="{$pid|escape}" />
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<table class="panel">
	<tr>
		<td>{tr}Map{/tr}</td><td>
		  <table border="1">
		  	<tr>
		  		<td>{tr}Users{/tr}:
				<input type="text" size="10" name="find_users" value="{$find_users|escape}" />&nbsp;
				<input type="submit" name="findusers" value="{tr}filter{/tr}" />
		  		</td>
		  		<td>{tr}Roles{/tr}:</td>
		  	</tr>
		  	<tr>
		  		<td>
					<select name="user[]" multiple="multiple" size="10">
					{section name=ix loop=$users}
					<option value="{$users[ix].user|escape}">{$users[ix].user}</option>
					{/section}
					</select>
		  		</td>
		  		<td>
					<select name="role[]" multiple="multiple" size="10">
					{section name=ix loop=$roles}
					<option value="{$roles[ix].role_id|escape}">{$roles[ix].name}</option>
					{/section}
					</select>	  		
		  		</td>
		  	</tr>
		  </table>
		</td>
	</tr><tr class="panelsubmitrow">
		<td colspan="2">
			<input type="submit" name="save" value="{tr}map{/tr}" />
		</td>
	</tr>
	</table>
	</form>
{else}
	<h3>{tr}Warning{/tr}</h3>
	{tr}No roles are defined{/tr}<br />
{/if}

<h3>{tr}List of mappings{/tr}</h3>
<form action="tiki-g-map_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find|escape}" />
<input type="submit" name="filter" value="{tr}find{/tr}" />
</form>

<form action="tiki-g-map_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />

<table>
<tr>
<th><input type="submit" name="delete" value="{tr}Delete{/tr}" /></th>
<th><a href="tiki-g-map_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></th>
<th><a href="tiki-g-map_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$items}
<tr class="{cycle}">
	<td><input type="checkbox" name="map[{$items[ix].user}:::{$items[ix].role_id}]" /></td>
	<td>{$items[ix].name}</td>
	<td>{$items[ix].user}</td>
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
{if $gBitSystem->isFeatureActive( 'site_direct_pagination' )}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:"$gBitSystem->getConfig('max_records')"}
<a href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
