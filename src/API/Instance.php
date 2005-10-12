<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Instance
//! A class representing a process instance.
/*!
This class represents a process instance, it is used when any activity is
executed. The $instance object is created representing the instance of a
process being executed in the activity or even a to-be-created instance
if the activity is a start activity.
*/
class Instance extends Base {
  var $properties = Array();
  var $owner_id;
  var $status = '';
  var $started;
  var $name = '';
  var $next_activity;
  var $next_group_id;
  var $ended;
  /// Array of asocs(activity_id,status,started,user)
  var $activities = Array();
  var $p_id;
  var $instance_id = 0;
  /// An array of workitem ids
  var $workitems = Array(); 
  
  /*!
  Method used to load an instance data from the database.
  */
  function getInstance($instance_id) {
    // Get the instance data
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instances` where `instance_id`=?";
    $result = $this->mDb->query($query,array((int)$instance_id));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();

    //Populate 
    $this->properties = unserialize($res['properties']);
    $this->status = $res['status'];
    $this->p_id = $res['p_id'];
    $this->instance_id = $res['instance_id'];
    $this->owner_id = $res['owner_id'];
    $this->started = $res['started'];
    $this->name = $res['name'];
    $this->ended = $res['ended'];
    $this->next_activity = $res['next_activity'];
    $this->next_group_id = $res['next_group_id'];
    // Get the activities where the instance is (ids only is ok)
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_activities` where  `instance_id`=?";
    $result = $this->mDb->query($query,array((int)$instance_id));    
    while($res = $result->fetchRow()) {
      $this->activities[]=$res;
    }    
  }
  
  /*! 
  Sets the next activity to be executed, if the current activity is
  a switch activity the complete() method will use the activity setted
  in this method as the next activity for the instance. 
  Note that this method receives an activity name as argument. (Not an Id)
  */
  function setNextActivity($actname) {
    $p_id = $this->p_id;
    $actname=trim($actname);
    $aid = $this->mDb->getOne("select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=? and `name`=?",array($p_id,$actname));
    if(!$this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=? and `p_id`=?",array($aid,$p_id))) {
      trigger_error(tra('Fatal error: setting next activity to an unexisting activity'),E_USER_WARNING);
    }
    $this->next_activity=$aid;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `next_activity`=? where `instance_id`=?";
    $this->mDb->query($query,array((int)$aid,(int)$this->instance_id));
  }

  /*!
  This method can be used to set the user that must perform the next 
  activity of the process. this effectively "assigns" the instance to
  some user.
  */
  function setNextGroup($group_id) {
    $p_id = $this->p_id;
    $this->next_group_id = $group_id;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `next_group_id`=? where `instance_id`=?";
    $this->mDb->query($query,array($group_id,(int)$this->instance_id));
  }
 
