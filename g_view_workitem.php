<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_view_workitem.php,v 1.8 2010/02/08 21:27:22 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
require_once( '../kernel/setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessMonitor.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'p_galaxia_use', "Permission denied you cannot use the workflow" );

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


$gBitSystem->display( 'bitpackage:Galaxia/g_view_workitem.tpl', tra("View Workitem") , array( 'display_mode' => 'display' ));

?>
