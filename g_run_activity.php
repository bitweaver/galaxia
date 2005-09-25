<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_run_activity.php,v 1.1.1.1.2.4 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

require( GALAXIA_PKG_PATH.'API.php' );
//include_once( WEBMAIL_PKG_PATH."htmlMimeMail.php");

$__activity_completed = false;

$gBitSystem->verifyPackage( 'galaxia' );

if (!isset($_REQUEST['auto'])) {
	$gBitSystem->verifyPermission( 'bit_p_use_workflow', "Permission denied you cannot use the workflow" );
}

// Determine the activity using the activity_id request
// parameter and get the activity information
// load then the compiled version of the activity
if (!isset($_REQUEST['activity_id'])) {
	galaxia_show_error("No activity indicated");
	die;
}

$activity = $baseActivity->getActivity($_REQUEST['activity_id']);
$process->getProcess($activity->getProcessId());

// Get user roles
$user_id = $gBitUser->getUserId();

// Get activity roles
$act_roles = $activity->getRoles();
$user_roles = $activity->getUserRoles($user_id);

// Only check roles if this is an interactive
// activity
if ($activity->is_interactive() == 'y') {
	if (!count(array_intersect($act_roles, $user_roles))) {
		galaxia_show_error("You cant execute this activity");
		die;
	}
}

$act_role_names = $activity->getActivityRoleNames($user_id);

foreach ($act_role_names as $role) {
	$name = 'bw-role-' . $role['name'];

	if (in_array($role['role_id'], $user_roles)) {
		$gBitSmarty->assign("$name", 'y');

		$$name = 'y';
	} else {
		$gBitSmarty->assign("$name", 'n');

		$$name = 'n';
	}
}
if (!isset($_REQUEST['__post'])) {
$source = GALAXIA_PROCESSES . $process->getNormalizedName(). '/compiled/' . $activity->getNormalizedName(). '.php';
$shared = GALAXIA_PROCESSES . $process->getNormalizedName(). '/code/shared.php';

// Existing variables here:
// $process, $activity, $instance (if not standalone)
// Include the shared code
include_once ($shared);

// Now do whatever you have to do in the activity
include_once ($source);
}
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
	$instance->getInstance($_REQUEST['iid']);
	$instance->replace_instance_comment($_REQUEST['__cid'], $activity->getActivityId(), $activity->getName(),
		$user_id, $_REQUEST['__title'], $_REQUEST['__comment']);
}

$__comments = $instance->get_instance_comments($activity->getActivityId());

// This goes to the end part of all activities
// If this activity is interactive then we have to display the template
if (!isset($_REQUEST['auto']) && $__activity_completed && $activity->is_interactive() && !isset($_REQUEST['__post'])) {
	$gBitSmarty->assign('procname', $process->getName());
	$gBitSmarty->assign('procversion', $process->getVersion());
	$gBitSmarty->assign('actname', $activity->getName());
	$gBitSmarty->assign('actid',$activity->getActivityId());
	$gBitSmarty->assign('post','n');
	$gBitSmarty->assign('iid',$instance->instance_id);
	$gBitSystem->display( 'bitpackage:Galaxia/g_activity_completed.tpl', tra("Activity Completed") );
} 
elseif (!isset($_REQUEST['auto']) && $activity->is_interactive() && isset($_REQUEST['__post'])) {
	$gBitSmarty->assign('procname', $process->getName());
	$gBitSmarty->assign('procversion', $process->getVersion());
	$gBitSmarty->assign('actname', $activity->getName());
	$gBitSmarty->assign('actid',$activity->getActivityId());
	$gBitSmarty->assign('title',$_REQUEST['__title']);
	$gBitSmarty->assign('comment',$_REQUEST['__comment']);
	$gBitSmarty->assign('post','y');
	$gBitSystem->display( 'bitpackage:Galaxia/g_activity_completed.tpl', tra("Activity Completed") );
}
else {
	if (!isset($_REQUEST['auto']) && $activity->is_interactive()) {
		$section = 'workflow';

		$template = $activity->getNormalizedName(). '.tpl';
		$gBitSystem->display( GALAXIA_PROCESSES . $process->getNormalizedName(). '/code/templates/' . $template, tra("Run Activity") );
	}
}

?>
