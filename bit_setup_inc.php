<?php
global $gBitSystem, $gBitUser;

$registerHash = array(
	'package_name' => 'galaxia',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'galaxia' ) ) {
	if( $gBitUser->hasPermission( 'p_galaxia_use' ) ) {
		$menuHash = array(
			'package_name'  => GALAXIA_PKG_NAME,
			'index_url'     => GALAXIA_PKG_URL.'g_user_processes.php',
			'menu_template' => 'bitpackage:galaxia/menu_galaxia.tpl',
		);
		$gBitSystem->registerAppMenu( $menuHash );
	}

/*
	// **********  GALAXIA  ************
	if ($p_galaxia_admin == 'y') {
		$perms = $userlib->get_permissions(0, -1, 'perm_name_desc', '', 'workflow');
		foreach ($perms["data"] as $perm)
		{
			$perm = $perm["perm_name"];
			$gBitSmarty->assign("$perm", 'y');
			$$perm = 'y';
		}
	}
*/
}
?>
