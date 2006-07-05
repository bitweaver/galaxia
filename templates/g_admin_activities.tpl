{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin process activities{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/process_nav.tpl"}

<div class="body">

{if count($errors) > 0}
<div class="wikibody">
{tr}This process is invalid{/tr}:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

<h3>{tr}Add or edit an activity{/tr}</h3><br />
<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activity_id=0">{tr}New{/tr}</a><br /><br />

<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="panel">
<tr>
  <td>{tr}Name{/tr}</td>
  <td><input type="text" name="name" value="{$info.name|escape}" /></td>
</tr>
<tr>
  <td>{tr}Description{/tr}</td>
  <td><textarea name="description" rows="4" cols="50">{$info.description|escape}</textarea></td>
</tr>
<tr>  
  <td>{tr}Type{/tr}</td>
  <td>
  <select name="type">
  <option value="start" {if $info.type eq 'start'}selected="selected"{/if}>{tr}start{/tr}</option>
  <option value="end" {if $info.type eq 'end'}selected="selected"{/if}>{tr}end{/tr}</option>		  
  <option value="activity" {if $info.type eq 'activity'}selected="selected"{/if}>{tr}activity{/tr}</option>		  
  <option value="switch" {if $info.type eq 'switch'}selected="selected"{/if}>{tr}switch{/tr}</option>		  
  <option value="split" {if $info.type eq 'split'}selected="selected"{/if}>{tr}split{/tr}</option>		  
  <option value="join" {if $info.type eq 'join'}selected="selected"{/if}>{tr}join{/tr}</option>		  
  <option value="standalone" {if $info.type eq 'standalone'}selected="selected"{/if}>{tr}standalone{/tr}</option>		  
  </select>
  {tr}Interactive:{/tr} <input type="checkbox" name="is_interactive" {if $info.is_interactive eq 'y'}checked="checked"{/if} />
  {tr}auto routed:{/tr} <input type="checkbox" name="is_auto_routed" {if $info.is_auto_routed eq 'y'}checked="checked"{/if} />
  </td>
</tr>

<tr>
  <td class="formcolor">{tr}Expiration Time{/tr} </td>
  <td class="formcolor">
  {tr}Years{/tr}:
  <SELECT name="year" size ="1">
  	{html_options options=$years selected=$info.year}
  </SELECT>
  {tr}Months{/tr}:
  <SELECT name="month" size="1">
  	{html_options options=$months selected=$info.month}
  </SELECT>
  {tr}Days{/tr}:
  <SELECT name="day" size="1">
  	{html_options options=$days selected=$info.day}
  </SELECT>
  {tr}Hours{/tr}:
  <SELECT name="hour" size="1">
  	{html_options options=$hours selected=$info.hour}
  </SELECT>
  {tr}Minutes{/tr}:
  <SELECT name="minute" size="1">
  	{html_options options=$minutes selected=$info.minute}
  </SELECT>
  </td>
</tr>
<tr>
  <td>{tr}Add transitions{/tr}</td>
  <td>
    <table class="panel">
	<tr><td>
		{tr}Add transition from:{/tr}<br />
		<select name="add_tran_from[]" multiple="multiple" size="5">
			{section name=ix loop=$items}
				<option value="{$items[ix].activity_id|escape}" {if $items[ix].from eq 'y'}selected="selected"{/if}>{$items[ix].name|adjust:30}</option>
			{/section}			
		</select>
	</td><td>
		{tr}Add transition to:{/tr}<br />
		<select name="add_tran_to[]" multiple="multiple" size="5">
			{section name=ix loop=$items}
				<option value="{$items[ix].activity_id|escape}" {if $items[ix].to eq 'y'}selected="selected"{/if}>{$items[ix].name|adjust:30}</option>
			{/section}			
		</select>
	</td></tr>    
    </table>
  </td>
</tr>

<tr>
  <td>{tr}Roles{/tr}</td><td>
  {section name=ix loop=$roles}
  {$roles[ix].name}<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;where={$where}&amp;activity_id={$info.activity_id}&amp;pid={$pid}&amp;remove_role={$roles[ix].role_id}">{biticon ipackage="liberty" iname="delete_small" iexplain="{tr}delete{/tr}"}</a><br />
  {sectionelse}
<div class="norecords">{tr}No roles associated to this activity{/tr}</div>
  {/section}
  </td>
</tr>
<tr>
  <td>{tr}Add role{/tr}</td><td>
  {if count($all_roles)}
  <select name="userole">
  <option value="">{tr}add new{/tr}</option>
  {section loop=$all_roles name=ix}
  <option value="{$all_roles[ix].role_id|escape}">{$all_roles[ix].name}</option>
  {/section}
  </select>
  {/if}
  <input type="text" name="rolename" />&nbsp;<input type="submit" name="addrole" value="{tr}add role{/tr}" />
  </td>
</tr>
<tr class="panelsubmitrow">
  <td colspan="2"><input type="submit" name="save_act" value="{tr}save{/tr}" /> </td>
</tr>
</table>
</form>

<h3>{tr}Process activities{/tr}</h3>
<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<table class="panel">
<tr><td>
{tr}Find{/tr}</td><td>
{tr}Type{/tr}</td><td>
{tr}Int{/tr}</td><td>
{tr}Routing{/tr}</td><td>
&nbsp;</td></tr>			
<tr><td>
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td><td>
	<select name="filter_type">
		<option value="">{tr}all{/tr}</option>
		<option value="start">{tr}start{/tr}</option>
		<option value="end">{tr}end{/tr}</option>
		<option value="activity">{tr}activity{/tr}</option>
		<option value="switch">{tr}switch{/tr}</option>
		<option value="split">{tr}split{/tr}</option>
		<option value="join">{tr}join{/tr}</option>
		<option value="standalone">{tr}standalone{/tr}</option>
	</select>
</td><td>
	<select name="filter_interactive">
		<option value="">{tr}all{/tr}</option>
		<option value="y">{tr}Interactive{/tr}</option>
		<option value="n">{tr}Automatic{/tr}</option>
	</select>
</td><td>
	<select name="filter_autoroute">
		<option value="">{tr}all{/tr}</option>
		<option value="y">{tr}Auto routed{/tr}</option>
		<option value="n">{tr}Manual{/tr}</option>
	</select>
</td><td>
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td></tr>
</table>	
</form>

<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post">
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
<table>
	<tr><th>
		<input type="submit" name="delete_act" value="Delete" /></th><th>
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'flow_num_desc'}flow_num_asc{else}flow_num_desc{/if}">{tr}#{/tr}</a></th><th>
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th><th>
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></th><th>
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'is_interactive_desc'}is_interactive_asc{else}is_interactive_desc{/if}">{tr}Interactive{/tr}</a></th><th>
		<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'is_interactive_desc'}is_auto_routed_asc{else}is_auto_routed_desc{/if}">{tr}Auto-Routing{/tr}</a></th><th>
		{tr}Action{/tr}
	</th></tr>
	{cycle values="even,odd" print=false}
	{section name=ix loop=$items}
		<tr class="{cycle}"><td style="text-align:center;">
			<input type="checkbox" name="activity[{$items[ix].activity_id}]" /></td><td style="text-align:right;">
			{$items[ix].flow_num}</td><td>
			<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activity_id={$items[ix].activity_id}">{$items[ix].name}</a>
			{if $items[ix].roles < 1}
				<small>{tr}(no roles){/tr}</small>
			{/if}</td><td style="text-align:center;">
			{$items[ix].type|act_icon:"$items[ix].is_interactive"}</td><td style="text-align:center;">
			<input type="checkbox" name="activity_inter[{$items[ix].activity_id}]" {if $items[ix].is_interactive eq 'y'}checked="checked"{/if} /></td><td style="text-align:center;">
			<input type="checkbox" name="activity_route[{$items[ix].activity_id}]" {if $items[ix].is_auto_routed eq 'y'}checked="checked"{/if} /></td><td>
			<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_shared_source.php?pid={$pid}&amp;activity_id={$items[ix].activity_id}">{tr}code{/tr}</a>
			{if $items[ix].is_interactive eq 'y'}<br />
				<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_shared_source.php?pid={$pid}&amp;activity_id={$items[ix].activity_id}&amp;template=1">{tr}template{/tr}</a>
			{/if}
		</td></tr>
{sectionelse}
<tr class="norecords"><td colspan="6">
{tr}No activities defined{/tr}
</td></tr>
{/section}
<tr class="panelsubmitrow"><td colspan="7">
<input type="submit" name="update_act" value="{tr}update{/tr}" />
</td></tr>
</table>
</form>

