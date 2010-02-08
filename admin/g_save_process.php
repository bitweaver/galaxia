<?php

// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/g_save_process.php,v 1.6 2010/02/08 21:27:22 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
require_once( '../../kernel/setup_inc.php' );

include_once( GALAXIA_PKG_PATH.'/ProcessManager.php');

$gBitSystem->verifyPackage( 'galaxia' );
$gBitSystem->verifyPermission( 'p_galaxia_admin', "Permission denied you cannot admin the workflow" );

// The galaxia process manager PHP script.

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
	$_REQUEST['pid'] = 0;

$info = $processManager->get_process($_REQUEST['pid']);
header ('Content-type: text/xml');
header ('Content-Disposition: inline; filename=' . $info['normalized_name'] );
echo ('<?xml version="1.0"?>');
$data = $processManager->serialize_process($_REQUEST['pid']);
echo $data;

?>
