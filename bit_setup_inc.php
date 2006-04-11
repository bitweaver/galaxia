<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'galaxia',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'galaxia' ) ) {
	if ($gBitUser->hasPermission( 'p_galaxia_use' ))
		$gBitSystem->registerAppMenu( GALAXIA_PKG_NAME, 'Workflow', GALAXIA_PKG_URL.'g_user_processes.php', 'bitpackage:Galaxia/menu_galaxia.tpl' );

	// **********  GALAXIA  ************
/*		if ($p_galaxia_admin == 'y')
	{
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
