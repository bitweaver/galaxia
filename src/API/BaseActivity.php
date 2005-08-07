<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Abstract class representing activities
//! An abstract class representing activities
/*!
This class represents activities, and must be derived for
each activity type supported in the system. Derived activities extending this
class can be found in the activities subfolder.
This class is observable.
*/
class BaseActivity extends Base {
  var $name;
  var $normalizedName;
  var $description;
  var $is_interactive;
  var $is_auto_routed;
  var $roles=Array();
  var $outbound=Array();
  var $inbound=Array();
  var $p_id;
  var $activity_id;
  var $type;
  
  function BaseActivity()
  {
    Base::Base();
    $this->type='base';
  }
  
  
  /*!
  Factory method returning an activity of the desired type
  loading the information from the database.
  */
  function getActivity($activity_id) 
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=?";
    $result = $this->mDb->query($query,array($activity_id));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    switch($res['type']) {
      case 'start':
        $act = new Start();  
        break;
      case 'end':
        $act = new End();
        break;
      case 'join':
        $act = new Join();
        break;
      case 'split':
        $act = new Split();
        break;
      case 'standalone':
        $act = new Standalone();
        break;
      case 'switch':
        $act = new SwitchActivity();
        break;
      case 'activity':
        $act = new Activity();
        break;
      default:
        trigger_error('Unknown activity type:'.$res['type'],E_USER_WARNING);
    }
    
    $act->setName($res['name']);
    $act->setProcessId($res['p_id']);
    $act->setNormalizedName($res['normalized_name']);
    $act->setDescription($res['description']);
    $act->setIsInteractive($res['is_interactive']);
    $act->setIsAutoRouted($res['is_auto_routed']);
    $act->setActivityId($res['activity_id']);
    $act->setType($res['type']);
    
    //Now get forward transitions 
    
    //Now get backward transitions
    
    //Now get roles
    $query = "select `role_id` from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=?";
    $result=$this->mDb->query($query,array($res['activity_id']));
    while($res = $result->fetchRow()) {
      $this->roles[] = $res['role_id'];
    }
    $act->setRoles($this->roles);
    return $act;
  }
  
  /*! Returns an Array of role_ids for the given user */
  function getUserRoles($user_id) {
//vd($user);
    if (isset($user_id))
        $query = "SELECT `role_id` FROM `".GALAXIA_TABLE_PREFIX."group_roles` AS gur
	INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=gur.`group_id`
	WHERE ugm.`user_id`=?";
	    $result=$this->mDb->query($query,array($user_id));

    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['role_id'];
    }
    return $ret;
  }

  /*! Returns an Array of asociative arrays with role_id and name
  for the given user */  
  function getActivityRoleNames() {
    $aid = $this->activity_id;
    $query = "select gr.`role_id`, `name` from `".GALAXIA_TABLE_PREFIX."activity_roles` gar, `".GALAXIA_TABLE_PREFIX."roles` gr where gar.`role_id`=gr.`role_id` and gar.`activity_id`=?";
    $result=$this->mDb->query($query,array($aid));
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  /*! Returns the normalized name for the activity */
  function getNormalizedName() {
    return $this->normalizedName;
  }

  /*! Sets normalized name for the activity */  
  function setNormalizedName($name) {
    $this->normalizedName=$name;
  }
  
  /*! Sets the name for the activity */
  function setName($name) {
    $this->name=$name;
  }
  
  /*! Gets the activity name */
  function getName() {
    return $this->name;
  }
  
  /*! Sets the activity description */
  function setDescription($desc) {
    $this->description=$desc;
  }
  
  /*! Gets the activity description */
  function getDescription() {
    return $this->description;
  }
  
  /*! Sets the type for the activity - this does NOT allow you to change the actual type */
  function setType($type) {
    $this->type=$type;
  }
  
  /*! Gets the activity type */
  function getType() {
    return $this->type;
  }

  /*! Sets if the activity is interactive */
  function setIsInteractive($is) {
    $this->is_interactive=$is;
  }
  
  /*! Returns if the activity is interactive */
  function is_interactive() {
    return $this->is_interactive == 'y';
  }
  
  /*! Sets if the activity is auto-routed */
  function setIsAutoRouted($is) {
    $this->is_auto_routed = $is;
  }
  
  /*! Gets if the activity is auto routed */
  function is_auto_routed() {
    return $this->is_auto_routed == 'y';
  }

  /*! Sets the processId for this activity */
  function setProcessId($pid) {
    $this->p_id=$pid;
  }
  
  /*! Gets the processId for this activity*/
  function getProcessId() {
    return $this->p_id;
  }

  /*! Gets the activity_id */
  function getActivityId() {
    return $this->activity_id;
  }  
  
  /*! Sets the activity_id */
  function setActivityId($id) {
    $this->activity_id=$id;
  }
  
  /*! Gets array with role_ids asociated to this activity */
  function getRoles() {
    return $this->roles;
  }
  
  /*! Sets roles for this activities, shoule receive an
  array of role_ids */
  function setRoles($roles) {
    $this->roles = $roles;
  }
  
  /*! Checks if a user has a certain role (by name) for this activity,
      e.g. $isadmin = $activity->checkUserRole($user,'admin'); */
  function checkUserRole($user,$rolename) {
    $aid = $this->activity_id;
    return $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activity_roles` gar, `".GALAXIA_TABLE_PREFIX."user_roles` gur, `".GALAXIA_TABLE_PREFIX."roles` gr where gar.`role_id`=gr.`role_id` and gur.`role_id`=gr.`role_id` and gar.`activity_id`=? and gur.`user`=? and gr.`name`=?",array($aid, $user, $rolename));
  }

}
?>
