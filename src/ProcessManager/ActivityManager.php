<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! ActivityManager
//! A class to maniplate process activities and transitions
/*!
  This class is used to add,remove,modify and list
  activities used in the Workflow engine.
  Activities are managed in a per-process level, each
  activity belongs to some process.
*/
class ActivityManager extends BaseManager {
  var $error='';
      
  /*!
    Constructor takes a PEAR::Db object to be used
    to manipulate activities in the database.
  */
  function ActivityManager()
  {
	BaseManager::BaseManager();
  }
  
  function get_error() {
    return $this->error;
  }
  
  /*!
   Asociates an activity with a role
  */
  function add_activity_role($activity_id, $role_id) {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=? and `role_id`=?";
    $this->mDb->query($query,array($activity_id, $role_id));
    $query = "insert into `".GALAXIA_TABLE_PREFIX."activity_roles`(`activity_id`,`role_id`) values(?,?)";
    $this->mDb->query($query,array($activity_id, $role_id));
  }
  
  /*!
   Gets the roles asociated to an activity
  */
  function get_activity_roles($activity_id) {
    $query = "select `activity_id`, roles.`role_id`, roles.`name`
              from `".GALAXIA_TABLE_PREFIX."activity_roles` gar, `".GALAXIA_TABLE_PREFIX."roles` roles
              where roles.`role_id`=gar.`role_id` and `activity_id`=?";
    $result = $this->mDb->query($query,array($activity_id));
    $ret = Array();
    while($res = $result->fetchRow()) {  
      $ret[] = $res;
    }
    return $ret;
  }
  
