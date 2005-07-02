<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_monitor_activities.php,v 1.1 2005/07/02 16:36:59 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'ProcessMonitor.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['filter_is_interactive']) && $_REQUEST['filter_is_interactive'])
	$wheres[] = "`is_interactive`='" . $_REQUEST['filter_is_interactive'] . "'";

if (isset($_REQUEST['filter_is_auto_routed']) && $_REQUEST['filter_is_auto_routed'])
	$wheres[] = "`is_auto_routed`='" . $_REQUEST['filter_is_auto_routed'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "`p_id`=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "`activity_id`=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_type']) && $_REQUEST['filter_type'])
	$wheres[] = "`type`='" . $_REQUEST['filter_type'] . "'";

$where = implode(' and ', $wheres);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'flow_num_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$smarty->assign('where', $where);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$items = $processMonitor->monitor_list_activities($offset, $maxRecords, $sort_mode, $find, $where);
$smarty->assign('cant', $items['cant']);

$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('items', $items["data"]);

$all_procs = $items = $processMonitor->monitor_list_processes(0, -1, 'name_desc', '', '');
$smarty->assign_by_ref('all_procs', $all_procs["data"]);

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
	$where = ' `p_id`=' . $_REQUEST['filter_process'];
} else {
	$where = '';
}

$all_acts = $processMonitor->monitor_list_activities(0, -1, 'name_desc', '', $where);
$smarty->assign_by_ref('all_acts', $all_acts["data"]);
$types = $processMonitor->monitor_list_activity_types();
$smarty->assign_by_ref('types', $types);

$smarty->assign('stats', $processMonitor->monitor_stats());
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_is_interactive',
	'filter_is_auto_routed',
	'filter_activity',
	'filter_type',
	'processId',
	'filter_process'
);


$gBitSystem->display( 'bitpackage:Galaxia/g_monitor_activities.tpl');

?>