  /*!
   \private
   Creates a new instance.
   This method is called in start activities when the activity is completed
   to create a new instance representing the started process.
  */
  function _createNewInstance($activity_id,$user_id) {
    // Creates a new instance setting up started,ended,user
    // and status
    $pid = $this->mDb->getOne("select `p_id` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array((int)$activity_id));
    $this->status = 'active';
    $this->next_activity = 0;
    $this->setNextGroup(NULL);
    $this->p_id = $pid;
    $now = date("U");
    $this->started=$now;
    $this->owner_id = $user_id;
    $props=serialize($this->properties);
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instances`(`started`,`ended`,`status`, `name`, `p_id`,`owner_id`,`properties`) values(?,?,?,?,?,?,?)";
    $this->mDb->query($query,array($now,0,'active',$this->name,$pid,$user_id,$props));
    $this->instance_id = $this->mDb->getOne("select max(`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` where `started`=? and `owner_id`=?",array((int)$now,$user_id));
    $iid=$this->instance_id;
    
    // Now update the properties!
    $props = serialize($this->properties);
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`=? where `instance_id`=?";
    $this->mDb->query($query,array($props,(int)$iid));

    // Then add in ".GALAXIA_TABLE_PREFIX."instance_activities an entry for the
    // activity the user and status running and started now
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instance_activities`(`instance_id`,`activity_id`,`user_id`,`started`,`ended`,`status`) values(?,?,?,?,?,?)";
    $this->mDb->query($query,array((int)$iid,(int)$activity_id,$user_id,(int)$now,0,'running'));
  }
  
  /*! 
  Sets a property in this instance. This method is used in activities to
  set instance properties. Instance properties are inemdiately serialized.
  */
  function set($name,$value) {
    $this->properties[$name] = $value;
    $props = serialize($this->properties);
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`=? where `instance_id`=?";
    $this->mDb->query($query,array($props,$this->instance_id));
  }
  
  /*! 
  Gets the value of an instance property.
  */
  function get($name) {
    if(isset($this->properties[$name])) {
      return $this->properties[$name];
    } else {
      return false;
    }
  }
  
  /*! 
  Returns an array of asocs describing the activities where the instance
  is present, can be more than one activity if the instance was "splitted"
  */
  function getActivities() {
    return $this->activities;
  }
  
  /*! 
  Gets the instance status can be
  'completed', 'active', 'aborted' or 'exception'
  */
  function getStatus() {
    return $this->status;
  }
  
  /*! 
  Sets the instance status , the value can be:
  'completed', 'active', 'aborted' or 'exception'
  */
  function setStatus($status) {
    $this->status = $status; 
    // and update the database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=? where `instance_id`=?";
    $this->mDb->query($query,array($status,(int)$this->instance_id));  
  }
  
  /*!
  Returns the instance_id
  */
  function getInstanceId() {
    return $this->instance_id;
  }
  
  /*! 
  Returns the processId for this instance
  */
  function getProcessId() {
    return $this->p_id;
  }
  
  /*! 
  Returns the name associated to the instance
  */
  function getName() {
    return $this->name;
  }
  
    /*! 
  Sets the instance name user 
  */
  function setName($name) {
    $this->name = $name;
    // save database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `name`=? where `instance_id`=?";
    $this->mDb->query($query,array($name,(int)$this->instance_id));  
  }
  
  /*! 
  Returns the user that created the instance
  */
  function getOwner() {
    return $this->owner_id;
  }
  
  /*! 
  Sets the instance creator user 
  */
  function setOwner($user_id) {
    $this->owner_id = $user_id;
    // save database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `owner_id`=? where `instance_id`=?";
    $this->mDb->query($query,array($owner_id,(int)$this->instance_id));  
  }
  
  /*!
  Sets the user that must execute the activity indicated by the activity_id.
  Note that the instance MUST be present in the activity to set the user,
  you can't program who will execute an activity.
  */
  function setActivityUser($activity_id,$theuser) {
    if(empty($theuser)) $theuser = NULL;
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        $this->activities[$i]['user_id']=$theuser;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `user_id`=? where `activity_id`=? and `instance_id`=?";

        $this->mDb->query($query,array($theuser,(int)$activity_id,(int)$this->instance_id));
      }
    }  
  }
  
  /*!
  Returns the user that must execute or is already executing an activity
  wherethis instance is present.
  */  
  function getActivityUser($activity_id) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        return $this->activities[$i]['user'];
      }
    }  
    return false;
  }

  /*!
  Sets the status of the instance in some activity, can be
  'running' or 'completed'
  */  
  function setActivityStatus($activity_id,$status) {
//    for($i=0;$i<count($this->activities);$i++) {
//      if($this->activities[$i]['activity_id']==$activity_id) {
//        $this->activities[$i]['status']=$status;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `status`=? where `activity_id`=? and `instance_id`=?";
        $this->mDb->query($query,array($status,(int)$activity_id,(int)$this->instance_id));
//      }
//    }  
  }
  
  
  /*!
  Gets the status of the instance in some activity, can be
  'running' or 'completed'
  */
  function getActivityStatus($activity_id) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        return $this->activities[$i]['status'];
      }
    }  
    return false;
  }
  
  /*!
  Resets the start time of the activity indicated to the current time.
  */
  function setActivityStarted($activity_id) {
    $now = date("U");
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        $this->activities[$i]['started']=$now;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `started`=? where `activity_id`=? and `instance_id`=?";
        $this->mDb->query($query,array($now,(int)$activity_id,(int)$this->instance_id));
      }
    }  
  }
  
  /*!
  Gets the Unix timstamp of the starting time for the given activity.
  */
  function getActivityStarted($activity_id) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        return $this->activities[$i]['started'];
      }
    }  
    return false;
  }
  
  /*!
  \private
  Gets an activity from the list of activities of the instance
  */
  function _get_instance_activity($activity_id) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activity_id']==$activity_id) {
        return $this->activities[$i];
      }
    }  
    return false;
  }

  /*!
  Sets the time where the instance was started.    
  */
  function setStarted($time) {
    $this->started=$time;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `started`=? where `instance_id`=?";
    $this->mDb->query($query,array((int)$time,(int)$this->instance_id));    
  }
  
  /*!
  Gets the time where the instance was started (Unix timestamp)
  */
  function getStarted() {
    return $this->started;
  }
  
  /*!
  Sets the end time of the instance (when the process was completed)
  */
  function setEnded($time) {
    $this->ended=$time;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `ended`=? where `instance_id`=?";
    $this->mDb->query($query,array((int)$time,(int)$this->instance_id));    
  }
  
  /*!
  Gets the end time of the instance (when the process was completed)
  */
  function getEnded() {
    return $this->ended;
  }
  
  /*!
  Completes an activity, normally from any activity you should call this
  function without arguments.
  The arguments are explained just in case.
  $activity_id is the activity that is being completed, when this is not
  passed the engine takes it from the $_REQUEST array,all activities
  are executed passing the activity_id in the URI.
  $force indicates that the instance must be routed no matter if the
  activity is auto-routing or not. This is used when "sending" an
  instance from a non-auto-routed activity to the next activity.
  $addworkitem indicates if a workitem should be added for the completed
  activity.
  YOU MUST NOT CALL complete() for non-interactive activities since
  the engine does automatically complete automatic activities after
  executing them.
  */
  function complete($activity_id=0,$force=false,$addworkitem=true) {
    global $gBitUser;
    global $__activity_completed;
    
    $__activity_completed = true;
  
    $theuser = $gBitUser->getUserId();
    
    if($activity_id==0) {
      $activity_id=$_REQUEST['activity_id'];
    }  
    
    // If we are completing a start activity then the instance must 
    // be created first!
    $type = $this->mDb->getOne("select `type` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array((int)$activity_id));    
    if($type=='start') {
      $this->_createNewInstance((int)$activity_id,$theuser);
    }
      
    // Now set ended
    $now = date("U");
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `ended`=? where `activity_id`=? and `instance_id`=?";
    $this->mDb->query($query,array((int)$now,(int)$activity_id,(int)$this->instance_id));
    
    //Add a workitem to the instance 
    $iid = $this->instance_id;
    if($addworkitem) {
      $max = $this->mDb->getOne("select max(`order_id`) from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?",array((int)$iid));
      if(!$max) {
        $max=1;
      } else {
        $max++;
      }
      $act = $this->_get_instance_activity($activity_id);
      if(!$act) {
        //Then this is a start activity ending
        $started = $this->getStarted();
        $putuser = $this->getOwner();
      } else {
        $started=$act['started'];
        $putuser = $act['user_id'];
      }
      $ended = date("U");
      $properties = serialize($this->properties);
      $query="insert into `".GALAXIA_TABLE_PREFIX."workitems`(`instance_id`,`order_id`,`activity_id`,`started`,`ended`,`properties`,`user_id`) values(?,?,?,?,?,?,?)";    
      $this->mDb->query($query,array((int)$iid,(int)$max,(int)$activity_id,(int)$started,(int)$ended,$properties,$putuser));
    }
    
    //Set the status for the instance-activity to completed
    $this->setActivityStatus($activity_id,'completed');
    
    //If this and end actt then terminate the instance
    if($type=='end') {
      $this->terminate();
      return;
    }
    
    //If the activity ending is autorouted then send to the
    //activity
    if ($type != 'end') {
      if (($force) || ($this->mDb->getOne("select `is_auto_routed` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array($activity_id)) == 'y'))   {
        // Now determine where to send the instance
        $query = "select `act_to_id` from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=?";
        $result = $this->mDb->query($query,array((int)$activity_id));
        $candidates = Array();
        while ($res = $result->fetchRow()) {
          $candidates[] = $res['act_to_id'];
        }
        if($type == 'split') {
          $first = true;
          foreach ($candidates as $cand) {
            $this->sendTo($activity_id,$cand,$first);
            $first = false;
          }
        } elseif($type == 'switch') {
          if (in_array($this->next_activity,$candidates)) {
            $this->sendTo((int)$activity_id,(int)$this->next_activity);
          } else {
            trigger_error(tra('Fatal error: next_activity does not match any candidate in autorouting switch activity'),E_USER_WARNING);
          }
        } else {
          if (count($candidates)>1) {
            trigger_error(tra('Fatal error: non-deterministic decision for autorouting activity'),E_USER_WARNING);
          } else {
            $this->sendTo((int)$activity_id,(int)$candidates[0]);
          }
        }
      }
    }
  }
  
  /*!
  Aborts an activity and terminates the whole instance. We still create a workitem to keep track
  of where in the process the instance was aborted
  */
  function abort($activity_id=0,$theuser = NULL,$addworkitem=true) {
    if(empty($theuser)) {
      global $gBitUser;
      if(empty($gBitUser->getUserId)) {
          $theuser = NULL;
      } else {
          $theuser = $gBitUser->getUserId();
      }
    }
    
    if($activity_id==0) {
      $activity_id=$_REQUEST['activity_id'];
    }  
    
    // If we are completing a start activity then the instance must 
    // be created first!
    $type = $this->mDb->getOne("select `type` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array((int)$activity_id));    
    if($type=='start') {
      $this->_createNewInstance((int)$activity_id,$theuser);
    }
      
    // Now set ended
    $now = date("U");
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `ended`=? where `activity_id`=? and `instance_id`=?";
    $this->mDb->query($query,array((int)$now,(int)$activity_id,(int)$this->instance_id));
    
    //Add a workitem to the instance 
    $iid = $this->instance_id;
    if($addworkitem) {
      $max = $this->mDb->getOne("select max(`order_id`) from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?",array((int)$iid));
      if(!$max) {
        $max=1;
      } else {
        $max++;
      }
      $act = $this->_get_instance_activity($activity_id);
      if(!$act) {
        //Then this is a start activity ending
        $started = $this->getStarted();
        $putuser = $this->getOwner();
      } else {
        $started=$act['started'];
        $putuser = $act['user_id'];
      }
      $ended = date("U");
      $properties = serialize($this->properties);
      $query="insert into `".GALAXIA_TABLE_PREFIX."workitems`(`instance_id`,`order_id`,`activity_id`,`started`,`ended`,`properties`,`user_id`) values(?,?,?,?,?,?,?)";    
      $this->mDb->query($query,array((int)$iid,(int)$max,(int)$activity_id,(int)$started,(int)$ended,$properties,$putuser));
    }
    
    //Set the status for the instance-activity to aborted
// TODO: support 'aborted' if we keep activities after termination some day
    //$this->setActivityStatus($activity_id,'aborted');

    // terminate the instance with status 'aborted'
    $this->terminate('aborted');
  }
  
  /*!
  Terminates the instance marking the instance and the process
  as completed. This is the end of a process.
  Normally you should not call this method since it is automatically
  called when an end activity is completed.
  */
  function terminate($status = 'completed') {
    //Set the status of the instance to completed
    $now = date("U");
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=?, `ended`=? where `instance_id`=?";
    $this->mDb->query($query,array($status,(int)$now,(int)$this->instance_id));
    //$query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=?";
    //$this->mDb->query($query,array((int)$this->instance_id));
    $this->status = $status;
    $this->activities = Array();
  }
  
  
  /*!
  Sends the instance from some activity to another activity.
  You should not call this method unless you know very very well what
  you are doing.
  */
  function sendTo($from,$activity_id,$split=false) {
    //1: if we are in a join check
    //if this instance is also in
    //other activity if so do
    //nothing
    $type = $this->mDb->getOne("select `type` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array((int)$activity_id));
    
    // Verify the existance of a transition
    if(!$this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=? and `act_to_id`=?",array($from,(int)$activity_id))) {
      trigger_error(tra('Fatal error: trying to send an instance to an activity but no transition found'),E_USER_WARNING);
    }
    
    //try to determine the group or *
    //Use the next_group_id
    if($this->next_group_id) {
      $putgroup = $this->next_group_id;
    } else {
      $candidates = Array();
      $query = "select `role_id` from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=?";
      $result = $this->mDb->query($query,array((int)$activity_id)); 
      while ($res = $result->fetchRow()) {
        $role_id = $res['role_id'];
        $query2 = "select ugm.`user_id` from `".GALAXIA_TABLE_PREFIX."group_roles` ggr
				INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
				where `role_id`=?";
        $result2 = $this->mDb->query($query2,array((int)$role_id)); 
        while ($res2 = $result2->fetchRow()) {
          $candidates[] = $res2['user_id'];
        }

      }
      if(count($candidates) == 1) {
        $putgroup = $candidates[0];
      } else {
        $putgroup = NULL;
      }
    }        
    //update the instance_activities table
    //if not splitting delete first
    //please update started,status,user
    if(!$split) {
//      $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=? and `activity_id`=?";
//      $this->mDb->query($query,array((int)$this->instance_id,$from));
    }
    $now = date("U");
    $iid = $this->instance_id;
    $query="delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=? and `activity_id`=?";
    $this->mDb->query($query,array((int)$iid,(int)$activity_id));
    $query="insert into `".GALAXIA_TABLE_PREFIX."instance_activities`(`instance_id`,`activity_id`,`user_id`,`status`,`started`,`ended`) values(?,?,?,?,?,?)";
    $this->mDb->query($query,array((int)$iid,(int)$activity_id,$putgroup,'running',(int)$now,0));
    
    //we are now in a new activity
    $this->activities=Array();
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=?";
    $result = $this->mDb->query($query,array((int)$iid));
    while ($res = $result->fetchRow()) {
      $this->activities[]=$res;
    }    
  
    if ($type == 'join') {
      if (count($this->activities)>1) {
        // This instance will have to wait!
        return;
      }
    }    

     
    //if the activity is not interactive then
    //execute the code for the activity and
    //complete the activity
    $is_interactive = $this->mDb->getOne("select `is_interactive` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?",array((int)$activity_id));
    if ($is_interactive=='n') {

      // Now execute the code for the activity (function defined in lib/Galaxia/config.php)
      galaxia_execute_activity($activity_id, $iid , 1);

      // Reload in case the activity did some change
      $this->getInstance($this->instance_id);
      $this->complete($activity_id);
    }
  }
  
  /*! 
  Gets a comment for this instance 
  */
  function get_instance_comment($c_id) {
    $iid = $this->instance_id;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instance_id`=? and `c_id`=?";
    $result = $this->mDb->query($query,array((int)$iid,(int)$c_id));
    $res = $result->fetchRow();
    return $res;
  }
  
  /*! 
  Inserts or updates an instance comment 
  */
  function replace_instance_comment($c_id, $activity_id, $activity, $user_id, $title, $comment) {
    if (!$user_id) {
      $user_id = $gBitUser->getUserId();
    }
    $iid = $this->instance_id;
    if ($c_id) {
      $query = "update `".GALAXIA_TABLE_PREFIX."instance_comments` set `title`=?,`comment`=? where `instance_id`=? and `c_id`=?";
      $this->mDb->query($query,array($title,$comment,(int)$iid,(int)$c_id));
    } else {
      $hash = md5($title.$comment);
      if ($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instance_id`=? and `hash`=?",array($iid,$hash))) {
        return false;
      }
      $now = date("U");
      $query ="insert into `".GALAXIA_TABLE_PREFIX."instance_comments`(`instance_id`,`user_id`,`activity_id`,`activity`,`title`,`comment`,`timestamp`,`hash`) values(?,?,?,?,?,?,?,?)";
      $this->mDb->query($query,array((int)$iid,$user_id,(int)$activity_id,$activity,$title,$comment,(int)$now,$hash));
    }  
  }
  
  /*!
  Removes an instance comment
  */
  function remove_instance_comment($c_id) {
    $iid = $this->instance_id;
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_comments` where `c_id`=? and `instance_id`=?";
    $this->mDb->query($query,array((int)$c_id,(int)$iid));
  }
 
  /*!
  Lists instance comments
  */
  function get_instance_comments($aid) {
    $iid = $this->instance_id;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instance_id`=? and `activity_id`=? order by ".$this->mDb->convert_sortmode("timestamp_asc");
    $result = $this->mDb->query($query,array((int)$iid,(int)$aid));    
    $ret = Array();
    while($res = $result->fetchRow()) {    
      $ret[] = $res;
    }
    return $ret;
  }

}
?>
