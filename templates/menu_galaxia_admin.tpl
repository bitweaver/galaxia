<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.GALAXIA_PKG_DIR|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		{if $gBitUser->hasPermission('p_galaxia_admin')}
		<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php">{tr}Admin processes{/tr}</a></li>
		{/if}
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=galaxia">{tr}Galaxia Settings{/tr}</a></li>
	</ul>
</li>
