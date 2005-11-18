<?php
include_once(GALAXIA_LIBRARY.'/src/common/Base.php');
//!! GUI
//! A GUI class for use in typical user interface scripts
/*!
This class provides methods for use in typical user interface scripts
*/
class GUI extends Base {

  /*!
  List user processes, user processes should follow one of these conditions:
  1) The process has an instance assigned to the user
  2) The process has a begin activity with a role compatible to the
     user roles
  3) The process has an instance assigned to NULL and the
     roles for the activity match the roles assigned to
     the user
  The method returns the list of processes that match this
  and it also returns the number of instances that are in the
  process matching the conditions.
  */
  function gui_list_user_processes($user_id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    // FIXME: this doesn't support multiple sort criteria
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);

    if (!isset($user_id))
	galaxia_show_error("No user id");

    $mid = "where gp.`is_active`=? and ugm.`user_id`=?";
    $bindvars = array('y',$user_id);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ((gp.`procname` like ?) or (gp.`description` like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }

    $query = "select distinct(gp.`p_id`),
                     gp.`is_active`,
                     gp.`procname`,
                     gp.`normalized_name` as `normalized_name`,
                     gp.`version` as `version`
              from `".GALAXIA_TABLE_PREFIX."processes` gp
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gp.`p_id`=ga.`p_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		$mid order by $sort_mode";
    $query_cant = "select count(distinct(gp.`p_id`))
              from `".GALAXIA_TABLE_PREFIX."processes` gp
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gp.`p_id`=ga.`p_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		$mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Get instances per activity
      $p_id=$res['p_id'];
      $res['activities']=$this->mDb->getOne("select count(distinct(ga.`activity_id`))
              from `".GALAXIA_TABLE_PREFIX."processes` gp
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gp.`p_id`=ga.`p_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		where gp.`p_id`=? and ugm.`user_id`=?",
              array($p_id,$user_id));
      $res['instances']=$this->mDb->getOne("select count(distinct(gi.`instance_id`))
              from `".GALAXIA_TABLE_PREFIX."instances` gi
                INNER JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gia.`activity_id`=gar.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON gar.`role_id`=ggr.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		where gi.`p_id`=? and gia.`status` <> ? and (gia.`user_id`=? or (gia.`user_id` is ? and ugm.`user_id`=?))",
		array($p_id,'completed',$user_id,NULL,$user_id));
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function gui_list_user_activities($user_id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    // FIXME: this doesn't support multiple sort criteria
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);

    if (!isset($user_id))
	galaxia_show_error("No user id");

    $mid = "where gp.`is_active`=? and ugm.`user_id`=?";
    $bindvars = array('y',$user_id);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ((ga.`name` like ?) or (ga.`description` like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }

    $query = "select distinct(ga.`activity_id`),
                     ga.`name`,
                     ga.`type`,
                     gp.`procname`,
                     ga.`is_interactive`,
                     ga.`is_auto_routed`,
                     ga.`activity_id`,
                     ga.`flow_num`,
                     gp.`version` as `version`,
                     gp.`p_id`,
                     gp.`is_active`
              from `".GALAXIA_TABLE_PREFIX."processes` gp
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gp.`p_id`=ga.`p_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		$mid order by $sort_mode";
    $query_cant = "select count(distinct(ga.`activity_id`))
              from `".GALAXIA_TABLE_PREFIX."processes` gp
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gp.`p_id`=ga.`p_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		$mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Get instances per activity
      $res['instances']=$this->mDb->getOne("select count(distinct(gi.`instance_id`))
              from `".GALAXIA_TABLE_PREFIX."instances` gi
                INNER JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gia.`activity_id`=gar.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON gar.`role_id`=ggr.`role_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
		where gia.`activity_id`=? and gia.`status` <> ? and (gia.`user_id`=? or (gia.`user_id` is ? and ugm.`user_id`=?))",
		array($res['activity_id'],'completed',$user_id,NULL,$user_id));
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function gui_list_user_instances($user_id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    // FIXME: this doesn't support multiple sort criteria
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);

    if (!isset($user_id))
	galaxia_show_error("No user id");

    $mid = "where (gia.`user_id`=? or (gia.`user_id` is ? and ugm.`user_id`=?))";
    $bindvars = array($user_id,NULL,$user_id);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ((ga.`name` like ?) or (ga.`description` like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }

    $query = "select distinct(gi.`instance_id`),
                     gi.`started`,
                     gi.`owner_id`,
                     gi.`name` as `iname`,
                     gia.`user_id`,
                     gia.`started` as `ia_started`,
                     gi.`status`,
                     gia.`status` as `actstatus`,
                     ga.`name`,
                     ga.`type`,
		     ga.`expiration_time` as exptime,
                     gp.`procname`,
                     ga.`is_interactive`,
                     ga.`is_auto_routed`,
                     ga.`activity_id`,
                     gp.`version` as `version`,
                     gp.`p_id`
              from `".GALAXIA_TABLE_PREFIX."instances` gi
                INNER JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gia.`activity_id`=gar.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`p_id`=ga.`p_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
              $mid order by $sort_mode";
    $query_cant = "select count(distinct(gi.`instance_id`))
              from `".GALAXIA_TABLE_PREFIX."instances` gi
                INNER JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instance_id`=gia.`instance_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activity_id`=ga.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gia.`activity_id`=gar.`activity_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON ggr.`role_id`=gar.`role_id`
                INNER JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`p_id`=ga.`p_id`
		INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
              $mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Get instances per activity
      $res['exptime'] = $this->make_ending_date ($res['ia_started'],$res['exptime']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*!
  Abort an instance - this terminates the instance with status 'aborted', and removes all running activities
  */
  function gui_abort_instance($user_id,$activity_id,$instance_id)
  {
    // Users can only abort instances they're currently running, or instances that they're the owner of
    if(!$this->mDb->getOne("select count(*)
                       from `".GALAXIA_TABLE_PREFIX."instance_activities` gia
                       INNER JOIN `".GALAXIA_TABLE_PREFIX."instances` gi ON gi.`instance_id`=gia.`instance_id`
                       where `activity_id`=? and gia.`instance_id`=? and (gia.`user_id`=? or gi.`owner_id`=?)",
                       array($activity_id,$instance_id,$user_id,$user_id)))
      return false;
    include_once(GALAXIA_LIBRARY.'/src/API/Instance.php');
    $instance = new Instance();
    $instance->getInstance($instance_id);
    if (!empty($instance->instance_id)) {
        $instance->abort($activity_id,$user_id);
    }
    unset($instance);
  }

  /*!
  Exception handling for an instance - this sets the instance status to 'exception', but keeps all running activities.
  The instance can be resumed afterwards via gui_resume_instance().
  */
  function gui_exception_instance($user_id,$activity_id,$instance_id)
  {
    // Users can only do exception handling for instances they're currently running, or instances that they're the owner of
    if(!$this->mDb->getOne("select count(*)
                       from `".GALAXIA_TABLE_PREFIX."instance_activities` gia, `".GALAXIA_TABLE_PREFIX."instances` gi
                       where `activity_id`=? and gia.`instance_id`=? and (gia.`user_id`=? or gi.`owner_id`=?)",
                       array($activity_id,$instance_id,$user_id,$user_id)))
      return false;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances`
              set `status`=?
              where `instance_id`=?";
    $this->mDb->query($query, array('exception',$instance_id));
  }

  /*!
  Resume an instance - this sets the instance status from 'exception' back to 'active'
  */
  function gui_resume_instance($user_id,$activity_id,$instance_id)
  {
    // Users can only resume instances they're currently running, or instances that they're the owner of
    if(!$this->mDb->getOne("select count(*)
                       from `".GALAXIA_TABLE_PREFIX."instance_activities` gia, `".GALAXIA_TABLE_PREFIX."instances` gi
                       INNER JOIN `".GALAXIA_TABLE_PREFIX."instances` gi ON gi.`instance_id`=gia.`instance_id`
                       where `activity_id`=? and gia.`instance_id`=? and (`user_id`=? or `owner_id`=?)",
                       array($activity_id,$instance_id,$user_id,$user_id)))
      return false;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances`
              set `status`=?
              where `instance_id`=?";
    $this->mDb->query($query, array('active',$instance_id));
  }


  function gui_send_instance($user_id,$activity_id,$instance_id)
  {
    if (!isset($user_id))
	galaxia_show_error("No user id");

    if(!
      ($this->mDb->getOne("select count(*)
                      from `".GALAXIA_TABLE_PREFIX."instance_activities`
                      where `activity_id`=? and `instance_id`=? and `user_id`=?",
                      array($activity_id,$instance_id,$user_id)))
      ||
      ($this->mDb->getOne("select count(*)
                      from `".GALAXIA_TABLE_PREFIX."instance_activities` gia
                      INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=gia.`activity_id`
                      INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON gar.`role_id`=ggr.`role_id`
		      INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
                      where gia.`instance_id`=? and gia.`activity_id`=? and gia.`user_id` is ? and ugm.`user_id`=?",
                      array($instance_id,$activity_id,NULL,$user_id)))
      ) return false;
    include_once(GALAXIA_LIBRARY.'/src/API/Instance.php');
    $instance = new Instance();
    $instance->getInstance($instance_id);
    $instance->complete($activity_id,true,false);
    unset($instance);
  }

  function gui_release_instance($user_id,$activity_id,$instance_id)
  {
    if(!$this->mDb->getOne("select count(*)
                       from `".GALAXIA_TABLE_PREFIX."instance_activities`
                       where `activity_id`=? and `instance_id`=? and `user_id`=?",
                       array($activity_id,$instance_id,$user_id))) return false;
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities`
              set `user_id`=?
              where `instance_id`=? and `activity_id`=?";
    $this->mDb->query($query, array(NULL,$instance_id,$activity_id));
  }

  function gui_grab_instance($user_id,$activity_id,$instance_id)
  {
    if (!isset($user_id))
	galaxia_show_error("No user id");

    // Grab only if roles are ok
    if(!$this->mDb->getOne("select count(*)
                      from `".GALAXIA_TABLE_PREFIX."instance_activities` gia
                      INNER JOIN `".GALAXIA_TABLE_PREFIX."activity_roles` gar ON gar.`activity_id`=gia.`activity_id`
                      INNER JOIN `".GALAXIA_TABLE_PREFIX."group_roles` ggr ON gar.`role_id`=ggr.`role_id`
		      INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ugm.`group_id`=ggr.`group_id`
                      where gia.`instance_id`=? and gia.`activity_id`=? and gia.`user_id` is ? and ugm.`user_id`=?",
                      array($instance_id,$activity_id,NULL,$user_id)))  return false;
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities`
              set `user_id`=?
              where `instance_id`=? and `activity_id`=?";
    $this->mDb->query($query, array($user_id,$instance_id,$activity_id));
  }
}
?>
