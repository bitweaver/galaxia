<?php
include_once(GALAXIA_LIBRARY.'/src/common/Base.php');
//!! ProcessMonitor
//! ProcessMonitor class
/*!
This class provides methods for use in typical monitoring scripts
*/
class ProcessMonitor extends Base {

  function monitor_stats() {
    $res = Array();
    $res['active_processes'] = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes` where `is_active`=?",array('y'));
    $res['processes'] = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes`");
    $result = $this->mDb->query("select distinct(`p_id`) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array('active'));
    $res['running_processes'] = $result->numRows();
    // get the number of instances per status
    $query = "select `status`, count(*) as `num_instances` from `".GALAXIA_TABLE_PREFIX."instances` group by `status`";
    $result = $this->mDb->query($query);
    $status = array();
    while($info = $result->fetchRow()) {
      $status[$info['status']] = $info['num_instances'];
    }
    $res['active_instances'] = isset($status['active']) ? $status['active'] : 0;
    $res['completed_instances'] = isset($status['completed']) ? $status['completed'] : 0;
    $res['exception_instances'] = isset($status['exception']) ? $status['exception'] : 0;
    $res['aborted_instances'] = isset($status['aborted']) ? $status['aborted'] : 0;
    return $res;
  }

  function update_instance_status($iid,$status) {
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=? where `instance_id`=?";
    $this->mDb->query($query,array($status,$iid));
  }

  function update_instance_activity_status($iid,$activity_id,$status) {
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `status`=? where `instance_id`=? and `activity_id`=?";
    $this->mDb->query($query,array($status,$iid,$activity_id));
  }

  function remove_instance($iid) {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?";
    $this->mDb->query($query,array($iid));
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=?";
    $this->mDb->query($query,array($iid));
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `instance_id`=?";
    $this->mDb->query($query,array($iid));  
  }

  function remove_aborted() {
    $query="select `instance_id` from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?";
    $result = $this->mDb->query($query,array('aborted'));
    while($res = $result->fetchRow()) {  
      $iid = $res['instance_id'];
      $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=?";
      $this->mDb->query($query,array($iid));
      $query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?";
      $this->mDb->query($query,array($iid));  
    }
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?";
    $this->mDb->query($query,array('aborted'));
  }

  function remove_all($p_id) {
    $query="select `instance_id` from `".GALAXIA_TABLE_PREFIX."instances` where `p_id`=?";
    $result = $this->mDb->query($query,array($p_id));
    while($res = $result->fetchRow()) {  
      $iid = $res['instance_id'];
      $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instance_id`=?";
      $this->mDb->query($query,array($iid));
      $query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?";
      $this->mDb->query($query,array($iid));  
    }
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `p_id`=?";
    $this->mDb->query($query,array($p_id));
  }


  function monitor_list_processes($offset,$maxRecords,$sort_mode,$find,$where='') {
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid=" where ((`name` like ?) or (`description` like ?))";
      $bindvars = array($findesc,$findesc);
    } else {
      $mid="";
      $bindvars = array();
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    // get the requested processes
    $query = "select * from `".GALAXIA_TABLE_PREFIX."processes` $mid order by $sort_mode";
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."processes` $mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $p_id = $res['p_id'];
      // Number of active instances
      $res['active_instances'] = 0;
      // Number of exception instances
      $res['exception_instances'] = 0;
      // Number of completed instances
      $res['completed_instances'] = 0;
      // Number of aborted instances
      $res['aborted_instances'] = 0;
      $res['all_instances'] = 0;
      // Number of activities
      $res['activities'] = 0;
      $ret[$p_id] = $res;
    }
    if (count($ret) < 1) {
      $retval = Array();
      $retval["data"] = $ret;
      $retval["cant"] = $cant;
      return $retval;
    }
    // get number of instances and timing statistics per process and status
    $query = "select `p_id`, `status`, count(*) as `num_instances`,
              min(`ended` - `started`) as `min_time`, avg(`ended` - `started`) as `avg_time`, max(`ended` - `started`) as `max_time`
              from `".GALAXIA_TABLE_PREFIX."instances` where `p_id` in (" . join(', ', array_keys($ret)) . ") group by `p_id`, `status`";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {
      $p_id = $res['p_id'];
      if (!isset($ret[$p_id])) continue;
      switch ($res['status']) {
        case 'active':
          $ret[$p_id]['active_instances'] = $res['num_instances'];
          $ret[$p_id]['all_instances'] += $res['num_instances'];
          break;
        case 'completed':
          $ret[$p_id]['completed_instances'] = $res['num_instances'];
          $ret[$p_id]['all_instances'] += $res['num_instances'];
          $ret[$p_id]['duration'] = array('min' => $res['min_time'], 'avg' => $res['avg_time'], 'max' => $res['max_time']);
          break;
        case 'exception':
          $ret[$p_id]['exception_instances'] = $res['num_instances'];
          $ret[$p_id]['all_instances'] += $res['num_instances'];
          break;
        case 'aborted':
          $ret[$p_id]['aborted_instances'] = $res['num_instances'];
          $ret[$p_id]['all_instances'] += $res['num_instances'];
          break;
      }
    }
    // get number of activities per process
    $query = "select `p_id`, count(*) as `num_activities`
              from `".GALAXIA_TABLE_PREFIX."activities`
              where `p_id` in (" . join(', ', array_keys($ret)) . ")
              group by `p_id`";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {
      $p_id = $res['p_id'];
      if (!isset($ret[$p_id])) continue;
      $ret[$p_id]['activities'] = $res['num_activities'];
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_activities($offset,$maxRecords,$sort_mode,$find,$where='') {
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid=" where ((ga.`name` like ?) or (ga.`description` like ?))";
      $bindvars = array($findesc,$findesc);
    } else {
      $mid="";
      $bindvars = array();
    }
    if($where) {
      $where = preg_replace('/`p_id`/', 'ga.`p_id`', $where);
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    $query = "select gp.`procname`, gp.`version`, ga.*
              from `".GALAXIA_TABLE_PREFIX."activities` ga
                left join `".GALAXIA_TABLE_PREFIX."processes` gp on gp.`p_id`=ga.`p_id`
              $mid order by $sort_mode";
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."activities` ga $mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Number of active instances
      $aid = $res['activity_id'];
      $res['active_instances']=$this->mDb->getOne("select count(gi.`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."instance_activities` gia where gi.`instance_id`=gia.`instance_id` and gia.`activity_id`=$aid and gi.`status`='active' and `p_id`=".$res['p_id']);
    // activities of completed instances are all removed from the instance_activities table for some reason, so we need to look at workitems
      $res['completed_instances']=$this->mDb->getOne("select count(distinct gi.`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."workitems` gw where gi.`instance_id`=gw.`instance_id` and gw.`activity_id`=$aid and gi.`status`='completed' and `p_id`=".$res['p_id']);
    // activities of aborted instances are all removed from the instance_activities table for some reason, so we need to look at workitems
      $res['aborted_instances']=$this->mDb->getOne("select count(distinct gi.`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."workitems` gw where gi.`instance_id`=gw.`instance_id` and gw.`activity_id`=$aid and gi.`status`='aborted' and `p_id`=".$res['p_id']);
      $res['exception_instances']=$this->mDb->getOne("select count(gi.`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."instance_activities` gia where gi.`instance_id`=gia.`instance_id` and gia.`activity_id`=$aid and gi.`status`='exception' and `p_id`=".$res['p_id']);
    $res['act_running_instances']=$this->mDb->getOne("select count(gi.`instance_id`) from `".GALAXIA_TABLE_PREFIX."instances` gi, `".GALAXIA_TABLE_PREFIX."instance_activities` gia where gi.`instance_id`=gia.`instance_id` and gia.`activity_id`=$aid and gia.`status`='running' and `p_id`=".$res['p_id']);      
    // completed activities are removed from the instance_activities table unless they're part of a split for some reason, so this won't work
    //  $res['act_completed_instances']=$this->mDb->getOne("select count(gi.instance_id) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instance_id=gia.instance_id and gia.activity_id=$aid and gia.status='completed' and p_id=".$res['p_id']);      
      $res['act_completed_instances'] = 0;
      $ret[$aid] = $res;
    }
    if (count($ret) < 1) {
      $retval = Array();
      $retval["data"] = $ret;
      $retval["cant"] = $cant;
      return $retval;
    }
    $query = "select `activity_id`, count(distinct `instance_id`) as `num_instances`, min(`ended` - `started`) as `min_time`, avg(`ended` - `started`) as `avg_time`, max(`ended` - `started`) as `max_time`
              from `".GALAXIA_TABLE_PREFIX."workitems`
              where `activity_id` in (" . join(', ', array_keys($ret)) . ")
              group by `activity_id`";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {
      // Number of active instances
      $aid = $res['activity_id'];
      if (!isset($ret[$aid])) continue;
      $ret[$aid]['act_completed_instances'] = $res['num_instances'] - $ret[$aid]['aborted_instances'];
      $ret[$aid]['duration'] = array('min' => $res['min_time'], 'avg' => $res['avg_time'], 'max' => $res['max_time']);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_instances($offset,$maxRecords,$sort_mode,$find,$where='',$wherevars=array()) {
    if($find) {
      $findesc = $this->qstr('%'.$find.'%');
      $mid=" where (`properties` like $findesc)";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    $query = "select gp.`p_id`, ga.`is_interactive`, gi.`owner_id`, gp.`procname`, gp.`version`, ga.`type`,
        ga.`activity_id`, ga.`name`, gi.`instance_id`, gi.`status`, gia.`activity_id`, gia.`user_id`, gi.`started`, gi.`name` as ins_name, gi.`ended`, gia.`status` as `actstatus`
        from `".GALAXIA_TABLE_PREFIX."instances` gi LEFT JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
        LEFT JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activity_id` = ga.`activity_id`
        LEFT JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`p_id`=gi.`p_id` $mid order by gi.".$this->mDb->convert_sortmode($sort_mode);   

    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."instances` gi
        LEFT JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
        LEFT JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activity_id` = ga.`activity_id`
        LEFT JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`p_id`=gi.`p_id` $mid";
    $result = $this->mDb->query($query,$wherevars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$wherevars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $iid = $res['instance_id'];
      $res['workitems']=$this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` where `instance_id`=?",array($iid));
      $ret[$iid] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function monitor_list_all_processes($sort_mode = 'name_asc', $where = '') {
    if (!empty($where)) {
      $where = " where ($where) ";
    }
    $query = "select `name`,`version`,`p_id` from `".GALAXIA_TABLE_PREFIX."processes` $where order by ".$this->mDb->convert_sortmode($sort_mode);
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $p_id = $res['p_id'];
      $ret[$p_id] = $res;
    }
    return $ret;
  }

  function monitor_list_all_activities($sort_mode = 'name_asc', $where = '') {
    if (!empty($where)) {
      $where = " where ($where) ";
    }
    $query = "select `name`,`activity_id` from `".GALAXIA_TABLE_PREFIX."activities` $where order by ".$this->mDb->convert_sortmode($sort_mode);
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $aid = $res['activity_id'];
      $ret[$aid] = $res;
    }
    return $ret;
  }

  function monitor_list_statuses() {
    $query = "select distinct(`status`) from `".GALAXIA_TABLE_PREFIX."instances`";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['status'];
    }
    return $ret;
  }

