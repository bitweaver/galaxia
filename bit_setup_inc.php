<?php
	global $gBitSystem;
	$gBitSystem->registerPackage( 'galaxia', dirname( __FILE__).'/' );

	if( $gBitSystem->isPackageActive( 'galaxia' ) ) {
		if ($gBitUser->hasPermission( 'bit_p_use_workflow' ))
			$gBitSystem->registerAppMenu( 'galaxia', 'Workflow', GALAXIA_PKG_URL.'g_user_processes.php', 'bitpackage:Galaxia/menu_galaxia.tpl' );

		// **********  GALAXIA  ************
/*		if ($bit_p_admin_workflow == 'y')
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
