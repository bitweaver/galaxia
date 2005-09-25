<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_admin_activities.php,v 1.1.1.1.2.5 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessManager.php');

$maxExpirationTime = array (
"years" => 5,
"months" => 11,
"days" => 30,
"hours" => 23,
"minutes" => 59
);

// The galaxia activities manager PHP script.
$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );


if (!isset($_REQUEST['pid'])) {
	galaxia_show_error("No process indicated");
	die;
}

$gBitSmarty->assign('pid', $_REQUEST['pid']);

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']=GALAXIA_PROCESSES_URL.$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";



// Retrieve activity info if we are editing, assign to 
// default values when creating a new activity
if (!isset($_REQUEST['activity_id']))
	$_REQUEST['activity_id'] = 0;

if ($_REQUEST["activity_id"]) {
	$info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST["activity_id"]);
	$time = $activityManager->get_expiration_members($info['expiration_time']);
	$info['year'] = $time['year'];
	$info['month'] = $time['month'];
	$info['day'] = $time['day'];
	$info['hour'] = $time['hour'];
	$info['minute'] = $time['minute'];
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'activity_id' => 0,
		'is_interactive' => 'y',
		'is_auto_routed' => 'n',
		'type' => 'activity',
		'month'=> 0,
		'day'=> 0,
		'hour'=> 0,
		'minute'=> 0,
		'expiration_time'=> 0
	);
}

$gBitSmarty->assign('activity_id', $_REQUEST['activity_id']);
$gBitSmarty->assign('info', $info);

// Remove a role from the activity
if (isset($_REQUEST['remove_role']) && $_REQUEST['activity_id']) {
	$activityManager->remove_activity_role($_REQUEST['activity_id'], $_REQUEST['remove_role']);
}

$role_to_add = 0;

// Add a role to the process
if (!empty($_REQUEST['rolename']) && isset($_REQUEST['addrole'])) {
	$is_interactive = (isset($_REQUEST['is_interactive']) && $_REQUEST['is_interactive'] == 'on') ? 'y' : 'n';

	$is_auto_routed = (isset($_REQUEST['is_auto_routed']) && $_REQUEST['is_auto_routed'] == 'on') ? 'y' : 'n';
	$info = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'activity_id' => $_REQUEST['activity_id'],
		'is_interactive' => $is_interactive,
		'is_auto_routed' => $is_auto_routed,
		'type' => $_REQUEST['type'],
		'month'=> 0,
		'day'=> 0,
		'hour'=> 0,
		'minute'=> 0,
		'expiration_time'=> 0
	);

	$vars = array(
		'name' => $_REQUEST['rolename'],
		'description' => ''
	);

	if (isset($_REQUEST["userole"]) && $_REQUEST["userole"] != '') {
		if ($_REQUEST['activity_id']) {
			$activityManager->add_activity_role($_REQUEST['activity_id'], $_REQUEST["userole"]);
		}
	} else {
		$rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);

		if ($_REQUEST['activity_id']) {
			$activityManager->add_activity_role($_REQUEST['activity_id'], $rid);
		}
	}
}

// Delete activities
if (isset($_REQUEST["delete_act"])) {
	foreach (array_keys($_REQUEST["activity"])as $item) {
		$activityManager->remove_activity($_REQUEST['pid'], $item);
	}
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save_act'])) {
	$is_interactive = (isset($_REQUEST['is_interactive']) && $_REQUEST['is_interactive'] == 'on') ? 'y' : 'n';

	$is_auto_routed = (isset($_REQUEST['is_auto_routed']) && $_REQUEST['is_auto_routed'] == 'on') ? 'y' : 'n';
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'activity_id' => $_REQUEST['activity_id'],
		'is_interactive' => $is_interactive,
		'is_auto_routed' => $is_auto_routed,
		'type' => $_REQUEST['type'],
		'expiration_time' => $_REQUEST['year']*535680+$_REQUEST['month']*44640+$_REQUEST['day']*1440+$_REQUEST['hour']*60+$_REQUEST['minute'],
	);

	if ($activityManager->activity_name_exists($_REQUEST['pid'], $_REQUEST['name']) && $_REQUEST['activity_id'] == 0) {
		galaxia_show_error("Activity name already exists");
		die;
	}

	$newaid = $activityManager->replace_activity($_REQUEST['pid'], $_REQUEST['activity_id'], $vars);
	$rid = 0;

	if (isset($_REQUEST['userole']) && $_REQUEST['userole'])
		$rid = $_REQUEST['userole'];

	if (!empty($_REQUEST['rolename'])) {
		$vars = array(
			'name' => $_REQUEST['rolename'],
			'description' => ''
		);

		$rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);
	}

	if ($rid) {
		$activityManager->add_activity_role($newaid, $rid);
	}

	$info = array(
		'name' => '',
		'description' => '',
		'activity_id' => 0,
		'is_interactive' => 'y',
		'is_auto_routed' => 'n',
		'type' => 'activity'
	);

	$_REQUEST['activity_id'] = 0;
	$gBitSmarty->assign('info', $info);
	// remove transitions
	$activityManager->remove_activity_transitions($_REQUEST['pid'], $newaid);

	if (isset($_REQUEST["add_tran_from"])) {
		foreach ($_REQUEST["add_tran_from"] as $actfrom) {
			$activityManager->add_transition($_REQUEST['pid'], $actfrom, $newaid);
		}
	}

	if (isset($_REQUEST["add_tran_to"])) {
		foreach ($_REQUEST["add_tran_to"] as $actto) {
			$activityManager->add_transition($_REQUEST['pid'], $newaid, $actto);
		}
	}
}

// Get all the process roles
$all_roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, 'name_asc', '');
$gBitSmarty->assign_by_ref('all_roles', $all_roles['data']);