  function monitor_list_users() {
    $query = "select distinct(`user_id`) from `".GALAXIA_TABLE_PREFIX."instance_activities`";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['user_id'];
    }
    return $ret;
  }

  function monitor_list_wi_users() {
    $query = "select distinct(`user_id`) from `".GALAXIA_TABLE_PREFIX."workitems`";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['user_id'];
    }
    return $ret;
  }


  function monitor_list_owners() {
    $query = "select distinct(`owner_id`) from `".GALAXIA_TABLE_PREFIX."instances`";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['owner_id'];
    }
    return $ret;
  }
  
  function monitor_list_instances_names() {
  	$query = "select distinct(`name`) from `".GALAXIA_TABLE_PREFIX."instances`";
  	$result = $this->mDb->query($query);
  	$ret = array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['name'];
    }
    return $ret;
  }

  function monitor_list_activity_types() {
    $query = "select distinct(`type`) from `".GALAXIA_TABLE_PREFIX."activities`";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res['type'];
    }
    return $ret;
  }

  function monitor_get_workitem($item_id) {
    $query = "select gw.`order_id`,ga.`name`,ga.`type`,ga.`is_interactive`,gp.`procname`,gp.`version`,
          gw.`item_id`,gw.`properties`,gw.`user_id`,`started`,`ended`-`started` as `duration`
          from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,
          `".GALAXIA_TABLE_PREFIX."processes` gp where ga.`activity_id`=gw.`activity_id` and ga.`p_id`=gp.`p_id` and `item_id`=?";
    $result = $this->mDb->query($query, array($item_id));
    $res = $result->fetchRow();
    $res['properties'] = unserialize($res['properties']);
    return $res;
  }

  // List workitems per instance, remove workitem, update_workitem
  function monitor_list_workitems($offset,$maxRecords,$sort_mode,$find,$where='',$wherevars=array()) {
    $mid = '';
    if ($where) {
      $mid.= " and ($where) ";
    }
    if($find) {
      $findesc = $this->qstr('%'.$find.'%');
      $mid.=" and (`properties` like $findesc)";
    }
// TODO: retrieve instance status as well
    $query = "select `item_id`,`ended`-`started` as `duration`, ga.`is_interactive`, ga.`type`,gp.`procname`,
        gp.`version`,ga.`name` as `actname`, ga.`activity_id`,`instance_id`,`order_id`,`properties`,`started`,`ended`,`user_id`
        from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,`".GALAXIA_TABLE_PREFIX."processes` gp 
        where gw.`activity_id`=ga.`activity_id` and ga.`p_id`=gp.`p_id` $mid order by gp.`p_id` desc,".$this->mDb->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,
        `".GALAXIA_TABLE_PREFIX."processes` gp where gw.`activity_id`=ga.`activity_id` and ga.`p_id`=gp.`p_id` $mid";
    $result = $this->mDb->query($query,$wherevars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$wherevars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $item_id = $res['item_id'];
      $ret[$item_id] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
}
?>
