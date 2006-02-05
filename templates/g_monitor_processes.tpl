{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin galaxia">
	<div class="header">
		<h1>{tr}Monitor processes{/tr}</h1>
	</div>

	<div class="body">
		{include file="bitpackage:Galaxia/monitor_nav.tpl"}

		{form legend="Filter Processes"}
			<input type="hidden" name="offset" value="{$offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />

			<div class="row">
				{formlabel label="Processes" for="find"}
				{forminput}
					<input size="8" type="text" name="find" id="find" value="{$find|escape}" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Version" for="filter_process"}
				{forminput}
					<select name="filter_process" id="filter_process">
						<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
						{foreach from=$all_procs item=proc}
							<option {if $proc.p_id eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.p_id|escape}">{$proc.name} {$proc.version}</option>
						{/foreach}
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Status" for="filter_active"}
				{forminput}
					<select name="filter_active" id="filter_active">
						<option {if $smarty.request.filter_active eq ""}selected="selected"{/if} value="">{tr}All{/tr}</option>
						<option {if $smarty.request.filter_active eq "y"}selected="selected"{/if} value="y">{tr}Active{/tr}</option>
						<option {if $smarty.request.filter_active eq "n"}selected="selected"{/if} value="n">{tr}Inactive{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Valid" for="filter_valid"}
				{forminput}
					<select name="filter_valid" id="filter_valid">
						<option value="">{tr}All{/tr}</option>
						<option {if $smarty.request.filter_valid eq "y"}selected="selected"{/if} value="y">{tr}Valid{/tr}</option>
						<option {if $smarty.request.filter_valid eq "n"}selected="selected"{/if} value="n">{tr}Invalid{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="filter" value="{tr}Filter{/tr}" />
			</div>
		{/form}

		{form}
			<input type="hidden" name="offset" value="{$offset|escape}" />
			<input type="hidden" name="find" value="{$find|escape}" />
			<input type="hidden" name="where" value="{$where|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />

			<table class="data">
				<caption>{tr}List of processes{/tr} <span class="total">[ {$cant} ]</span></caption>
				<tr>
					<th><a href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Name{/tr}</a></th>
					<th>{tr}Activities{/tr}</th>
					<th><a href="{if $sort_mode eq 'is_active_desc'}{sameurl sort_mode='is_active_asc'}{else}{sameurl sort_mode='is_active_desc'}{/if}">{tr}Active{/tr}</a></th>
					<th><a href="{if $sort_mode eq 'is_valid_desc'}{sameurl sort_mode='is_valid_asc'}{else}{sameurl sort_mode='is_valid_desc'}{/if}">{tr}Valid{/tr}</a></th>
					<th>{tr}Instances{/tr}*</th>
				</tr>

				{foreach from=$items item=proc}
					<tr class="{cycle values="odd,even"}">
						<td>
							<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php?pid={$proc.p_id}">{$proc.procname} {$proc.version}</a>
						</td>

						<td>
							<a href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_activities.php?filter_process={$proc.p_id}">{$proc.activities}</a>
						</td>

						<td style="text-align:center;">
							{if $proc.is_active eq 'y'}
								{biticon ipackage="Galaxia" iname="refresh2" iexplain="active process"}
							{else}
								{$proc.is_active}
							{/if}
						</td>

						<td style="text-align:center;">
							{if $proc.is_valid eq 'n'}
							{biticon ipackage="Galaxia" iname="red_dot" iexplain="invalid process"}
							{else}
							{biticon ipackage="Galaxia" iname="green_dot" iexplain="valid process"}
							{/if}
						</td>

						<td style="text-align:right;">
							<a style="color:green;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=active">{$proc.active_instances}</a>
							&nbsp;| <a style="color:black;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=completed">{$proc.completed_instances}</a>
							&nbsp;| <a style="color:grey;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=aborted">{$proc.aborted_instances}</a>
							&nbsp;| <a style="color:red;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_process={$proc.p_id}&amp;filter_status=exception">{$proc.exception_instances}</a>
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="6">{tr}No processes defined yet{/tr}</td></tr>	
				{/foreach}
			</table>
		{/form}

		{pagination}
	</div><!-- end .body -->

	{include file="bitpackage:Galaxia/g_monitor_stats.tpl"}
</div><!-- end .galaxia -->
{/strip}


{* OLD PAGINATION
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
{assign var=selector_offset value=$smarty.section.foo.index|times:"$gBitSystem->getPreference( 'max_records' )"}
<a href="{sameurl offset=$selector_offset}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div> 
END OF PAGINATION *}

