<?php
// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/admin_galaxia_inc.php,v 1.1.2.2 2006/01/08 08:57:50 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$formGalaxia = array(
	"galaxia_instance_names" => array(
		'label' => 'Instance Names',
		'note' => 'Enable Instance Names',
	),
);
$gBitSmarty->assign( 'formGalaxia', $formGalaxia );

if( !empty( $_REQUEST['list_submit'] ) ) {
	foreach( $formGalaxia as $item => $data ) {
		simple_set_toggle( $item );
	}
}

?>
