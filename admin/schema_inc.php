<?php
include_once( GALAXIA_PKG_PATH.'config.php' );

$tables = array(

'galaxia_activities' => "
	activity_id I4 AUTO PRIMARY,
	name C(80),
	normalized_name C(80),
	p_id I4 NOTNULL,
	type C(12) NOTNULL,
	is_auto_routed C(1),
	flow_num I8,
	is_interactive C(1),
	expiration_time I8 DEFAULT 0,
	last_modified I8,
	description X
",

'galaxia_activity_roles' => "
	activity_id I4 PRIMARY,
	role_id I4 PRIMARY
",

'galaxia_instance_activities' => "
	instance_id I4 PRIMARY,
	activity_id I4 PRIMARY,
	started I8 NOTNULL,
	ended I8 NOTNULL,
	user_id I4,
	status C(12) NOTNULL
",

'galaxia_instance_comments' => "
	c_id I4 AUTO PRIMARY,
	instance_id I4 NOTNULL,
	user_id I4,
	activity_id I4,
	hash C(32),
	title C(250),
	comment X,
	activity C(80),
	timestamp I8
",

'galaxia_instances' => "
	instance_id I4 AUTO PRIMARY,
	name C(80),
	p_id I4 NOTNULL,
	started I8,
	owner_id I4,
	next_activity I8,
	next_group_id I4,
	ended I8,
	status C(12) NOTNULL,
	properties X
",

'galaxia_processes' => "
	p_id I4 AUTO PRIMARY,
	procname C(80),
	is_valid C(1),
	is_active C(1),
	version C(12),
	description X,
	last_modified I8,
	normalized_name C(80)
",

'galaxia_roles' => "
	role_id I4 AUTO PRIMARY,
	p_id I4 NOT NULL,
	last_modified I8,
	name C(80),
	description X
",

'galaxia_transitions' => "
	p_id I4 NOTNULL,
	act_from_id I4 PRIMARY,
	act_to_id I4 PRIMARY
",

'galaxia_group_roles' => "
	p_id I4 NOT NULL,
	role_id I4 AUTO PRIMARY,
	group_id I4 PRIMARY
",

'galaxia_workitems' => "
	item_id I4 AUTO PRIMARY,
	instance_id I4 NOTNULL,
	order_id I4 NOTNULL,
	activity_id I4 NOTNULL,
	properties X,
	started I8,
	ended I8,
	user_id I4
"

);

global $gBitInstaller;

$gBitInstaller->makePackageHomeable(GALAXIA_PKG_NAME);

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( GALAXIA_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( GALAXIA_PKG_NAME, array(
	'description' => "Galaxia is a open source activity-based Workflow engine based on Openflow (http://www.openflow.it).",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'experimental',
	'dependencies' => '',
) );



// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( GALAXIA_PKG_NAME, array(
	array('bit_p_admin_workflow', 'Can admin workflow processes', 'admin', GALAXIA_PKG_NAME),
	array('bit_p_abort_instance', 'Can abort a process instance', 'editors', GALAXIA_PKG_NAME),
	array('bit_p_use_workflow', 'Can execute workflow activities', 'registered', GALAXIA_PKG_NAME),
	array('bit_p_exception_instance', 'Can declare an instance as exception', 'registered', GALAXIA_PKG_NAME),
	array('bit_p_send_instance', 'Can send instances after completion', 'registered', GALAXIA_PKG_NAME),
) );

?>
