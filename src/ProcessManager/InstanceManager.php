<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! InstanceManager
//! A class to maniplate instances
/*!
  This class is used to add,remove,modify and list
  instances.
*/
class InstanceManager extends BaseManager {
  
  function InstanceManager() 
  {
	BaseManager::BaseManager();
  }
  
  function get_instance_activities($iid)
  {
    $query = "select ga.`type`, ga.`is_interactive`, ga.`is_auto_routed`, gi.`p_id`, ga.`activity_id`, ga.`name`, gi.`instance_id`, gi.`status`, gia.`activity_id`, gia.`user_id`, gi.`started`, gia.`status` as `actstatus` from `".GALAXIA_TABLE_PREFIX."activities` ga, `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."instance_activities` gia where ga.`activity_id`=gia.`activity_id` and gi.`instance_id`=gia.`instance_id` and gi.`instance_id`=?";
    $result = $this->query($query,array($iid));
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Number of active instances
      $ret[] = $res;
    }
    return $ret;
  }

  function get_instance($iid)
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instances` gi where `instance_id`=$iid";
    $result = $this->query($query);
    $res = $result->fetchRow();
    $res['workitems']=$this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=$iid");
    return $res;
  }

  function get_instance_properties($iid)
  {
    $prop = unserialize($this->getOne("select `properties` from `".GALAXIA_TABLE_PREFIX."instances` gi where `instance_id`=$iid"));
    return $prop;
  }
  
  function set_instance_properties($iid,&$prop)
  {
    $props = addslashes(serialize($prop));
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`='$props' where `instance_id`=$iid";
    $this->query($query);
  }
  
  function set_instance_owner($iid,$owner)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `owner`='$owner' where `instance_id`=$iid";
    $this->query($query);
  }
  
  function set_instance_status($iid,$status)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`='$status' where `instance_id`=$iid";
    $this->query($query); 
  }
  
  function set_instance_destination($iid,$activity_id)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=$iid";
    $this->query($query);
    $now = date("U");
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instance_activities` (`instance_id`, `activity_id`, `started`, `ended`, `user_id`, `status`)
    values(?,?,?,?,?,?)";
    $this->query($query, array($iid,$activity_id,(int)$now,0,NULL,'running'));
  }
  
  function set_instance_user($iid,$activity_id,$user_id)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `user_id`='$user_id', `status`='running' where `instance_id`=$iid and `activity_id`=$activity_id";
    $this->query($query);  
  }

}    

?>
