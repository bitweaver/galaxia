<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! InstanceManager
//! A class to maniplate instances
/*!
  This class is used to add,remove,modify and list
  instances.
*/
class InstanceManager extends BaseManager {
  
  /*!
    Constructor takes a PEAR::Db object to be used
    to manipulate roles in the database.
  */
  function InstanceManager() 
  {
	BaseManager::BaseManager();
  }
  
  function get_instance_activities($iid,$aid = "")
  {
  	if ($aid == "") {
  		$and = "";
  	}
  	else {
  		$and = "and ga.`activity_id` = $aid";
  	}
    $query = "select ga.`activity_id`, ga.`type`, ga.`is_interactive`, ga.`is_auto_routed`, gi.`p_id`, ga.`name`,
		gi.`instance_id`, gi.`status`, ga.`expiration_time` as exptime, gia.`user_id`, gi.`started`, gia.`started` as ia_started, gia.`status` as `actstatus`, gia.`ended`
		FROM `".GALAXIA_TABLE_PREFIX."activities` ga
		INNER JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON ga.`activity_id`=gia.`activity_id`
		INNER JOIN `".GALAXIA_TABLE_PREFIX."instances` gi ON gi.`instance_id`=gia.`instance_id`
		WHERE gi.`instance_id`=? $and ORDER BY gia.`started`";
    $result = $this->mDb->query($query,array($iid));
    $ret = Array();
    if ($and == "") {
    	while($res = $result->fetchRow()) {
    		// Number of active instances
    		$res['exptime'] = $this->make_ending_date($res['ia_started'],$res['exptime']);
    		$ret[] = $res;
    	}
    	return $ret;
    }
    else {
    	$res = $result->fetchRow();
    	$res['exptime'] = $this->make_ending_date($res['ia_started'],$res['exptime']);
    	return $res;
    }
  }

  function get_instance($iid)
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instances` gi where `instance_id`=$iid";
    $result = $this->mDb->query($query);
    $res = $result->fetchRow();
    $res['workitems']=$this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=$iid");
    return $res;
  }

  function get_instance_properties($iid)
  {
    $prop = unserialize($this->mDb->getOne("select `properties` from `".GALAXIA_TABLE_PREFIX."instances` gi where `instance_id`=$iid"));
    return $prop;
  }
  
  function set_instance_properties($iid,&$prop)
  {
    $props = addslashes(serialize($prop));
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`='$props' where `instance_id`=$iid";
    $this->mDb->query($query);
  }
  
  function set_instance_name($iid,$name)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set name=? where instance_id=?";
    $this->mDb->query($query, array($name,$iid) );
  }
  
  function set_instance_owner($iid,$owner)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `owner_id`='$owner' where `instance_id`=$iid";
    $this->mDb->query($query);
  }
  
  function set_instance_status($iid,$status)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`='$status' where `instance_id`=$iid";
    $this->mDb->query($query); 
  }
  
  function set_instance_destination($iid,$activity_id)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=$iid";
    $this->mDb->query($query);
    $now = date("U");
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instance_activities` (`instance_id`, `activity_id`, `started`, `ended`, `user_id`, `status`)
    values(?,?,?,?,?,?)";
    $this->mDb->query($query, array($iid,$activity_id,(int)$now,0,NULL,'running'));
  }
  
  function set_instance_user($iid,$activity_id,$user_id)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `user_id`=?, `status`=? where `instance_id`=? and `activity_id`=?";
    $this->mDb->query($query, array($user_id,'running',(int)$iid,(int)$activity_id));
  }

}    

?>
