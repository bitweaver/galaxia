{* $Header: *}
<div class="floaticon">{bithelp}</div>
{strip}
<div class="admin galaxia">
	<div class="header">
		<h1>{tr}Admin processes{/tr}</h1>
	</div>

	{include file="bitpackage:Galaxia/monitor_nav.tpl"}

	<div class="body">
		{if $pid > 0}
			{include file="bitpackage:Galaxia/process_nav.tpl"}
			{if count($errors) > 0}
				<div class="error">
					{formfeedback hash=$errors}
				</div>
			{/if}
		{/if}

		{jstabs}
			{jstab title="Create / Edit Process"}
				{form legend="Create / Edit Process"}
					<input type="hidden" name="version" value="{$info.version|escape}" />
					<input type="hidden" name="pid" value="{$info.p_id|escape}" />
					<input type="hidden" name="offset" value="{$offset|escape}" />
					<input type="hidden" name="where" value="{$where|escape}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
				
					<div class="row">
						{formlabel label="Process Name" for="name"}
						{forminput}
							<input type="text" maxlength="80" name="name" id="name" value="{$info.procname|escape}" /> {tr}ver:{/tr}{$info.version}
							{formhelp note=""}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Version" for=""}
						{forminput}
							{$info.version}
							{formhelp note=""}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Description" for="description"}
						{forminput}
							<textarea rows="5" cols="60" name="description" id="description">{$info.description|escape}</textarea>
							{formhelp note="A brief description about the purpose of the process"}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Is Process Active?" for="is_active"}
						{forminput}
							<input type="checkbox" name="is_active" id="is_active" {if $info.is_active eq 'y'}checked="checked"{/if} />
							{formhelp note="Indicates if the process is active. Invalid processes cannot be active"}
						{/forminput}
					</div>

					<div class="row submit">
						<input type="submit" name="save" value="{if $pid > 0}{tr}Update{/tr}{else}{tr}Create{/tr}{/if}" />
						{if $pid > 0}
							&nbsp; {smartlink ititle="New Process"}
						{/if}
					</div>
				{/form}
			{/jstab}

			{jstab title="Upload Process"}
				{form legend="Upload a Process" enctype="multipart/form-data"}
					<div class="row">
						{formlabel label="Upload File" for="upload"}
						{forminput}
							<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" />
							<input size="16" id="upload" name="userfile1" type="file" />
							{formhelp note=""}
						{/forminput}
					</div>

					<div class="row submit">
						<input type="submit" name="upload" value="{tr}Upload{/tr}" />
					</div>
				{/form}
			{/jstab}

			{jstab title="Filter Processes"}
				{form legend="Filter Processes"}
					<input type="hidden" name="offset" value="{$offset|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />

					<div class="row">
						{formlabel label="Processes" for="filter_name"}
						{forminput}
							<select name="filter_name" id="filter_name">
								<option {if $smarty.request.filter_active eq ""}selected="selected"{/if} value="">{tr}All{/tr}</option>
								{foreach from=$all_proc_names item=name}
									<option {if $name eq $smarty.request.filter_name}selected="selected"{/if} value="{$name|escape}">{$name}</option>
								{/foreach}
							</select>
							{formhelp note=""}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Version" for="filter_version"}
						{forminput}
							<select name="filter_version" id="filter_version">
								<option {if $smarty.request.filter_active eq ""}selected="selected"{/if} value="">{tr}All{/tr}</option>
								{foreach from=$all_proc_versions item=version}
									<option {if $version eq $smarty.request.filter_version}selected="selected"{/if} value="{$version}">{$version}</option>
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
								<option {if $smarty.request.filter_valid eq ""}selected="selected"{/if} value="">{tr}All{/tr}</option>
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
			{/jstab}
		{/jstabs}

		{minifind}

		{form}
			<input type="hidden" name="offset" value="{$offset|escape}" />
			<input type="hidden" name="find" value="{$find|escape}" />
			<input type="hidden" name="where" value="{$where|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
			<table class="data">
				<caption>{tr}List of processes{/tr}</caption>
				<tr>
					<th>&nbsp;</th>
					<th style="width:40%;">{smartlink ititle="Name" isort=procname find=$find where=$where offset=$offset}</th>
					<th style="width:10%;">{smartlink ititle="Version" isort=version find=$find where=$where offset=$offset}</th>
					<th style="width:10%;">{smartlink ititle="Active" isort=is_active find=$find where=$where offset=$offset}</th>
					<th style="width:10%;">{smartlink ititle="Valid" isort=is_valid find=$find where=$where offset=$offset}</th>
					<th style="width:30%;">{tr}Action{/tr}</th>
				</tr>
				{section name=ix loop=$items}
					<tr class="{cycle values='odd,even'}">
						<td>
							<input title="{tr}Select this Process{/tr}" type="checkbox" name="process[{$items[ix].p_id}]" />
						</td>
						<td>
							<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php?find={$find}&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;pid={$items[ix].p_id}">{$items[ix].procname}</a>
						</td>
						<td style="text-align:center;">
							{$items[ix].version}</td><td style="text-align:center;">
							{if $items[ix].is_active eq 'y'}
								{biticon ipackage="Galaxia" iname="refresh2" iexplain="active process"}
							{else}
								&nbsp;{$items[ix].is_active}
							{/if}
						</td>
						<td style="text-align:center;">
							{if $items[ix].is_valid eq 'n'}
								{biticon ipackage="galaxia" iname="red_dot" iexplain="invalid process"}
							{else}
								{biticon ipackage="galaxia" iname="green_dot" iexplain="valid process"}
							{/if}
						</td>
						<td class="actionicon">
							{smartlink ititle="Activities" ifile="admin/g_admin_activities.php" ibiticon="galaxia/activity" pid=$items[ix].p_id}
							{smartlink ititle="Code" ifile="admin/g_admin_shared_source.php" ibiticon="galaxia/book" pid=$items[ix].p_id}
							{smartlink ititle="Roles" ifile="admin/g_admin_roles.php" ibiticon="galaxia/myinfo" pid=$items[ix].p_id}
							{smartlink ititle="Export" ifile="admin/g_save_process.php" ibiticon="galaxia/export" pid=$items[ix].p_id}
							<br />
							{smartlink ititle="New minor" sort_mode=$sort_mode find=$find where=$where offset=$offset newminor=$items[ix].p_id}
							&nbsp;&bull;&nbsp;{smartlink ititle="New major" sort_mode=$sort_mode find=$find where=$where offset=$offset newmajor=$items[ix].p_id}
						</td>
					</tr>
				{sectionelse}
					<tr class="norecords">
						<td colspan="6">
							{tr}No processes defined yet{/tr}
						</td>
					</tr>	
				{/section}
			</table>

			{if $items}
				<input type="submit" name="delete" value="{tr}Remove Selected{/tr}" />
			{/if}
		{/form}

		<p class="total small">
			{tr}Total number of entries{/tr}: {$cant}
		</p>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .galaxia -->
{/strip}
