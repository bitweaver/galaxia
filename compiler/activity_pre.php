<?php
//Code to be executed before an activity
// If we didn't retrieve the instance before
if(empty($instance->instance_id)) {
  // This activity needs an instance to be passed to 
  // be started, so get the instance into $instance.
  if(isset($_REQUEST['iid'])) {
    $instance->getInstance($_REQUEST['iid']);
  } else {
    // defined in lib/Galaxia/config.php
    galaxia_show_error(tra("No instance indicated"));
    die;  
  }
}
if ($instance->getActivityStatus($_REQUEST['activity_id']) == "completed")
{
	galaxia_show_error(tra("This instance of activity is already complete"));
}

// Set the current user for this activity
if(!empty($instance->instance_id) && !empty($activity->activity_id)) {
  $instance->setActivityUser($activity->activity_id,$gBitUser->getUserId());
}

?>
