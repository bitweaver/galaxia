<div class="footer">
  {$stats.processes} {tr}processes{/tr} ({$stats.active_processes} {tr}active{/tr}) ({$stats.running_processes} {tr}being run{/tr})
<br />
  {tr}*Instances{/tr}:
  <a style="color:green;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=active">{$stats.active_instances} {tr}active{/tr}</a> |
  <a style="color:black;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=completed">{$stats.completed_instances} {tr}completed{/tr}</a> |
  <a style="color:grey;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=aborted">{$stats.aborted_instances} {tr}aborted{/tr}</a> |
  <a style="color:red;" href="{$gBitLoc.GALAXIA_PKG_URL}g_monitor_instances.php?filter_status=exception">{$stats.exception_instances} {tr}exceptions{/tr}</a>
</div>
