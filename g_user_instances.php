<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_user_instances.php,v 1.1 2005/07/02 16:36:59 bitweaver Exp $

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
if (isset($_REQUEST['send'])) {
	
	$GUI->gui_send_instance($gBitUser->getUserId(), $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['abort'])) {
	
	$GUI->gui_abort_instance($gBitUser->getUserId(), $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['exception'])) {
	
	$GUI->gui_exception_instance($gBitUser->getUserId(), $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['grab'])) {
	
	$GUI->gui_grab_instance($gBitUser->getUserId(), $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['release'])) {
	
	$GUI->gui_release_instance($gBitUser->getUserId(), $_REQUEST['aid'], $_REQUEST['iid']);
}

$where = '';
$wheres = array();

if (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'])
	$wheres[] = "gi.`status`='" . $_REQUEST['filter_status'] . "'";

if (isset($_REQUEST['filter_act_status']) && $_REQUEST['filter_act_status'])
	$wheres[] = "`actstatus`='" . $_REQUEST['filter_act_status'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gi.`p_id`=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "gia.`activity_id`=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_user']) && $_REQUEST['filter_user'])
	$wheres[] = "ugm.`user_id`=" . ($_REQUEST['filter_user']=='*') ? NULL : "'" . $_REQUEST['filter_user'] . "'";

if (isset($_REQUEST['filter_owner']) && $_REQUEST['filter_owner'])
	$wheres[] = "gi.`owner_id`='" . $_REQUEST['filter_owner'] . "'";

$where = implode(' and ', $wheres);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'procname_asc';
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

$smarty->assign_by_ref('user_info', $gBitUser->mInfo);

$smarty->assign('find', $find);
$smarty->assign('where', $where);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$items = $GUI->gui_list_user_instances($gBitUser->getUserId(), $offset, $maxRecords, $sort_mode, $find, $where);
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

$all_statuses = array(
	'aborted',
	'active',
	'exception'
);

$smarty->assign('statuses', $all_statuses);

$section = 'workflow';

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_user',
	'filter_status',
	'filter_act_status',
	'filter_type',
	'processId',
	'filter_process',
	'filter_owner',
	'filter_activity'
);


$gBitSystem->display( 'bitpackage:Galaxia/g_user_instances.tpl');

?>
