<?php

/**
 * Configuration of the Galaxia Workflow Engine for TikiWiki
 */

// Common prefix used for all database table names, e.g. galaxia_
if (!defined('GALAXIA_TABLE_PREFIX')) {
    define('GALAXIA_TABLE_PREFIX', BIT_DB_PREFIX.'galaxia_');
}

// Directory containing the Galaxia library, e.g. lib/Galaxia
if (!defined('GALAXIA_LIBRARY')) {
    define('GALAXIA_LIBRARY', dirname(__FILE__));
}

// Directory where the Galaxia processes will be stored, e.g. lib/Galaxia/processes
if (!defined('GALAXIA_PROCESSES')) {
    global $bitdomain;
    // Note: this directory must be writeable by the webserver !
    define('GALAXIA_PROCESSES', STORAGE_PKG_PATH . $bitdomain . 'galaxia/processes/');
    mkdir_p( GALAXIA_PROCESSES );
}

// Directory where the Galaxia processes will be references
if (!defined('GALAXIA_PROCESSES_URL')) {
    global $bitdomain;
    // Note: this directory must be writeable by the webserver !
    define('GALAXIA_PROCESSES_URL', STORAGE_PKG_URL . $bitdomain . 'galaxia/processes/');
}

// Directory where a *copy* of the Galaxia activity templates will be stored, e.g. templates
// Define as '' if you don't want to copy templates elsewhere
if (!defined('GALAXIA_TEMPLATES')) {
    // Note: this directory must be writeable by the webserver !
    define('GALAXIA_TEMPLATES', GALAXIA_PROCESSES . '/templates');
	mkdir_p( GALAXIA_PROCESSES . '/templates' );
}

// Default header to be added to new activity templates
if (!defined('GALAXIA_TEMPLATE_HEADER')) {
    define('GALAXIA_TEMPLATE_HEADER', '{*Smarty template*}');
    //define('GALAXIA_TEMPLATE_HEADER', '');
}

// File where the ProcessManager logs for Galaxia will be saved, e.g. lib/Galaxia/log/pm.log
// Define as '' if you don't want to use logging
if (!defined('GALAXIA_LOGFILE')) {
    // Note: this file must be writeable by the webserver !
    //define('GALAXIA_LOGFILE', GALAXIA_PROCESSES . '/log/pm.log');
    define('GALAXIA_LOGFILE', '');
}

// Directory containing the GraphViz 'dot' and 'neato' programs, in case
// your webserver can't find them via its PATH environment variable
if (!defined('GRAPHVIZ_BIN_DIR')) {
    define('GRAPHVIZ_BIN_DIR', '');
    //define('GRAPHVIZ_BIN_DIR', 'd:/wintools/ATT/GraphViz/bin');
}

/**
 * TikiWiki-specific adaptations
 */
// Specify how error messages should be shown
if (!function_exists('galaxia_show_error')) {
    function galaxia_show_error($msg)
    {
        global $gBitSystem;
        $gBitSystem->error(tra($msg));
    }
}

// Specify how to execute a non-interactive activity (for use in src/API/Instance.php)
if (!function_exists('galaxia_execute_activity')) {
    function galaxia_execute_activity($activity_id = 0, $iid = 0, $auto = 1)
    {
      // Now execute the code for the activity but we are in a method!
      // so just use an fopen with http mode
      $parsed = parse_url($_SERVER["REQUEST_URI"]);
      $URI = httpPrefix().$parsed["path"];
      $parts = explode('/',$URI);
      $parts[count($parts)-1] = "g_run_activity.php?activity_id=$activity_id&amp;iid=$iid&amp;auto=$auto";
      $URI = implode('/',$parts);
      $fp = fopen($URI,"r");
      $data = '';
      if (!$fp) {
        trigger_error(tra("Fatal error: cannot execute automatic activity $activity_id"),E_USER_WARNING);
        die;
      }
      while (!feof($fp)) {
        $data.=fread($fp,8192);
      }
	  
      /*
      if(!empty($data)) {
        trigger_error(tra("Fatal error: automatic activity produced some output:$data"),E_USER_WARNING);      
      }
      */
      fclose($fp);

    }
}

?>
