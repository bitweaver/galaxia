{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<div class="floaticon">{bithelp}</div>

<div class="admin workflow">
<div class="header">
<h1>{tr}Admin process sources{/tr}</h1>
</div>

{include file="bitpackage:Galaxia/monitor_nav.tpl"}

<div class="navbar above">
  <a href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php?where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;pid=0">{tr}new{/tr}</a>
</div>

<div class="body">

<h2>{tr}Add or edit source code{/tr}</h2>

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

<form id="editsource" action="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_shared_source.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="source_name" value="{$source_name|escape}" />
<table class="panel">
<tr>
  <td>{tr}Select Source{/tr}</td>
  <td>
		<select name="activity_id" onchange="document.getElementById('editsource').submit();">
		<option value="" {if $activity_id eq 0}selected="selected"{/if}>{tr}Shared code{/tr}</option>
		{section loop=$items name=ix}
		<option value="{$items[ix].activity_id|escape}" {if $activity_id eq $items[ix].activity_id}selected="selected"{/if}>{$items[ix].name}</option>
		{/section}
		</select>
  </td>

  <td style="text-align:center;">
    {if $activity_id > 0 and $act_info.is_interactive eq 'y'}
        {if $template eq 'y'}
	    <input type="submit" class="btn" name="code" value="{tr}code{/tr}" />
            <input type="hidden" name="template" value="{$template|escape}">
        {else}
            <input type="submit" class="btn" name="template" value="{tr}template{/tr}" />
        {/if}
    {else}
        {tr}Non Interactive{/tr}
    {/if}
  </td>
  <td>
  	<input type="submit" class="btn" name="save" value="{tr}save{/tr}" />
  	<input type="submit" class="btn" name="cancel" value="{tr}cancel{/tr}" />
  </td>
</tr>
<tr>
  <td colspan="4">
    <table>
    <tr>
    <td>
  	<textarea id="src" name="source" rows="20" cols="50">{$data|escape}</textarea>
  	</td>
  	<td>
  	{if $template eq 'y'}
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}if{rdelim}\n{ldelim}elseif{rdelim}\n{ldelim}else{rdelim}\n{ldelim}/if{rdelim}\n');">{ldelim}if{rdelim}{ldelim}/if{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}section name= loop={rdelim}\n{ldelim}sectionelse{rdelim}\n{ldelim}/section{rdelim}\n');">{ldelim}section{rdelim}{ldelim}/section{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}foreach from= item={rdelim}\n{ldelim}foreachelse{rdelim}\n{ldelim}/foreach{rdelim}\n');">{ldelim}foreach{rdelim}{ldelim}/foreach{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}tr{rdelim}{ldelim}/tr{rdelim}\n');">{ldelim}tr{rdelim}{ldelim}/tr{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}literal{rdelim}\n{ldelim}/literal{rdelim}\n');">{ldelim}literal{rdelim}{ldelim}/literal{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}* *{rdelim}\n');">{ldelim}* *{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}{literal}strip{/literal}{rdelim}{ldelim}{literal}/strip{/literal}{rdelim}\n');">{ldelim}{literal}strip{/literal}{rdelim}{ldelim}{literal}/strip{/literal}{rdelim}</a><hr/>
		<a class="link" href="javascript:setSomeElement('src','\n{ldelim}include file={rdelim}\n');">{ldelim}include{rdelim}</a><hr/>
  	{else}
  		{literal}
  		<a href="javascript:setSomeElement('src','$instance->setNextUser(\'\');');">{tr}Set next user{/tr}</a><hr />
		<a href="javascript:setSomeElement('src','$instance->get(\'\');');">{tr}Get property{/tr}</a><hr />
		<a href="javascript:setSomeElement('src','$instance->set(\'\',\'\');');">{tr}Set property{/tr}</a><hr />
		{/literal}
  		{if $act_info.is_interactive eq 'y'}
			{literal}
  			<a href="javascript:setSomeElement('src','$instance->complete();');">{tr}Complete{/tr}</a><hr />
  			<a href="javascript:setSomeElement('src','if(isset($_REQUEST[\'save\'])) {\n  $instance->complete();\n}');">{tr}Process form{/tr}</a><hr />
			{/literal}
  		{/if}
  		{if $act_info.type eq 'switch'}
  			{literal}
			<a href="javascript:setSomeElement('src','$instance->setNextActivity(\'\');');">{tr}Set Next act{/tr}</a><hr />  		    
			<a href="javascript:setSomeElement('src','if() {\n  $instance->setNextActivity(\'\');\n}');">{tr}If:SetNextact{/tr}</a><hr />  		    
			<a href="javascript:setSomeElement('src','switch($instance->get(\'\')){\n  case:\'\':\n  $instance->setNextActivity(\'\');\n  break;\n}');">{tr}Switch construct{/tr}</a><hr />
			{/literal}
  		{/if}
  	{/if}
  	</td>
  	</tr>
  	</table>
  </td>
</tr>
</table>
</form>
</div>

</div> {* end .workflow *}
