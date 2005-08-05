<p class="footer">
	{tr}{$stats.processes} processes ({$stats.active_processes} active) ({$stats.running_processes} being run){/tr}
	<br />
	{tr}*Instances{/tr}:
	<a style="color:green;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=active">{$stats.active_instances} {tr}active{/tr}</a> |
	<a style="color:black;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=completed">{$stats.completed_instances} {tr}completed{/tr}</a> |
	<a style="color:grey;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=aborted">{$stats.aborted_instances} {tr}aborted{/tr}</a> |
	<a style="color:red;" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=exception">{$stats.exception_instances} {tr}exceptions{/tr}</a>
</p>
