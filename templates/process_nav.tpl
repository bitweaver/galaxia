<div class="box">
<div class="boxtitle">{$proc_info.name} {tr}version{/tr} {$proc_info.version}</div>
<div class="boxcontent">
  <div class="navbar workflow">
    {if $proc_info.is_valid eq 'y'}
      {biticon ipackage="Galaxia" iname="green_dot" iexplain="valid" class="icon"}
    {else}
      {biticon ipackage="Galaxia" iname="red_dot" iexplain="invalid" class="icon"}
    {/if}

    {if $proc_info.is_active eq 'y'}
      <a title="{tr}stop{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?pid={$pid}&amp;deactivate_proc={$pid}">{biticon ipackage="Galaxia" iname="stop" iexplain="stop" class="icon"}</a>
    {else}
      {if $proc_info.is_valid eq 'y'}
        <a title="{tr}activate{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?pid={$pid}&amp;activate_proc={$pid}">{biticon ipackage="Galaxia" iname="refresh2" iexplain="activate" class="icon"}</a>
      {/if}
    {/if}

    <a title="{tr}activities{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_activities.php?pid={$pid}">{biticon ipackage="Galaxia" iname="Activity" iexplain="activities" class="icon"}</a>
    <a title="{tr}edit{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_processes.php?pid={$pid}">{biticon ipackage="Galaxia" iname="change" iexplain="edit" class="icon"}</a>
    <a title="{tr}code{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_shared_source.php?pid={$pid}">{biticon ipackage="Galaxia" iname="book" iexplain="code" class="icon"}</a>
    <a title="{tr}roles{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_admin_roles.php?pid={$pid}">{biticon ipackage="Galaxia" iname="myinfo" iexplain="roles" class="icon"}</a>
    <a {jspopup href="`$proc_info.graph`" title="Graph" gutsonly=true}>{biticon ipackage="Galaxia" iname="graph" iexplain="graph" class="icon"}</a>
    <a title="{tr}export{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}admin/g_save_process.php?pid={$pid}">{biticon ipackage="Galaxia" iname="export" iexplain="export" class="icon"}</a>
  <a title="{tr}monitor processes{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_processes.php">{biticon ipackage="Galaxia" iname="process" iexplain="processes"}</a>
  <a title="{tr}monitor activities{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_activities.php">{biticon ipackage="Galaxia" iname="activity" iexplain="activities"}</a>
  <a title="{tr}monitor instances{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_instances.php">{biticon ipackage="Galaxia" iname="instance" iexplain="instances"}</a>
  <a title="{tr}monitor workitems{/tr}" href="{$smarty.const.GALAXIA_PKG_URL}g_monitor_workitems.php">{biticon ipackage="Galaxia" iname="memo" iexplain="work items"}</a>
  </div>
</div>
</div>
