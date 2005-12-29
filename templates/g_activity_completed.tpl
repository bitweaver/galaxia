{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
<h1>{tr}Activity completed{/tr}</h1>
{include file="bitpackage:Galaxia/user_nav.tpl"}
<br /><br />
<table class="panel">
<tr>
	<td>{tr}Process{/tr}
	<td>{$procname} {$procversion}</td>
</tr>
<tr>
	<td>{tr}Activity{/tr}
	<td>{$actname}</td>
</tr>
<tr>
	<td></td>
</tr>

<form method="POST" action="{$smarty.const.GALAXIA_PKG_URL}g_run_activity.php">
<tr class="normal">
	<tr>
		<td class="odd" colspan="2">{tr}Comment{/tr}</td>
	</tr>

		<td class="odd" colspan="2">{tr}Title{/tr}:<input type="text" name="__title" value="{if $post eq 'y'}{$title}{/if}" {if $post eq 'y'}readonly{/if}/></td>
	<tr>
		<td class="odd" colspan="2"><textarea rows="5" cols="50" name="__comment" {if $post eq 'y'}readonly{/if}>{if $post eq 'y'}{$comment}{/if}</textarea></td>
	</tr>
	{if $post eq 'n'}
	<tr>
		<td class="odd" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
	</tr>
	{/if}
</tr>
<INPUT type="hidden" name="iid" value="{$iid}">
<INPUT type="hidden" name="__post" value="y">
<INPUT type="hidden" name="activity_id" value="{$actid}">
</form>
</table>
