<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

	'BWR1' => array(
		'BWR2' => array(
array( 'DATADICT' => array(
	array( 'RENAMECOLUMN' => array(
		'galaxia_instance_comments' => array(
			'`timestamp`' => '`com_timestamp` I8'
		),
		'galaxia_activities' => array(
			'`type`' => '`act_type` C(12) NOTNULL'
		),
	)),
)),

		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( GALAXIA_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