// Get activity roles
if ($_REQUEST['activity_id']) {
	$roles = $activityManager->get_activity_roles($_REQUEST['activity_id']);
} else {
	$roles = array();
}

$gBitSmarty->assign('roles', $roles);

$where = '';

if (isset($_REQUEST['filter'])) {
	$wheres = array();

	if ($_REQUEST['filter_type']) {
		$wheres[] = " type='" . $_REQUEST['filter_type'] . "'";
	}

	if ($_REQUEST['filter_interactive']) {
		$wheres[] = " is_interactive='" . $_REQUEST['filter_interactive'] . "'";
	}

	if ($_REQUEST['filter_autoroute']) {
		$wheres[] = " is_auto_routed='" . $_REQUEST['filter_autoroute'] . "'";
	}

	$where = implode('and', $wheres);
}

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'flow_num_asc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

if (!isset($_REQUEST['were']))
	$_REQUEST['where'] = $where;

$gBitSmarty->assign('sort_mode', $_REQUEST['sort_mode']);
$gBitSmarty->assign('find', $_REQUEST['find']);
$gBitSmarty->assign('where', $_REQUEST['where']);

// Transitions
if (isset($_REQUEST["delete_tran"])) {
	foreach (array_keys($_REQUEST["transition"])as $item) {
		$parts = explode("_", $item);

		$activityManager->remove_transition($parts[0], $parts[1]);
	}
}

if (isset($_REQUEST['add_trans'])) {
	$activityManager->add_transition($_REQUEST['pid'], $_REQUEST['act_from_id'], $_REQUEST['act_to_id']);
}

if (isset($_REQUEST['filter_tran_name']) && $_REQUEST['filter_tran_name']) {
	$transitions = $activityManager->get_process_transitions($_REQUEST['pid'], $_REQUEST['filter_tran_name']);
} else {
	$transitions = $activityManager->get_process_transitions($_REQUEST['pid'], '');
}

if (!isset($_REQUEST['filter_tran_name']))
	$_REQUEST['filter_tran_name'] = '';

$gBitSmarty->assign('filter_tran_name', $_REQUEST['filter_tran_name']);
$gBitSmarty->assign_by_ref('transitions', $transitions);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$proc_info['is_valid'] = $valid ? 'y' : 'n';

if ($valid && isset($_REQUEST['activate_proc'])) {
	$processManager->activate_process($_REQUEST['pid']);

	$proc_info['is_active'] = 'y';
}

if (isset($_REQUEST['deactivate_proc'])) {
	$processManager->deactivate_process($_REQUEST['pid']);

	$proc_info['is_active'] = 'n';
}

$gBitSmarty->assign_by_ref('proc_info', $proc_info);

$errors = array();

if (!$valid) {
	$errors = $activityManager->get_error();
}

$gBitSmarty->assign('errors', $errors);

//Now information for activities in this process
$activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, $_REQUEST['sort_mode'], $_REQUEST['find'], $where);

//Now check if the activity is or not part of a transition
if (isset($_REQUEST['activity_id'])) {
	for ($i = 0; $i < count($activities["data"]); $i++) {
		$id = $activities["data"][$i]['activity_id'];

		$activities["data"][$i]['to']
			= $activityManager->transition_exists($_REQUEST['pid'], $_REQUEST['activity_id'], $id) ? 'y' : 'n';
		$activities["data"][$i]['from']
			= $activityManager->transition_exists($_REQUEST['pid'], $id, $_REQUEST['activity_id']) ? 'y' : 'n';
	}
}

// Set activities
if (isset($_REQUEST["update_act"])) {
	for ($i = 0; $i < count($activities["data"]); $i++) {
		$id = $activities["data"][$i]['activity_id'];

		if (isset($_REQUEST['activity_inter']["$id"])) {
			$activities["data"][$i]['is_interactive'] = 'y';

			$activityManager->set_interactivity($_REQUEST['pid'], $id, 'y');
		} else {
			$activities["data"][$i]['is_interactive'] = 'n';

			$activityManager->set_interactivity($_REQUEST['pid'], $id, 'n');
		}

		if (isset($_REQUEST['activity_route']["$id"])) {
			$activities["data"][$i]['is_auto_routed'] = 'y';

			$activityManager->set_autorouting($_REQUEST['pid'], $id, 'y');
		} else {
			$activities["data"][$i]['is_auto_routed'] = 'n';

			$activityManager->set_autorouting($_REQUEST['pid'], $id, 'n');
		}
	}
}

$arYears = array ();
$arMonths = array();
$arDays = array();
$arHours = array();
$arminutes = array();
for ($i=0;$i<=$maxExpirationTime['months'];$i++)
	$arMonths[$i] = $i;
for ($i=0;$i<=$maxExpirationTime['years'];$i++)
	$arYears[$i] = $i;
for ($i=0;$i<=$maxExpirationTime['days'];$i++)
	$arDays["$i"] = $i;
for ($i=0;$i<=$maxExpirationTime['hours'];$i++)
	$arHours["$i"] = $i;
for ($i=0;$i<=$maxExpirationTime['minutes'];$i++)
	$arminutes["$i"] = $i;
$gBitSmarty->assign("years",$arYears);
$gBitSmarty->assign("months",$arMonths);
$gBitSmarty->assign("days",$arDays);
$gBitSmarty->assign("hours",$arHours);
$gBitSmarty->assign("minutes",$arminutes);



$gBitSmarty->assign_by_ref('items', $activities['data']);

$activityManager->build_process_graph($_REQUEST['pid']);

$gBitSystem->display( 'bitpackage:Galaxia/g_admin_activities.tpl', tra('Admin Activites') );

?>
