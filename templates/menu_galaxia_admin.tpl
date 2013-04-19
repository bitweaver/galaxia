{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
		{if $gBitUser->hasPermission('p_galaxia_admin')}
	<li><a class="item" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php">{tr}Admin processes{/tr}</a></li>
		{/if}
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=galaxia">{tr}Galaxia{/tr}</a></li>
</ul>