<h3>{tr}Process Transitions{/tr}</h3>
<table class="panel">
<tr><td>
	<h3>{tr}List of transitions{/tr}</h3>
		<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post" id="filtran">
		<input type="hidden" name="pid" value="{$pid|escape}" />
		<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
		<input type="hidden" name="find" value="{$find2|escape}" />
		<input type="hidden" name="where" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
			{tr}From:{/tr} <select name="filter_tran_name" onchange="javascript:document.getElementById('filtran').submit();">
			<option value="" {if $filter_tran_name eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
			{section name=ix loop=$items}
			<option value="{$items[ix].activity_id|escape}" {if $filter_tran_name eq $items[ix].activity_id}selected="selected"{/if}>{$items[ix].name}</option>
			{/section}
			</select>
	</form>

	<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post">
		<input type="hidden" name="pid" value="{$pid|escape}" />
		<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
		<input type="hidden" name="find" value="{$find2|escape}" />
		<input type="hidden" name="where" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
		<input type="hidden" name="where2" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />

	<table>
		<tr><th>
			<input type="submit" name="delete_tran" value="{tr}Delete{/tr}" /></th><th>
			<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'actfromname_desc'}actfromname_asc{else}actfromname_desc{/if}">{tr}Origin{/tr}</a></th>
				{* <th><a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'acttoname_desc'}acttoname_asc{else}acttoname_desc{/if}">{tr}To{/tr}</a></th> *}
			</tr>
			{cycle values="even,odd" print=false}
			{section name=ix loop=$transitions}
			<tr class="{cycle}"><td>
				<input type="checkbox" name="transition[{$transitions[ix].act_from_id}_{$transitions[ix].act_to_id}]" /></td><td>
				<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activity_id={$transitions[ix].act_from_id}">{$transitions[ix].actfromname}</a>
				{biticon ipackage="Galaxia" iname="next" iexplain="next class="icon"}
				<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activity_id={$transitions[ix].act_to_id}">{$transitions[ix].acttoname}</a></td> {* <td>
				{$transitions[ix].acttoname}</td> *}
			</tr>
			{sectionelse}
				<tr class="norecords"><td colspan="3">
					{tr}No transitions defined{/tr}
				</td></tr>
			{/section}
			</table>
			</form>
	</td><td>

	<h3>{tr}Add a transition{/tr}</h3>
	<form action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php" method="post">
		<input type="hidden" name="pid" value="{$pid|escape}" />
		<input type="hidden" name="activity_id" value="{$info.activity_id|escape}" />
		<input type="hidden" name="find" value="{$find2|escape}" />
		<input type="hidden" name="where" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
		<input type="hidden" name="where2" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
		<table class="panel">
			<tr><td>From:</td><td>
				<select name="act_from_id">
					{section name=ix loop=$items}
						<option value="{$items[ix].activity_id|escape}">{$items[ix].name}</option>
					{/section}
				</select>
			</td></tr>
			<tr><td>To:</td><td>
				<select name="act_to_id">
					{section name=ix loop=$items}
						<option value="{$items[ix].activity_id|escape}">{$items[ix].name}</option>
					{/section}
				</select>
			</td></tr>
			<tr class="panelsubmitrow"><td colspan="2">
				<input type="submit" name="add_trans" value="{tr}add{/tr}" />
			</td></tr>
		</table>
	</form>
	</td></tr>
</table>
</div><!-- end .body -->

</div> {* end .workflow *}
