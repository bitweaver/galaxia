<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_user_activities.php,v 1.1 2005/07/02 16:36:59 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/GUI.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_use_workflow', "Permission denied you cannot use the workflow" );

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
$where = '';
$wheres = array();

/*
if(isset($_REQUEST['filter_active'])&&$_REQUEST['filter_active']) $wheres[]="is_active='".$_REQUEST['filter_active']."'";
if(isset($_REQUEST['filter_valid'])&&$_REQUEST['filter_valid']) $wheres[]="is_valid='".$_REQUEST['filter_valid']."'";
*/
if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gp.`p_id`=" . $_REQUEST['filter_process'] . "";

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

$items = $GUI->gui_list_user_activities($gBitUser->getUserId(), $offset, $maxRecords, $sort_mode, $find, $where);
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

$processes = $GUI->gui_list_user_processes($gBitUser->getUserId(), 0, -1, 'procname_asc', '', '');
$smarty->assign_by_ref('all_procs', $processes['data']);

$section = 'workflow';
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


$gBitSystem->display( 'bitpackage:Galaxia/g_user_activities.tpl');

?>
