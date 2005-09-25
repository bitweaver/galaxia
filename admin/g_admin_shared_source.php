<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_admin_shared_source.php,v 1.1.1.1.2.4 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessManager.php');

// The galaxia source editor for activities and processes.
$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_admin_workflow', "Permission denied you cannot admin the workflow" );


if (!isset($_REQUEST['pid'])) {
	galaxia_show_error("No process indicated");
	die;
}

$gBitSmarty->assign('pid', $_REQUEST['pid']);

if (isset($_REQUEST['code'])) {
	unset($_REQUEST['template']);
	$_REQUEST['save'] = 'y';
} elseif (isset($_REQUEST['template'])) {
	$_REQUEST['save'] = 'y';
}

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']=GALAXIA_PROCESSES_URL.$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";
$gBitSmarty->assign_by_ref('proc_info',$proc_info);

$procname = $proc_info['normalized_name'];

$gBitSmarty->assign('warn', '');

if (!isset($_REQUEST['activity_id']))
	$_REQUEST['activity_id'] = 0;

$gBitSmarty->assign('activity_id', $_REQUEST['activity_id']);

if ($_REQUEST['activity_id']) {
	$act_info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST['activity_id']);

	$actname = $act_info['normalized_name'];

	if ($act_info['is_interactive'] == 'y' && isset($_REQUEST['template'])) {
		$gBitSmarty->assign('template', 'y');

		$source = GALAXIA_PROCESSES.$procname."/code/templates/$actname" . '.tpl';
	} else {
		$gBitSmarty->assign('template', 'n');

		$source = GALAXIA_PROCESSES.$procname."/code/activities/$actname" . '.php';
	}

	// Then editing an activity
	$gBitSmarty->assign('act_info', $act_info);
} else {
	// Then editing shared code
	$source = GALAXIA_PROCESSES.$procname."/code/shared.php";
}

//First of all save
if (isset($_REQUEST['save']) && isset($_REQUEST['source'])) {

	mkdir_p(dirname($_REQUEST['source_name']));	
	$fp = fopen($_REQUEST['source_name'], "w");

	fwrite($fp, $_REQUEST['source']);
	fclose ($fp);

	if ($_REQUEST['activity_id']) {
		$activityManager->compile_activity($_REQUEST['pid'], $_REQUEST['activity_id']);
	}
}

$gBitSmarty->assign('source_name', $source);

$data = "";
if (file_exists($source)) {
	$fp = fopen($source, "r");
	$data = fread($fp, filesize($source));
	fclose ($fp);
} 
$gBitSmarty->assign('data', $data);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$errors = array();

if (!$valid) {
	$errors = $activityManager->get_error();

	$proc_info['is_valid'] = 'n';
} else {
	$proc_info['is_valid'] = 'y';
}

$gBitSmarty->assign('errors', $errors);

$activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, 'name_asc', '');
$gBitSmarty->assign_by_ref('items', $activities['data']);


$gBitSystem->display( 'bitpackage:Galaxia/g_admin_shared_source.tpl', tra('Admin Process Sources') );

?>