  /*!
   Removes a role from an activity
  */
  function remove_activity_role($activity_id, $role_id)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."activity_roles`
              where `activity_id`=$activity_id and `role_id`=$role_id";
    $this->mDb->query($query);
  }
  
  /*!
   Checks if a transition exists
  */
  function transition_exists($pid,$act_from_id,$act_to_id)
  {
    return($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `p_id`=$pid and `act_from_id`=$act_from_id and `act_to_id`=$act_to_id"));
  }
  
  /*!
   Adds a transition 
  */
  function add_transition($p_id, $act_from_id, $act_to_id)
  {
    // No circular transitions allowed
    if($act_from_id == $act_to_id) return false;
    
    // Rule: if act is not spl-x or spl-a it can't have more than
    // 1 outbound transition.
    $a1 = $this->get_activity($p_id, $act_from_id);
    $a2 = $this->get_activity($p_id, $act_to_id);
    if(!$a1 || !$a2) return false;
    if($a1['type'] != 'switch' && $a1['type'] != 'split') {
      if($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=$act_from_id")) {
        $this->error = tra('Cannot add transition only split activities can have more than one outbound transition');
        return false;
      }
    }
    
    // Rule: if act is standalone no transitions allowed
    if($a1['type'] == 'standalone' || $a2['type']=='standalone') return false;
    // No inbound to start
    if($a2['type'] == 'start') return false;
    // No outbound from end
    if($a1['type'] == 'end') return false;
     
    
    $query = "delete from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=? and `act_to_id`=?";
    $this->mDb->query($query,array($act_from_id, $act_to_id));
    $query = "insert into `".GALAXIA_TABLE_PREFIX."transitions`(`p_id`,`act_from_id`,`act_to_id`) values(?,?,?)";
    $this->mDb->query($query,array($p_id, $act_from_id, $act_to_id));

    return true;
  }
  
  /*!
   Removes a transition
  */
  function remove_transition($act_from_id, $act_to_id)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=$act_from_id and `act_to_id`=$act_to_id";
    $this->mDb->query($query);
    return true;
  }
  
  /*!
   Removes all the activity transitions
  */
  function remove_activity_transitions($p_id, $aid)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."transitions` where `p_id`=$p_id and (`act_from_id`=$aid or `act_to_id`=$aid)";
    $this->mDb->query($query);
  }
  
  
  /*!
   Returns all the transitions for a process
  */
  function get_process_transitions($p_id,$actid=0)
  {
    if(!$actid) {
        $query = "select a1.`name` as `actfromname`, a2.`name` as `acttoname`, `act_from_id`, `act_to_id` from `".GALAXIA_TABLE_PREFIX."transitions` gt, `".GALAXIA_TABLE_PREFIX."activities` a1, `".GALAXIA_TABLE_PREFIX."activities` a2 where gt.`act_from_id`=a1.`activity_id` and gt.`act_to_id`=a2.`activity_id` and gt.`p_id`=$p_id";
    } else {
        $query = "select a1.`name` as `actfromname`, a2.`name` as `acttoname`, `act_from_id`, `act_to_id` from `".GALAXIA_TABLE_PREFIX."transitions` gt, `".GALAXIA_TABLE_PREFIX."activities` a1, `".GALAXIA_TABLE_PREFIX."activities` a2 where gt.`act_from_id`=a1.`activity_id` and gt.`act_to_id`=a2.`activity_id` and gt.`p_id`=$p_id and (`act_from_id`=$actid)";
    }
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {  
      $ret[] = $res;
    }
    return $ret;
  }
  
  /*!
   Indicates if an activity is autoRouted
  */
  function activity_is_auto_routed($actid)
  {
    return($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=$actid and `is_auto_routed`='y'"));
  }
  
  /*!
   Returns all the activities for a process as
   an array
  */
  function get_process_activities($p_id)
  {
       $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id";
    $result = $this->mDb->query($query);
    $ret = Array();
    while($res = $result->fetchRow()) {  
      $ret[] = $res;
    }
    return $ret;
  }

  /*!
   Builds the graph 
  */
  //\todo build the real graph
  function build_process_graph($p_id)
  {
    $attributes = Array(
    
    );
    $graph = new Process_GraphViz(true,$attributes);
    $pm = new ProcessManager();
    $name = $pm->_get_normalized_name($p_id);
    $graph->set_pid($name);
    
    // Nodes are process activities so get
    // the activities and add nodes as needed
    $nodes = $this->get_process_activities($p_id);
    
    foreach($nodes as $node)
    {
      if($node['is_interactive']=='y') {
        $color='blue';
      } else {
        $color='black';
      }
      $auto[$node['name']] = $node['is_auto_routed'];
      $graph->addNode($node['name'],array('URL'=>"foourl?activity_id=".$node['activity_id'],
                                      'label'=>$node['name'],
                                      'shape' => $this->_get_activity_shape($node['type']),
                                      'color' => $color

                                      )
                     );    
    }
    
    // Now add edges, edges are transitions,
    // get the transitions and add the edges
    $edges = $this->get_process_transitions($p_id);
    foreach($edges as $edge)
    {
      if($auto[$edge['actfromname']] == 'y') {
        $color = 'red';
      } else {
        $color = 'black';
      }
        $graph->addEdge(array($edge['actfromname'] => $edge['acttoname']), array('color'=>$color));    
    }
    
    
    // Save the map image and the image graph
    $graph->image_and_map();
    unset($graph);
    return true;   
  }
  
  
  /*!
   Validates if a process can be activated checking the
   process activities and transitions the rules are:
   0) No circular activities
   1) Must have only one a start and end activity
   2) End must be reachable from start
   3) Interactive activities must have a role assigned
   4) Roles should be mapped
   5) Standalone activities cannot have transitions
   6) Non intractive activities non-auto routed must have some role
      so the user can "send" the activity
  */
  function validate_process_activities($p_id)
  {
    $errors = Array();
    // Pre rule no cricular activities
    $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `p_id`=$p_id and `act_from_id`=`act_to_id`");
    if($cant) {
      $errors[] = tra('Circular reference found some activity has a transition leading to itself');
    }

    // Rule 1 must have exactly one start and end activity
    $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `type`='start'");
    if($cant < 1) {
      $errors[] = tra('Process does not have a start activity');
    }
    $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `type`='end'");
    if($cant != 1) {
      $errors[] = tra('Process does not have exactly one end activity');
    }
    
    // Rule 2 end must be reachable from start
    $nodes = Array();
    $endId = $this->mDb->getOne("select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `type`='end'");
    $aux['id']=$endId;
    $aux['visited']=false;
    $nodes[] = $aux;
    
    $startId = $this->mDb->getOne("select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `type`='start'");
    $start_node['id']=$startId;
    $start_node['visited']=true;    
    
    while($this->_list_has_unvisited_nodes($nodes) && !$this->_node_in_list($start_node,$nodes)) {
      for($i=0;$i<count($nodes);$i++) {
        $node=&$nodes[$i];
        if(!$node['visited']) {
          $node['visited']=true;          
          $query = "select `act_from_id` from `".GALAXIA_TABLE_PREFIX."transitions` where `act_to_id`=".$node['id'];
          $result = $this->mDb->query($query);
          $ret = Array();
          while($res = $result->fetchRow()) {  
            $aux['id'] = $res['act_from_id'];
            $aux['visited']=false;
            if(!$this->_node_in_list($aux,$nodes)) {
              $nodes[] = $aux;
            }
          }
        }
      }
    }
    
    if(!$this->_node_in_list($start_node,$nodes)) {
      // Start node is NOT reachable from the end node
      $errors[] = tra('End activity is not reachable from start activity');
    }
    
    //Rule 3: interactive activities must have a role
    //assigned.
    //Rule 5: standalone activities can't have transitions
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {  
      $aid = $res['activity_id'];
      if($res['is_interactive'] == 'y') {
          $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=".$res['activity_id']);
          if(!$cant) {
            $errors[] = tra('Activity').': '.$res['name'].tra(' is interactive but has no role assigned');
          }
      } else {
        if( $res['type'] != 'end' && $res['is_auto_routed'] == 'n') {
          $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=".$res['activity_id']);
            if(!$cant) {
              $errors[] = tra('Activity').': '.$res['name'].tra(' is non-interactive and non-autorouted but has no role assigned');
            }
        }
      }
      if($res['type']=='standalone') {
        if($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=$aid or `act_to_id`=$aid")) {
           $errors[] = tra('Activity').': '.$res['name'].tra(' is standalone but has transitions');
        }
      }

    }
    
    
    //Rule4: roles should be mapped
    $query = "select * from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=$p_id";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {      
        $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."group_roles` where `role_id`=".$res['role_id']);
        if(!$cant) {
          $errors[] = tra('Role').': '.$res['name'].tra(' is not mapped');
        }        
    }
    
    
    // End of rules

    // Validate process sources
    $serrors=$this->validate_process_sources($p_id);
    $errors = array_merge($errors,$serrors);
    
    $this->error = $errors;
    
    
    
    $is_valid = (count($errors)==0) ? 'y' : 'n';

    $query = "update `".GALAXIA_TABLE_PREFIX."processes` set `is_valid`='$is_valid' where `p_id`=$p_id";
    $this->mDb->query($query);
    
    $this->_label_nodes($p_id);    
    
    return ($is_valid=='y');
    
    
  }
  
  /*! 
  Validate process sources
  Rules:
  1) Interactive activities (non-standalone) must use complete()
  2) Standalone activities must not use $instance
  3) Switch activities must use setNextActivity
  4) Non-interactive activities cannot use complete()
  */
  function validate_process_sources($pid)
  {
    $errors=Array();
    $procname= $this->mDb->getOne("select `normalized_name` from `".GALAXIA_TABLE_PREFIX."processes` where `p_id`=$pid");
    
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$pid";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {          
      $actname = $res['normalized_name'];
      $source = GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php';
      if (!file_exists($source)) {
          continue;
      }
      $fp = fopen($source,'r');
      $data='';
      while(!feof($fp)) {
        $data.=fread($fp,8192);
      }
      fclose($fp);
      if($res['type']=='standalone') {
          if(strstr($data,'$instance')) {
            $errors[] = tra('Activity '.$res['name'].' is standalone and is using the $instance object');
          }    
      } else {
        if($res['is_interactive']=='y') {
          if(!strstr($data,'$instance->complete()')) {
            $errors[] = tra('Activity '.$res['name'].' is interactive so it must use the $instance->complete() method');
          }
        } else {
          if(strstr($data,'$instance->complete()')) {
            $errors[] = tra('Activity '.$res['name'].' is non-interactive so it must not use the $instance->complete() method');
          }
        }
        if($res['type']=='switch') {
          if(!strstr($data,'$instance->setNextActivity(')) { 
            $errors[] = tra('Activity '.$res['name'].' is switch so it must use $instance->setNextActivity($actname) method');          
          }
        }
      }    
    }
    return $errors;
  }
  
  /*! 
   Indicates if an activity with the same name exists
  */
  function activity_name_exists($p_id,$name)
  {
    $name = addslashes($this->_normalize_name($name));
    return $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `normalized_name`='$name'");
  }
  
  
  /*!
    Gets a activity fields are returned as an asociative array
  */
  function get_activity($p_id, $activity_id)
  {
    if (!isset($activity_id)) $activity_id = "NULL";
      $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `activity_id`=$activity_id";

    $result = $this->mDb->query($query);
    $res = $result->fetchRow();
    return $res;
  }
  
  /*!
   Lists activities at a per-process level
  */
  function list_activities($p_id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid=" where `p_id`=? and ((`name` like ?) or (`description` like ?))";
      $bindvars = array($p_id,$findesc,$findesc);
    } else {
      $mid=" where `p_id`=? ";
      $bindvars = array($p_id);
    }
    if($where) {
      $mid.= " and ($where) ";
    }
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` $mid order by $sort_mode";
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."activities` $mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $res['roles'] = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=?",array($res['activity_id']));
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  
  /*! 
      Removes a activity.
  */
  function remove_activity($p_id, $activity_id)
  {
    $pm = new ProcessManager();
    $proc_info = $pm->get_process($p_id);
    $actname = $this->_get_normalized_name($activity_id);
    $query = "delete from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id and `activity_id`=$activity_id";
    $this->mDb->query($query);
    $query = "select `act_from_id`, `act_to_id` from `".GALAXIA_TABLE_PREFIX."transitions` where `act_from_id`=$activity_id or `act_to_id`=$activity_id";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {  
      $this->remove_transition($res['act_from_id'], $res['act_to_id']);
    }
    $query = "delete from `".GALAXIA_TABLE_PREFIX."activity_roles` where `activity_id`=$activity_id";
    $this->mDb->query($query);
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `activity_id`=?";
    $this->mDb->query($query, array($activity_id));
    $query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `activity_id`=?";
    $this->mDb->query($query, array($activity_id));

    // And we have to remove the user and compiled files
    // for this activity
    $procname = $proc_info['normalized_name'];
    if (file_exists(GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php')) {
      unlink(GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php'); 
    }
    if (file_exists(GALAXIA_PROCESSES."/$procname/code/templates/$actname".'.tpl')) {
      unlink(GALAXIA_PROCESSES."/$procname/code/templates/$actname".'.tpl'); 
    }
    if (file_exists(GALAXIA_PROCESSES."/$procname/compiled/$actname".'.php')) {
      unlink(GALAXIA_PROCESSES."/$procname/compiled/$actname".'.php'); 
    }
    return true;
  }
  
  /*!
    Updates or inserts a new activity in the database, $vars is an asociative
    array containing the fields to update or to insert as needed.
    $p_id is the processId
    $activity_id is the activity_id  
  */
  function replace_activity($p_id, $activity_id, $vars)
  {
    $TABLE_NAME = GALAXIA_TABLE_PREFIX."activities";
    $now = date("U");
    $vars['last_modified']=$now;
    $vars['p_id']=$p_id;
    $vars['normalized_name'] = $this->_normalize_name($vars['name']);    

    $pm = new ProcessManager();
    $proc_info = $pm->get_process($p_id);
    
    
    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }
  
    if($activity_id) {
      $oldname = $this->_get_normalized_name($activity_id);
      // update mode
      $first = true;
      $query ="update `$TABLE_NAME` set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " `$key`=$value ";
        $first = false;
      }
      $query .= " where `p_id`=$p_id and `activity_id`=$activity_id ";
      $this->mDb->query($query);
      
      $newname = $vars['normalized_name'];
      // if the activity is changing name then we
      // should rename the user_file for the activity
      // remove the old compiled file and recompile
      // the activity
      
      $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$oldname.'.php';
      $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$newname.'.php';
      rename($user_file_old, $user_file_new);

      $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$oldname.'.tpl';
      $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$newname.'.tpl';
      if ($user_file_old != $user_file_new) {
        @rename($user_file_old, $user_file_new);
      }

      
      $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/'.$oldname.'.php';    
      unlink($compiled_file);
      $this->compile_activity($p_id,$activity_id);
      
      
    } else {
      
      // When inserting activity names can't be duplicated
      if($this->activity_name_exists($p_id, $vars['name'])) {
          return false;
      }
      unset($vars['activity_id']);
      // Check flow_num is numeric
      if (!isset($vars['flow_num']) || !is_numeric($vars['flow_num'])) {
          $vars['flow_num'] = 0;
      }

      // insert mode
      $first = true;
      $query = "insert into `$TABLE_NAME`(";
      foreach(array_keys($vars) as $key) {
        if (!is_numeric($key)) {
          if(!$first) $query.= ','; 
          $query.= "`$key`";
          $first = false;
        }
      } 
      $query .=") values(";
      $first = true;
      foreach($vars as $key=>$value) {
        if (!is_numeric($key)) {
          if(!$first) $query.= ','; 
          if(!is_numeric($value)) $value="'".$value."'";
          $query.= "$value";
          $first = false;
        }
      } 
      $query .=")";
      $this->mDb->query($query);
      $activity_id = $this->mDb->getOne("select max(`activity_id`) from `$TABLE_NAME` where `p_id`=$p_id and `last_modified`=$now"); 
      $ret = $activity_id;
      if(!$activity_id) {
         print("select max(`activity_id`) from `$TABLE_NAME` where `p_id`=$p_id and `last_modified`=$now");
         die;      
      }
      // Should create the code file
      $procname = $proc_info["normalized_name"];
        mkdir_p(GALAXIA_PROCESSES."/$procname/code/activities/");
        $fw = fopen(GALAXIA_PROCESSES."/$procname/code/activities/".$vars['normalized_name'].'.php','w');
        fwrite($fw,'<'.'?'.'php'."\n".'?'.'>');
        fclose($fw);
        
         if($vars['is_interactive']=='y') {
        mkdir_p(GALAXIA_PROCESSES."/$procname/code/templates/");
            $fw = fopen(GALAXIA_PROCESSES."/$procname/code/templates/".$vars['normalized_name'].'.tpl','w');
            if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
              fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
            }
            fclose($fw);
        }

         $this->compile_activity($p_id,$activity_id);
      
    }
    // Get the id
    return $activity_id;
  }
  
  /*!
   Sets if an activity is interactive or not
  */
  function set_interactivity($p_id, $actid, $value)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."activities` set `is_interactive`='$value' where `p_id`=$p_id and `activity_id`=$actid";
    $this->mDb->query($query);
    // If template does not exist then create template
    $this->compile_activity($p_id,$actid);
  }

  /*!
   Sets if an activity is auto routed or not
  */
  function set_autorouting($p_id, $actid, $value)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."activities` set `is_auto_routed`='$value' where `p_id`=$p_id and `activity_id`=$actid";
    $this->mDb->query($query);
  }

  
  /*!
  Compiles activity
  */
  function compile_activity($p_id, $activity_id)
  {
    $act_info = $this->get_activity($p_id,$activity_id);
       $actname = $act_info['normalized_name'];
    $pm = new ProcessManager();
    $proc_info = $pm->get_process($p_id);
    $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/'.$act_info['normalized_name'].'.php';    
    $template_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$actname.'.tpl';    
    $user_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$actname.'.php';
    $pre_file = GALAXIA_LIBRARY.'/compiler/'.$act_info['type'].'_pre.php';
    $pos_file = GALAXIA_LIBRARY.'/compiler/'.$act_info['type'].'_pos.php';

    while( !($fw = @fopen($compiled_file,"wb")) ) {
        mkdir_p(GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/');
    }

    // First of all add an include to to the shared code
    $shared_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/shared.php';    
    
    if (file_exists($shared_file))
        fwrite($fw, '<'."?php include_once('$shared_file'); ?".'>'."\n");
    
    mkdir_p(GALAXIA_LIBRARY.'/compiler/');
    // Before pre shared
    $fp = fopen(GALAXIA_LIBRARY.'/compiler/_shared_pre.php',"rb");
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
    }
    fclose($fp);

    // Now get pre and pos files for the activity
    while( !( $fp = @fopen($pre_file,"rb")) ) {
        mkdir_p(GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/');
    }
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
    }
    fclose($fp);
    
    mkdir_p(GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/code/activities/");
    // Get the user data for the activity 
    $fp = fopen($user_file,"rb");    
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
    }
    fclose($fp);

    // Get pos and write
    $fp = fopen($pos_file,"rb");
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
    }
    fclose($fp);

    // Shared pos
    $fp = fopen(GALAXIA_LIBRARY.'/compiler/_shared_pos.php',"rb");
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
    }
    fclose($fp);

    fclose($fw);

    //Copy the templates
    
    mkdir_p(GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/code/templates/");
    if($act_info['is_interactive']=='y' && !file_exists($template_file)) {
      $fw = fopen($template_file,'w');
      if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
        fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
      }
      fclose($fw);
    }
    if($act_info['is_interactive']!='y' && file_exists($template_file)) {
      @unlink($template_file);
      if (GALAXIA_TEMPLATES && file_exists(GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl")) {
        @unlink(GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl");
      }
    }
    if (GALAXIA_TEMPLATES && file_exists($template_file)) {
      @copy($template_file,GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl");
    }
  }
  
  /*!
   Compiles all the activities for a process
  */
  function compile_process_activities($pId)
  {
	$acts = $this->get_process_activities($pId);
	foreach( $acts as $act ) {
		$this->compile_activity($pId,$act['activity_id']);
	}
  }

  /*!
   \private
   Returns activity id by pid,name (activity names are unique)
  */
  function _get_activity_id_by_name($pid,$name)
  {
    $name = addslashes($name);
    if($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$pid and `name`='$name'")) {
      return($this->mDb->getOne("select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$pid and `name`='$name'"));    
    } else {
      return '';
    }
  }
  
  /*!
   \private Returns the activity shape
  */
  function _get_activity_shape($type)
  {
    switch($type) {
      case "start": 
          return "circle";
      case "end":
          return "doublecircle";
      case "activity":
          return "box";
      case "split":
          return "triangle";
      case "switch":
        return "diamond";
      case "join":
          return "invtriangle";
      case "standalone":
          return "hexagon";
      default:
          return "egg";            
      
    }

  }

  
  /*!
   \private Returns true if a list contains unvisited nodes
   list members are asoc arrays containing id and visited
  */
  function _list_has_unvisited_nodes($list) 
  {
    foreach($list as $node) {
      if(!$node['visited']) return true;
    }
    return false;
  }
  
  /*!
   \private Returns true if a node is in a list
   list members are asoc arrays containing id and visited
  */
  function _node_in_list($node,$list)
  {
    foreach($list as $a_node) {
      if($node['id'] == $a_node['id']) return true;
    }
    return false;
  }
  
  /*!
  \private
  Normalizes an activity name
  */
  function _normalize_name($name)
  {
    $name = str_replace(" ","_",$name);
    $name = preg_replace("/[^A-Za-z_]/",'',$name);
    return $name;
  }
  
  /*!
  \private
  Returns normalized name of an activity
  */
  function _get_normalized_name($activity_id)
  {
    return $this->mDb->getOne("select `normalized_name` from `".GALAXIA_TABLE_PREFIX."activities` where `activity_id`=$activity_id");
  }
  
  /*!
  \private
  Labels nodes 
  */  
  function _label_nodes($p_id)
  {
    
    
    ///an empty list of nodes starts the process
    $nodes = Array();
    // the end activity id
    $endId = $this->mDb->getOne("select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=? and `type`='end'", array($p_id));
    // and the number of total nodes (=activities)
    $cant = $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=?", array($p_id));
    $nodes[] = $endId;
    $label = $cant;
    $num = $cant;
    
    $query = "update `".GALAXIA_TABLE_PREFIX."activities` set `flow_num`=? where `p_id`=?";
    $this->mDb->query($query, array($cant + 1, $p_id));
    
    $seen = array();
    while(count($nodes)) {
      $newnodes = Array();
      foreach($nodes as $node) {
        // avoid endless loops
        if (isset($seen[$node])) continue;
        $seen[$node] = 1;
        $query = "update `".GALAXIA_TABLE_PREFIX."activities` set `flow_num`=? where `activity_id`=?";
        $this->mDb->query($query, array($num, $node));
        $query = "select `act_from_id` from `".GALAXIA_TABLE_PREFIX."transitions` where `act_to_id`=?";
        $result = $this->mDb->query($query, array($node));
        $ret = Array();
        while($res = $result->fetchRow()) {  
          $newnodes[] = $res['act_from_id'];
        }
      }
      $num--;
      $nodes=Array();
      $nodes=$newnodes;
      
    }

    $min = $this->mDb->getOne("select min(`flow_num`) from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=?", array($p_id));
    $query = "update `".GALAXIA_TABLE_PREFIX."activities` set `flow_num`=`flow_num`-? where `p_id`=?";
    $this->mDb->query($query, array($min, $p_id));
    
    //$query = "update ".GALAXIA_TABLE_PREFIX."activities set flow_num=0 where flow_num=$cant+1";
    //$this->mDb->query($query);
  }
   
}


?>
