<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
require_once( '../kernel/setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/GUI.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'p_galaxia_use', "Permission denied you cannot use the workflow" );

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

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);
$gBitSmarty->assign('where', $where);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);

$items = $GUI->gui_list_user_activities($gBitUser->getUserId(), $offset, $max_records, $sort_mode, $find, $where);
$gBitSmarty->assign('cant', $items['cant']);

$cant_pages = ceil($items["cant"] / $max_records);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $max_records));

if ($items["cant"] > ($offset + $max_records)) {
	$gBitSmarty->assign('next_offset', $offset + $max_records);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $max_records);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('items', $items["data"]);

$processes = $GUI->gui_list_user_processes($gBitUser->getUserId(), 0, -1, 'procname_asc', '', '');
$gBitSmarty->assign_by_ref('all_procs', $processes['data']);

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
					} elseif ($item['is_interactive'] == 'y' && ($item['act_type'] == 'start' || $item['act_type'] == 'standalone')) {
						if ($gBitSystem->getConfig( 'galaxia_instance_names' ) == 'y') {
							$mapline = preg_replace('/href=".*?activity_id=(\d+)/', 'href="#" onClick="var answer = prompt(\''.tra("Enter the name of this instance").':\',\'\'); while(answer == \'\')answer = prompt(\''.tra("The name is not valid. Please, enter the name again").':\',\'\'); if (answer != null) window.location = \''.GALAXIA_PKG_URL.'g_run_activity.php?activity_id=$1&ins_name=\'+answer;', $mapline);
						} else {
							$mapline = preg_replace('/href=".*?activity_id=(\d+)/', 'href="'.GALAXIA_PKG_URL.'g_run_activity.php?activity_id=$1', $mapline);
						}
						$map .= $mapline;
					}
				}
				$gBitSmarty->assign('graph', GALAXIA_PROCESSES_URL.$graph);
				$gBitSmarty->assign('map', $map);
				$gBitSmarty->assign('procname', $info['procname']);
			} else {
				$gBitSmarty->assign('graph', '');
			}
			break;
		}
	}
}

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


$gBitSystem->display( 'bitpackage:Galaxia/g_user_activities.tpl', tra('User Activities') , array( 'display_mode' => 'display' ));

?>
