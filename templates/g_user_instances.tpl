{popup_init src="`$gBitLoc.THEMES_PKG_URL`js/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}User instances{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/user_nav.tpl"}

<div class="body">

<h2>{tr}List of instances{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="find">
<tr>
<th>{tr}find{/tr}</th>
<th>&nbsp;</th>	
<th>{tr}status{/tr}</th>
<th>{tr}proc{/tr}</th>
<th>&nbsp;</th>	
<th>{tr}user{/tr}</th>
<th><!--{tr}act status{/tr}--></th>
<th>&nbsp;</th>	
</tr><tr>
<td>
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td><td>
	&nbsp;
</td><td>
	<select name="filter_status">
	<option {if '' eq $smarty.request.filter_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$statuses name=ix}
	<option {if $statuses[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]|escape}">{tr}{$statuses[ix]}{/tr}</option>
	{/section}
	</select>
</td><td>
	<select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].p_id|escape}">{$all_procs[ix].procname} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td><td>
<!--	<select name="filter_act_status">
	<option {if '' eq $smarty.request.filter_act_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="running" {if 'y' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}running{/tr}</option>
	<option value="completed" {if 'n' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}completed{/tr}</option>
	</select>-->
</td>
<td>
<select name="filter_user">
	<option {if $smarty.request.filter_user eq ''}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option {if $smarty.request.filter_user eq '*'}selected="selected"{/if} value="*">*</option>
	<option {if $smarty.request.filter_user eq $user_id}selected="selected"{/if} value="{$user_id|escape}">{displayname user_id=$user_id}</option>
	</select>
</td><td>
	&nbsp;
</td><td>
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

</div> {* end .body *}

{*LISTING*}
<form action="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="data">
<tr>
<th><a href="{if $sort_mode eq 'instance_id_desc'}{sameurl sort_mode='instance_id_asc'}{else}{sameurl sort_mode='instance_id_desc'}{/if}">{tr}Id{/tr}</a></th>
<th><a href="{if $sort_mode eq 'owner_desc'}{sameurl sort_mode='owner_asc'}{else}{sameurl sort_mode='owner_desc'}{/if}">{tr}Owner{/tr}</a></th>
<th><a href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Instance Status{/tr}</a></th>
<th><a href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></th>
<th><a href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></th>
<th><a href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></th>
<th><a href="{if $sort_mode eq 'actstatus_desc'}{sameurl sort_mode='actstatus_asc'}{else}{sameurl sort_mode='actstatus_desc'}{/if}">{tr}Activity status{/tr}</a></th>
<th>{tr}Action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$items}
<tr class="{cycle}">
	<td style="text-align:center;">{$items[ix].instance_id}</td>
	<td style="text-align:center;">{displayname user_id=$items[ix].owner_id}</td>
	<td style="text-align:center;">{$items[ix].status}</td>
	<td style="text-align:center;">{$items[ix].procname} {$items[ix].version}</td>
	<td style="text-align:center;">{$items[ix].type|act_icon:"$items[ix].is_interactive"} {$items[ix].name}</td>
	<td style="text-align:center;">{if $items[ix].user_id eq ''}*{else}{displayname user_id=$items[ix].user_id}{/if}</td>
	<td style="text-align:center;">{$items[ix].actstatus}</td>
	{*<td class="{cycle advance=false}">
	{if $items[ix].exptime eq 0}
	    {tr}Not defined{/tr}
	{else}
	  {$items[ix].exptime|bit_long_datetime"}
	{/if}
	</td>*}
	<td>{*actions*}<table>
	  <tr>
	  {*exception*}
      {if $gBitUser->hasPermission('bit_p_exception_instance')}
	  {if $items[ix].status ne 'aborted' and $items[ix].status ne 'exception' and $items[ix].user_id eq $user_id}
	  <td><a onclick="javascript:return confirm('{tr}Are you sure you want to exception this instance?{/tr}');" title="{tr}exception instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?exception=1&amp;iid={$items[ix].instance_id}&amp;aid={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="stop" iexplain="exception instance" iclass="icon"}</a></td>
	  {/if}
      {/if}
	  {if $items[ix].is_auto_routed eq 'n' and $items[ix].actstatus eq 'completed'}
	  {*send*}
	  <td><a title="{tr}send instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?send=1&amp;iid={$items[ix].instance_id}&amp;aid={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="linkto" iexplain="send instance" iclass="icon"}</a></td>
	  {/if}
	  {if $items[ix].is_interactive eq 'y' and $items[ix].status eq 'active'}
	  {*run*}
	  <td><a title="{tr}run instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_run_activity.php?iid={$items[ix].instance_id}&amp;activity_id={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="next" iexplain="run instance" iclass="icon"}</a></td>
	  {/if}
	  {*abort*}
      {if $gBitUser->hasPermission('bit_p_abort_instance')}
	  {if $items[ix].status ne 'aborted' and $items[ix].user_id eq $user_id}
	  <td><a onclick="javascript:return confirm('{tr}Are you sure you want to abort this instance?{/tr}');" title="{tr}abort instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?abort=1&amp;iid={$items[ix].instance_id}&amp;aid={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="trash" iexplain="abort instance" iclass="icon"}</a></td>
      {/if}
	  {/if}
	  {if $items[ix].user_id eq NULL and $items[ix].status eq 'active'}
	  {*grab*}
	  <td><a title="{tr}grab instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?grab=1&amp;iid={$items[ix].instance_id}&amp;aid={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="fix" iexplain="grab instance" iclass="icon"}</a></td>
	  {else}
	  {*release*}
	  {if $items[ix].status eq 'active'}
	  <td><a title="{tr}release instance{/tr}" href="{$gBitLoc.GALAXIA_PKG_URL}g_user_instances.php?release=1&amp;iid={$items[ix].instance_id}&amp;aid={$items[ix].activity_id}">{biticon ipackage="Galaxia" iname="float" iexplain="release instance" iclass="icon"}</a></td>
	  {/if}
	  {/if}
	  </tr>
	  </table>
	</td>

</tr>
{sectionelse}
<tr class="norecords">
	<td colspan="8">{tr}No instances defined yet{/tr}</td>
</tr>	
{/section}
</table>
</form>
{* END OF LISTING *}

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
<a href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div> {* end .workflow *}
