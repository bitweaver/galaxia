<?php
// $Header$
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

$formGalaxia = array(
	"galaxia_instance_names" => array(
		'label' => 'Instance Names',
		'note' => 'Enable Instance Names',
	),
);
$gBitSmarty->assign( 'formGalaxia', $formGalaxia );

if( !empty( $_REQUEST['list_submit'] ) ) {
	foreach( $formGalaxia as $item => $data ) {
		simple_set_toggle( $item, GALAXIA_PKG_NAME );
	}
}

?>
