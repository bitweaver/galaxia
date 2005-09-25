<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_view_workitem.php,v 1.1.1.1.2.4 2005/09/25 11:36:31 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../bit_setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessMonitor.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'bit_p_use_workflow', "Permission denied you cannot use the workflow" );

if (!isset($_REQUEST['item_id'])) {
	galaxia_show_error("No item indicated");
	die;
}

$wi = $processMonitor->monitor_get_workitem($_REQUEST['item_id']);
$gBitSmarty->assign_by_ref('wi', $wi);

$gBitSmarty->assign('stats', $processMonitor->monitor_stats());

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'item_id'
);


$gBitSystem->display( 'bitpackage:Galaxia/g_view_workitem.tpl', tra("View Workitem") );

?>
