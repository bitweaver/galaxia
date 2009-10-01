<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_monitor_processes.php,v 1.8 2009/10/01 14:16:59 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
require_once( '../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessMonitor.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'p_galaxia_admin', "Permission denied you cannot admin the workflow" );

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

if (isset($_REQUEST['filter_active']) && $_REQUEST['filter_active'])
	$wheres[] = "`is_active`='" . $_REQUEST['filter_active'] . "'";

if (isset($_REQUEST['filter_valid']) && $_REQUEST['filter_valid'])
	$wheres[] = "`is_valid`='" . $_REQUEST['filter_valid'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "`p_id`=" . $_REQUEST['filter_process'] . "";

$where = implode(' and ', $wheres);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'last_modified_desc';
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

$items = $processMonitor->monitor_list_processes($offset, $max_records, $sort_mode, $find, $where);
$gBitSmarty->assign('cant', $items['cant']);

$cant_pages = ceil($items["cant"] / $max_records);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $max_records));

if ($items["cant"] > ($offset + $max_records)) {
	$gBitSmarty->assign('next_offset', $offset + $max_records);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $max_records);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('items', $items["data"]);

$all_procs = $processMonitor->monitor_list_processes(0, -1, 'procname_desc', '', '');
$gBitSmarty->assign_by_ref('all_procs', $all_procs["data"]);

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_valid',
	'filter_process',
	'filter_active',
	'processId'
);

$gBitSmarty->assign('stats', $processMonitor->monitor_stats());


$gBitSystem->display( 'bitpackage:Galaxia/g_monitor_processes.tpl', tra('Monitor Processes') , array( 'display_mode' => 'display' ));

?>
