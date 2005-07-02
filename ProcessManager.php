<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/src/ProcessManager/ProcessManager.php');
include_once (GALAXIA_LIBRARY.'/src/ProcessManager/InstanceManager.php');
include_once (GALAXIA_LIBRARY.'/src/ProcessManager/RoleManager.php');
include_once (GALAXIA_LIBRARY.'/src/ProcessManager/ActivityManager.php');
include_once (GALAXIA_LIBRARY.'/src/ProcessManager/GraphViz.php');

/// $roleManager is the object that will be used to manipulate roles.
$roleManager = new RoleManager();
/// $activityManager is the object that will be used to manipulate activities.
$activityManager = new ActivityManager();
/// $processManager is the object that will be used to manipulate processes.
$processManager = new ProcessManager();
/// $instanceManager is the object that will be used to manipulate instances.
$instanceManager = new InstanceManager();

if (defined('GALAXIA_LOGFILE') && GALAXIA_LOGFILE) {
    include_once (GALAXIA_LIBRARY.'/src/Observers/Logger.php');

    $logger = new Logger(GALAXIA_LOGFILE);
    $processManager->attach_all($logger);
    $activityManager->attach_all($logger);
    $roleManager->attach_all($logger);
}

?>
