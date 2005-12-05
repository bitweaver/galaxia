<ul>
{if $gBitUser->hasPermission('bit_p_admin_workflow')}
<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php">{tr}Admin processes{/tr}</a></li>
{/if}
<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=galaxia">{tr}Galaxia Settings{/tr}</a></li>
</ul>
