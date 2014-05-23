{* $Header: *}
{strip}
{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>
<div class="admin galaxia">
	<div class="header">
		<h1>{tr}Admin process roles{/tr}</h1>
	</div>

	<div class="body">
		{if $pid > 0}
			{include file="bitpackage:Galaxia/process_nav.tpl"}
			{if count($errors) > 0}
				<div class="error">
					{tr}This process is invalid{/tr}:<br />
					{formfeedback hash=$errors}
				</div>
			{/if}
		{/if}

		{jstabs}
			{jstab title="Create / Edit Role"}
				{form legend="Create / Edit Role"}
					<input type="hidden" name="role_id" value="{$role_id|escape}" />
					<input type="hidden" name="pid" value="{$pid|escape}" />
					<input type="hidden" name="offset" value="{$offset|escape}" />
					<input type="hidden" name="where" value="{$where|escape}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
					<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />

					<div class="control-group">
						{formlabel label="Role Name" for="name"}
						{forminput}
							<input type="text" name="name" id="name" value="{$info.name|escape}" />
							{formhelp note=""}
						{/forminput}
					</div>

					<div class="control-group">
						{formlabel label="Description" for="description"}
						{forminput}
							<textarea rows="5" cols="50" name="description" id="description">{$info.description|escape}</textarea>
							{formhelp note="A brief description about the purpose of the role"}
						{/forminput}
					</div>

					<div class="control-group submit">
						<input type="submit" class="btn btn-default" name="save" value="{if $info.role_id > 0}{tr}Update{/tr}{else}{tr}Create{/tr}{/if}" />
						{if $info.role_id > 0}
							&nbsp; {smartlink ititle="New Role" pid=$info.p_id}
						{/if}
					</div>
				{/form}
				{minifind}

				{form}
					<input type="hidden" name="pid" value="{$pid|escape}" />
					<input type="hidden" name="role_id" value="{$role_id|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
					<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="offset" value="{$offset|escape}" />
					<table class="table data">
						<caption>{tr}List of roles{/tr}</caption>
						<tr>
							<th>&nbsp;</th>
							<th style="width:40%;">{smartlink ititle="Name" isort=name find=$find where=$where offset=$offset}</th>
							<th style="width:60%;">{smartlink ititle="Description" isort=description find=$find where=$where offset=$offset}</th>
						</tr>
						{foreach from=$items item=proc}
							<tr class="{cycle values='odd,even'}">
								<td>
									<input title="{tr}Select this Role{/tr}" type="checkbox" name="role[{$proc.role_id}]" />
								</td>
								<td style="text-align:center;">
									<a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_roles.php?sort_mode={$sort_mode}&amp;offset={$offset}&amp;find={$find}&amp;pid={$pid}&amp;sort_mode2={$sort_mode2}&amp;role_id={$proc.role_id}">{$proc.name}</a>
								</td>
								<td style="text-align:center;">
									{$proc.description}
								</td>
							</tr>
						{foreachelse}
							<tr class="norecords">
								<td colspan="6">
									{tr}No roles defined yet{/tr}
								</td>
							</tr>	
						{/foreach}
					</table>

					{if $items}
						<input type="submit" class="btn btn-default" name="delete" value="{tr}Remove Selected{/tr}" />
					{/if}
				{/form}
				<p class="total small">
					{tr}Total number of entries{/tr}: {$cant}
				</p>

				{pagination}
			{/jstab}

			{jstab title="Map groups to roles"}
				{if count($roles) > 0}
					{form legend="Map groups to roles"}
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
									<td>{tr}Groups{/tr}:
										<input type="text" size="10" name="find_groups" value="{$find_groups|escape}" />&nbsp;
										<input type="submit" class="btn btn-default" name="findgroups" value="{tr}filter{/tr}" />	  
									</td>
									<td>{tr}Roles{/tr}:</td>
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
									<input type="submit" class="btn btn-default" name="save_map" value="{tr}map{/tr}" />
								</td>
							</tr>
						</table>
					{/form}
				{else}
					<div class="norecords">
						{tr}No roles are defined yet so no roles can be mapped{/tr}
					</div>	
				{/if}

				{minifind}

				{form}
					<input type="hidden" name="pid" value="{$pid|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
					<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="offset" value="{$offset|escape}" />
					<table class="table data">
						<caption>{tr}List of mappings{/tr}</caption>
						<tr>
							<th>&nbsp;</th>
							<th style="width:40%;">{smartlink ititle="Name" isort=name find=$find where=$where offset=$offset}</th>
							<th style="width:60%;">{smartlink ititle="Group" isort=group_name find=$find where=$where offset=$offset}</th>
						</tr>
						{section name=ix loop=$mapitems}
							<tr class="{cycle values='odd,even'}">
								<td>
									<input title="{tr}Select this Mapping{/tr}" type="checkbox" name="map[{$mapitems[ix].group_id}:::{$mapitems[ix].role_id}]" />
								</td>
								<td style="text-align:center;">
									  {$mapitems[ix].name}
								</td>
								<td style="text-align:center;">
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

					{if $items}
						<input type="submit" class="btn btn-default" name="delete" value="{tr}Remove Selected{/tr}" />
					{/if}
				{/form}
				<p class="total small">
					{tr}Total number of entries{/tr}: {$cant}
				</p>

				{pagination}

			{/jstab}

		{/jstabs}


	</div><!-- end .body -->
</div><!-- end .galaxia -->
{/strip}
