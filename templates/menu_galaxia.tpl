{strip}
<ul>
{if $gBitUser->hasPermission('p_galaxia_admin')}
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php">{tr}Admin processes{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_processes.php">{tr}Monitor processes{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_activities.php">{tr}Monitor activities{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php">{tr}Monitor instances{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_workitems.php">{tr}Monitor workitems{/tr}</a></li>
{/if}
{if $gBitUser->hasPermission('p_galaxia_use')}
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_user_processes.php">{tr}User processes{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_user_activities.php">{tr}User activities{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}g_user_instances.php">{tr}User instances{/tr}</a></li>
{/if}
</ul>
{/strip}
