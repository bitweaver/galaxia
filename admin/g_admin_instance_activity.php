<?php


// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_admin_instance_activity.php,v 1.1.2.2 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// This file was created by Víctor Codocedo on 01/31/2005

require_once( '../../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'ProcessManager.php');
include_once( GALAXIA_PKG_PATH.'API.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );


if (!isset($_REQUEST['iid'])) {
	galaxia_show_error(tra("No instance indicated"));
	die;
}

if (!isset($_REQUEST['aid'])) {
	galaxia_show_error(tra("No activity indicated"));
}

$gBitSmarty->assign('iid', $_REQUEST['iid']);
$gBitSmarty->assign('aid', $_REQUEST['aid']);

// Get workitems and list the workitems with an option to edit workitems for
// this instance
if (isset($_REQUEST['save'])) {

	//status, user
	$instanceManager->set_instance_status($_REQUEST['iid'], $_REQUEST['status']);
	$instanceManager->set_instance_name($_REQUEST['iid'],$_REQUEST['name']);
	$instanceManager->set_instance_user($_REQUEST['iid'], $_REQUEST['user']);
	//y luego acts[activity_id][user] para reasignar users
	if (isset($_REQUEST['acts'])) {
		foreach (array_keys($_REQUEST['acts'])as $act) {
		$instanceManager->set_instance_user($_REQUEST['iid'], $act, $_REQUEST['acts'][$act]);
		}
	}

	if ($_REQUEST['sendto']) {
		$instanceManager->set_instance_destination($_REQUEST['iid'], $_REQUEST['sendto']);
	}
//process sendto
	$instanceManager->set_instance_user($_REQUEST['iid'],$_REQUEST['aid'],$_REQUEST['user_id']);
}

// Get the instance and set instance information
$ins_info = $instanceManager->get_instance($_REQUEST['iid']);
$gBitSmarty->assign_by_ref('ins_info', $ins_info);

// Get the process from the instance and set information
$proc_info = $processManager->get_process($ins_info['p_id']);
$gBitSmarty->assign_by_ref('proc_info', $proc_info);

// Process activities
$activities = $activityManager->list_activities($ins_info['p_id'], 0, -1, 'flow_num_asc', '', '');
$gBitSmarty->assign('activities', $activities['data']);

// Users
$users = $gBitUser->get_users(0, -1, 'login_asc', '');
$gBitSmarty->assign_by_ref('users', $users['data']);

$props = $instanceManager->get_instance_properties($_REQUEST['iid']);

if (isset($_REQUEST['unsetprop'])) {

	unset ($props[$_REQUEST['unsetprop']]);

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (!is_array($props))
	$props = array();

$gBitSmarty->assign_by_ref('props', $props);

if (isset($_REQUEST['addprop'])) {
	
	$props[$_REQUEST['name']] = $_REQUEST['value'];

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (isset($_REQUEST['saveprops'])) {
	
	foreach (array_keys($_REQUEST['props'])as $key) {
		$props[$key] = $_REQUEST['props'][$key];
	}

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

$acts = $instanceManager->get_instance_activities($_REQUEST['iid'],$_REQUEST['aid']);
$gBitSmarty->assign_by_ref('acts', $acts);

$instance->getInstance($_REQUEST['iid']);

$user_id = $gBitUser->getUserId();
// Process comments
if (isset($_REQUEST['__removecomment'])) {

	$__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);

	if ($__comment['user_id'] == $user_id or $gBitUser->hasPermission('bit_p_admin_workflow')) {
		$instance->remove_instance_comment($_REQUEST['__removecomment']);
	}
}

$gBitSmarty->assign_by_ref('__comments', $__comments);

if (!isset($_REQUEST['__cid']))
	$_REQUEST['__cid'] = 0;

if (isset($_REQUEST['__post'])) {

	$instance->replace_instance_comment($_REQUEST['__cid'], $_REQUEST['aid'], '', $user_id, $_REQUEST['__title'], $_REQUEST['__comment']);
}

$__comments = $instance->get_instance_comments($_REQUEST['aid']);
$gBitSmarty->assign('comments',$__comments);


$gBitSystem->display( 'bitpackage:Galaxia/g_admin_instance_activity.tpl', tra("Admin Instance Activity") );

?>
