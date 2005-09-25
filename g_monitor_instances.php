<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_monitor_instances.php,v 1.1.1.1.2.8 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessMonitor.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['update'])) {
	
	foreach ($_REQUEST['update_status'] as $key => $val) {
		$processMonitor->update_instance_status($key, $val);
	}

	foreach ($_REQUEST['update_actstatus'] as $key => $val) {
		$parts = explode(':', $val);

		$processMonitor->update_instance_activity_status($key, $parts[1], $parts[0]);
	}
}

if (isset($_REQUEST['delete'])) {
	
	foreach (array_keys($_REQUEST['inst'])as $ins) {
		$processMonitor->remove_instance($ins);
	}
}

if (isset($_REQUEST['remove_aborted'])) {
	
	$processMonitor->remove_aborted();
}

if (isset($_REQUEST['remove_all'])) {
	
	$processMonitor->remove_all($_REQUEST['filter_process']);
}

if (isset($_REQUEST['sendInstance'])) {
	
	//activity_id indicates the activity where the instance was
	//and we have to send it to some activity to be determined
	include_once( GALAXIA_PKG_PATH.'/src/API/Instance.php');

	$instance = new Instance();
	$instance->getInstance($_REQUEST['sendInstance']);
	// Do not add a workitem since the instance must be already completed!
	$instance->complete($_REQUEST['activity_id'], true, false);
	unset ($instance);
}

if (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'])
	$wheres[] = "gi.`status`='" . $_REQUEST['filter_status'] . "'";

if (isset($_REQUEST['filter_act_status']) && $_REQUEST['filter_act_status'])
	$wheres[] = "`actstatus`='" . $_REQUEST['filter_act_status'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gi.`p_id`=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "gia.`activity_id`=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_instanceName']) && $_REQUEST['filter_instanceName'])
	$wheres[] = "gi.name='" . $_REQUEST['filter_instanceName'] . "'";

if (isset($_REQUEST['filter_owner']) && $_REQUEST['filter_owner'])
	$wheres[] = "gia.`owner_id`='" . $_REQUEST['filter_owner'] . "'";

$where = implode(' and ', $wheres);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'instance_id_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);
$gBitSmarty->assign('where', $where);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);

$items = $processMonitor->monitor_list_instances($offset, $maxRecords, $sort_mode, $find, $where);
$gBitSmarty->assign('cant', $items['cant']);

$cant_pages = ceil($items["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('items', $items["data"]);

$all_procs = $processMonitor->monitor_list_processes(0, -1, 'procname_desc', '', '');
$gBitSmarty->assign_by_ref('all_procs', $all_procs["data"]);

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
	$where = ' `p_id`=' . $_REQUEST['filter_process'];
} else {
	$where = '';
}

$all_acts = $processMonitor->monitor_list_activities(0, -1, 'name_desc', '', $where);
$gBitSmarty->assign_by_ref('all_acts', $all_acts["data"]);

$types = $processMonitor->monitor_list_activity_types();
$gBitSmarty->assign_by_ref('types', $types);

$names = $processMonitor->monitor_list_instances_names();
$gBitSmarty->assign_by_ref('names', $names);

$gBitSmarty->assign('stats', $processMonitor->monitor_stats());

$all_statuses = array(
	'aborted',
	'active',
	'completed',
	'exception'
);

$gBitSmarty->assign('all_statuses', $all_statuses);

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_status',
	'filter_act_status',
	'filter_type',
	'process_id',
	'filter_process',
	'filter_owner',
	'filter_activity'
);

$gBitSmarty->assign('statuses', $processMonitor->monitor_list_statuses());
$gBitSmarty->assign('owners', $processMonitor->monitor_list_owners());


$gBitSystem->display( 'bitpackage:Galaxia/g_monitor_instances.tpl', tra('Monitor Instances') );

?>
