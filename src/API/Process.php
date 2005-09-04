<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Process.php
//! A class representing a process
/*!
This class representes the process that is being executed when an activity
is executed. You can access this class methods using $process from any activity.
No need to instantiate a new object.
*/
class Process extends Base {
  var $name;
  var $description;
  var $version;
  var $normalizedName;
  var $p_id = 0;

  /*!
  Loads a process form the database
  */
  function getProcess($p_id) {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."processes` where `p_id`=?";
    $result = $this->mDb->query($query,array($p_id));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    $this->name = $res['procname'];
    $this->description = $res['description'];
    $this->normalizedName = $res['normalized_name'];
    $this->version = $res['version'];
    $this->p_id = $res['p_id'];
  }
  
  /*!
  Gets the normalized name of the process
  */
  function getNormalizedName() {
    return $this->normalizedName;
  }
  
  /*!
  Gets the process name
  */
  function getName() {
    return $this->name;
  }
  
  /*!
  Gets the process version
  */
  function getVersion() {
    return $this->version;
  }

  /*!
  Gets information about an activity in this process by name,
  e.g. $actinfo = $process->getActivityByName('Approve CD Request');
    if ($actinfo) {
      $some_url = 'g-run_activity.php?activity_id=' . $actinfo['activity_id'];
    }
  */
  function getActivityByName($actname) {
    // Get the activity data
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=? and `name`=?";
    $p_id = $this->p_id;
    $result = $this->mDb->query($query,array($p_id,$actname));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
  }

}

?>
