<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/g_user_activities.php,v 1.1.1.1.2.3 2005/07/16 13:08:08 wolff_borg Exp $

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

if (count($processes['data']) == 1 && empty($_REQUEST['filter_process'])) {
    $_REQUEST['filter_process'] = $processes['data'][0]['p_id'];
}

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
    $actid2item = array();
    foreach (array_keys($items["data"]) as $index) {
        $actid2item[$items["data"][$index]['activity_id']] = $index;
    }
    foreach ($processes['data'] as $info) {
        if ($info['p_id'] == $_REQUEST['filter_process'] && !empty($info['normalized_name'])) {
            $graph = $info['normalized_name']."/graph/".$info['normalized_name'].".png";
            $mapfile = $info['normalized_name']."/graph/".$info['normalized_name'].".map";
            if (file_exists(GALAXIA_PROCESSES.$graph) && file_exists(GALAXIA_PROCESSES.$mapfile)) {
                $maplines = file(GALAXIA_PROCESSES.$mapfile);
                $map = '';
                foreach ($maplines as $mapline) {
                    if (!preg_match('/activity_id=(\d+)/', $mapline, $matches)) continue;
                    $actid = $matches[1];
                    if (!isset($actid2item[$actid])) continue;
                    $index = $actid2item[$actid];
                    $item = $items['data'][$index];
                    if ($item['instances'] > 0) {
                        $url = GALAXIA_PKG_URL."g_user_instances.php?filter_process=".$info['p_id'];
                        $mapline = preg_replace('/href=".*?activity_id/', 'href="' . $url . '&amp;filter_activity', $mapline);
                        $map .= $mapline;
                    } elseif ($item['is_interactive'] == 'y' && ($item['type'] == 'start' || $item['type'] == 'standalone')) {
                        $mapline = preg_replace('/href=".*?activity_id=(\d+)/', 'href="#" onClick="var answer = prompt(\''.tra("Enter the name of this instance").':\',\'\'); while(answer == \'\')answer = prompt(\''.tra("The name is not valid. Please, enter the name again").':\',\'\'); if (answer != null) window.location = \''.GALAXIA_PKG_URL.'g_run_activity.php?activity_id=$1&ins_name=\'+answer;', $mapline);
                        $map .= $mapline;
                    }
                }
                $smarty->assign('graph', GALAXIA_PROCESSES_URL.$graph);
                $smarty->assign('map', $map);
                $smarty->assign('procname', $info['procname']);
            } else {
                $smarty->assign('graph', '');
            }
            break;
        }
    }
}

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
	'p_id',
	'filter_process'
);


$gBitSystem->display( 'bitpackage:Galaxia/g_user_activities.tpl', tra('User Activities') );

?>
