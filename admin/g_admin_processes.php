<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_admin_processes.php,v 1.1.1.1.2.2 2005/07/11 12:30:54 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'ProcessManager.php');

$smarty->assign('is_active_help', tra('indicates if the process is active. Invalid processes cant be active'));

// The galaxia process manager PHP script.
$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );


// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
	$_REQUEST['pid'] = 0;

if ($_REQUEST["pid"]) {
	$info = $processManager->get_process($_REQUEST["pid"]);

	$info['graph'] = GALAXIA_PROCESSES_URL . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'version' => '1.0',
		'is_active' => 'n',
		'p_id' => 0,
		'graph' => '#'
	);
}

$smarty->assign('pid', $_REQUEST['pid']);
$smarty->assign('info', $info);

//Check here for an uploaded process
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	
	$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

	$data = '';
	$fhash = '';

	while (!feof($fp)) {
		$data .= fread($fp, 8192 * 16);
	}

	fclose ($fp);
	$size = $_FILES['userfile1']['size'];
	$name = $_FILES['userfile1']['name'];
	$type = $_FILES['userfile1']['type'];

	$process_data = $processManager->unserialize_process($data);

	if ($processManager->process_name_exists($process_data['name'], $process_data['version'])) {
		galaxia_show_error("The process name already exists");
		die;
	} else {
		$processManager->import_process($process_data);
	}
}

if (isset($_REQUEST["delete"])) {
	
	foreach (array_keys($_REQUEST["process"])as $item) {
		$processManager->remove_process($item);
	}
}

if (isset($_REQUEST['newminor'])) {
	
	$processManager->new_process_version($_REQUEST['newminor']);
}

if (isset($_REQUEST['newmajor'])) {
	
	$processManager->new_process_version($_REQUEST['newmajor'], false);
}

if (isset($_REQUEST['save'])) {
	
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'version' => $_REQUEST['version'],
		'is_active' => 'n'
	);

	if ($processManager->process_name_exists($_REQUEST['name'], $_REQUEST['version']) && $_REQUEST['pid'] == 0) {
		galaxia_show_error("Process already exists");
		die;
	}

	if (isset($_REQUEST['is_active']) && $_REQUEST['is_active'] == 'on') {
		$vars['is_active'] = 'y';
	}

	$pid = $processManager->replace_process($_REQUEST['pid'], $vars);

	$valid = $activityManager->validate_process_activities($pid);

	if (!$valid) {
		$processManager->deactivate_process($pid);
	}

	$info = array(
		'name' => '',
		'description' => '',
		'version' => '1.0',
		'is_active' => 'n',
		'p_id' => 0,
		'graph' => '#'
	);

	$smarty->assign('info', $info);
}
$smarty->assign_by_ref('proc_info', $info);

$where = '';
$wheres = array();

if (isset($_REQUEST['filter'])) {
	if ($_REQUEST['filter_name']) {
		$wheres[] = " `name`='" . $_REQUEST['filter_name'] . "'";
	}

	if ($_REQUEST['filter_active']) {
		$wheres[] = " `is_active`='" . $_REQUEST['filter_active'] . "'";
	}

	$where = implode('and', $wheres);
}

if (isset($_REQUEST['where'])) {
	$where = $_REQUEST['where'];
}

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

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$smarty->assign('where', $where);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$items = $processManager->list_processes($offset, $maxRecords, $sort_mode, $find, $where);
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

if ($_REQUEST['pid']) {
	
	$valid = $activityManager->validate_process_activities($_REQUEST['pid']);

	$errors = array();

	if (!$valid) {
		$processManager->deactivate_process($_REQUEST['pid']);

		$errors = $activityManager->get_error();
	}

	$smarty->assign('errors', $errors);
}

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_name',
	'filter_active'
);

$all_procs = $processManager->list_processes(0, -1, 'name_desc', '', '');
$smarty->assign_by_ref('all_procs', $all_procs['data']);


$gBitSystem->display( 'bitpackage:Galaxia/g_admin_processes.tpl', tra('Admin Processes') );

?>
