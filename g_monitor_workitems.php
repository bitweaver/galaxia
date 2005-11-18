<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_monitor_workitems.php,v 1.1.1.1.2.6 2005/11/18 19:21:38 wolff_borg Exp $

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

if (isset($_REQUEST['filter_instance']) && $_REQUEST['filter_instance'])
	$wheres[] = "instance_id=" . $_REQUEST['filter_instance'] . "";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gp.`p_id`=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "ga.activity_id=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_user']) && $_REQUEST['filter_user']) {
	if ($_REQUEST['filter_user'] == '*')
		$wheres[] = "`user_id` is NULL";
	elseif (is_numeric($_REQUEST['filter_user']))
		$wheres[] = "`user_id`='" . $_REQUEST['filter_user'] . "'";
}

$where = implode(' and ', $wheres);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'order_id_asc';
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

$items = $processMonitor->monitor_list_workitems($offset, $maxRecords, $sort_mode, $find, $where);
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

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_user',
	'filter_activity',
	'filter_process',
	'filter_instance',
	'processId',
	'filter_process'
);

$types = $processMonitor->monitor_list_activity_types();
$gBitSmarty->assign_by_ref('types', $types);

$gBitSmarty->assign('stats', $processMonitor->monitor_stats());

$gBitSmarty->assign('users', $processMonitor->monitor_list_wi_users());


$gBitSystem->display( 'bitpackage:Galaxia/g_monitor_workitems.tpl', tra('Monitor Workitems') );

?>
