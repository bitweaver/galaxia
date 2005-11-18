<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_admin_roles.php,v 1.1.1.1.2.5 2005/11/18 17:16:11 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessManager.php');

// The galaxia roles manager PHP script.
$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );


if (!isset($_REQUEST['pid']) || empty($_REQUEST['pid'])) {
	galaxia_show_error("No process indicated");
	die;
}

$gBitSmarty->assign('pid', $_REQUEST['pid']);

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']=GALAXIA_PROCESSES_URL.$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";


// Retrieve activity info if we are editing, assign to 
// default values when creating a new activity
if (!isset($_REQUEST['role_id']))
	$_REQUEST['role_id'] = 0;

if ($_REQUEST["role_id"]) {
	$info = $roleManager->get_role($_REQUEST['pid'], $_REQUEST["role_id"]);
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'role_id' => 0
	);
}

$gBitSmarty->assign('role_id', $_REQUEST['role_id']);
$gBitSmarty->assign('info', $info);

// Delete roles
if (isset($_REQUEST["delete"])) {
	
	foreach (array_keys($_REQUEST["role"])as $item) {
		$roleManager->remove_role($_REQUEST['pid'], $item);
	}
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save'])) {
	
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
	);

	$roleManager->replace_role($_REQUEST['pid'], $_REQUEST['role_id'], $vars);

	$info = array(
		'name' => '',
		'description' => '',
		'role_id' => 0
	);

	$gBitSmarty->assign('info', $info);
}

// list mappings
if ( empty( $_REQUEST["sort_mode"] ) ) {
	$_REQUEST["sort_mode"] = 'name_asc';
}
$sort_mode = $_REQUEST["sort_mode"];

// MAPIING
if (!isset($_REQUEST['find_groups']))
	$_REQUEST['find_groups'] = '';

$gBitSmarty->assign('find_groups', $_REQUEST['find_groups']);
$groups = $gBitUser->getAllGroups($_REQUEST);
$gBitSmarty->assign_by_ref('groups', $groups['data']);

$roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, 'name_asc', '');
$gBitSmarty->assign_by_ref('roles', $roles['data']);

if (isset($_REQUEST["delete_map"])) {
	
	foreach (array_keys($_REQUEST["map"])as $item) {
		$parts = explode(':::', $item);
		$roleManager->remove_mapping($parts[0], $parts[1]);
	}
}

if (isset($_REQUEST['save_map'])) {
	
	if (isset($_REQUEST['group']) && isset($_REQUEST['role'])) {
		foreach ($_REQUEST['group'] as $a_group) {
			foreach ($_REQUEST['role'] as $role) {
				$roleManager->map_group_to_role($_REQUEST['pid'], $a_group, $role);
			}
		}
	}
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
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$mapitems = $roleManager->list_mappings($_REQUEST['pid'], $offset, $maxRecords, $sort_mode, $find);

$gBitSmarty->assign('cant', $mapitems['cant']);
$cant_pages = ceil($mapitems["cant"] / $maxRecords);
if ($cant_pages == 0) $cant_pages = 1;
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($mapitems["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('mapitems', $mapitems["data"]);

//MAPPING
if (!isset($_REQUEST['sort_mode2']))
	$_REQUEST['sort_mode2'] = 'name_asc';

$gBitSmarty->assign('sort_mode2', $_REQUEST['sort_mode2']);
// Get all the process roles
$all_roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, $_REQUEST['sort_mode2'], '');
$gBitSmarty->assign_by_ref('items', $all_roles['data']);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$proc_info['is_valid'] = $valid ? 'y' : 'n';
$errors = array();

if (!$valid) {
	$errors = $activityManager->get_error();
}

$gBitSmarty->assign('errors', $errors);
$gBitSmarty->assign('proc_info', $proc_info);
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'offset2',
	'find2',
	'sort_mode2',
	'where2',
	'processId'
);


$gBitSystem->display( 'bitpackage:Galaxia/g_admin_roles.tpl', tra('Admin Process Roles') );

?>
