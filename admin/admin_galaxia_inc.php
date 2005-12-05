<?php
// $Header: /cvsroot/bitweaver/_bit_galaxia/admin/admin_galaxia_inc.php,v 1.2 2005/12/05 23:52:31 squareing Exp $
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

$processForm = set_tab();

if( $processForm ) {
	$blogToggles = array_merge( $formGalaxia);
	foreach( $blogToggles as $item => $data ) {
		simple_set_toggle( $item );
	}
}

?>
