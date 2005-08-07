<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! RoleManager
//! A class to maniplate roles.
/*!
  This class is used to add,remove,modify and list
  roles used in the Workflow engine.
  Roles are managed in a per-process level, each
  role belongs to some process.
*/

/*!TODO
  Add a method to check if a role name exists in a process (to be used
  to prevent duplicate names)
*/

class RoleManager extends BaseManager {
    
  function RoleManager() 
  {
	BaseManager::BaseManager();
  }
  
  function get_role_id($pid,$name)
  {
    $name = addslashes($name);
    return ($this->mDb->getOne("select `role_id` from `".GALAXIA_TABLE_PREFIX."roles` where `name`='$name' and `p_id`=$pid"));
  }
  
  /*!
    Gets a role fields are returned as an asociative array
  */
  function get_role($p_id, $role_id)
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=? and `role_id`=?";
  $result = $this->mDb->query($query,array($p_id, $role_id));
  $res = $result->fetchRow();
  return $res;
  }
  
  /*!
    Indicates if a role exists
  */
  function role_name_exists($pid,$name)
  {
    $name = addslashes($name);
    return ($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=$pid and `name`='$name'"));
  }
  
  /*!
    Maps a group to a role
  */
  function map_group_to_role($p_id,$group_id,$role_id)
  {
  $this->remove_mapping($group_id, $role_id);
  $query = "insert into `".GALAXIA_TABLE_PREFIX."group_roles`(`p_id`, `group_id`, `role_id`) values(?,?,?)";
  $this->mDb->query($query,array($p_id,$group_id,$role_id));
  }
  
  /*!
    Removes a mapping
  */
  function remove_mapping($group_id,$role_id)
  { 
  $query = "delete from `".GALAXIA_TABLE_PREFIX."group_roles` where `group_id`=? and `role_id`=?";
  $this->mDb->query($query,array($group_id, $role_id));
  }
  
  /*!
    List mappings
  */
  function list_mappings($p_id,$offset,$maxRecords,$sort_mode,$find)  {
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);
    if($find) {
      // no more quoting here - this is done in bind vars already
      $findesc = '%'.$find.'%';
      $query = "select `name`,gr.`role_id`,gur.`group_id` as group_id,`group_name` from `".GALAXIA_TABLE_PREFIX."roles` gr, `".GALAXIA_TABLE_PREFIX."group_roles` gur
	INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON ug.`group_id`=gur.`group_id`
	where gr.`role_id`=gur.`role_id` and gur.`p_id`=? and ((`name` like ?) or (`group_id` like ?) or (`description` like ?)) order by $sort_mode";
      $result = $this->mDb->query($query,array($p_id,$findesc,$findesc,$findesc), $maxRecords, $offset);
      $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."roles` gr, `".GALAXIA_TABLE_PREFIX."group_roles` gur
	INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON ug.`group_id`=gur.`group_id`
	where gr.`role_id`=gur.`role_id` and gur.`p_id`=? and ((`name` like ?) or (`group_id` like ?) or (`description` like ?))";
      $cant = $this->mDb->getOne($query_cant,array($p_id,$findesc,$findesc,$findesc));
    } else {
      $query = "select `name`,gr.`role_id`,gur.`group_id` as group_id,`group_name` from `".GALAXIA_TABLE_PREFIX."roles` gr, `".GALAXIA_TABLE_PREFIX."group_roles` gur
	INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON ug.`group_id`=gur.`group_id`
	where gr.`role_id`=gur.`role_id` and gur.`p_id`=? order by $sort_mode";
      $result = $this->mDb->query($query,array($p_id), $maxRecords, $offset);
      $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."roles` gr, `".GALAXIA_TABLE_PREFIX."group_roles` gur
	INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON ug.`group_id`=gur.`group_id`
	where gr.`role_id`=gur.`role_id` and gur.`p_id`=?";
      $cant = $this->mDb->getOne($query_cant,array($p_id));
    }
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  /*!
    Lists roles at a per-process level
  */
  function list_roles($p_id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = $this->mDb->convert_sortmode($sort_mode);
    if($find) {
      // no more quoting here - this is done in bind vars already
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
    $query = "select * from `".GALAXIA_TABLE_PREFIX."roles` $mid order by $sort_mode";
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."roles` $mid";
    $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->mDb->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  
  /*! 
    Removes a role.
  */
  function remove_role($p_id, $role_id)
  {
    $query = "delete from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=? and `role_id`=?";
    $this->mDb->query($query,array($p_id, $role_id));
    $query = "delete from `".GALAXIA_TABLE_PREFIX."activity_roles` where `role_id`=?";
    $this->mDb->query($query,array($role_id));
    $query = "delete from `".GALAXIA_TABLE_PREFIX."group_roles` where `role_id`=?";
    $this->mDb->query($query,array($role_id));
  }
  
  /*!
    Updates or inserts a new role in the database, $vars is an asociative
    array containing the fields to update or to insert as needed.
    $p_id is the processId
    $role_id is the role_id  
  */
  function replace_role($p_id, $role_id, $vars)
  {
    $TABLE_NAME = GALAXIA_TABLE_PREFIX."roles";
    $now = date("U");
    $vars['last_modified']=$now;
    $vars['p_id']=$p_id;
    
    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }
  
    if($role_id) {
      // update mode
      $first = true;
      $query ="update `$TABLE_NAME` set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " `$key`=$value ";
        $first = false;
      }
      $query .= " where `p_id`=$p_id and `role_id`=$role_id ";
      $this->mDb->query($query);
    } else {
      $name = $vars['name'];
      if ($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=$p_id and `name`='$name'")) {
        return false;
      }
      unset($vars['role_id']);
      // insert mode
      $first = true;
      $query = "insert into `$TABLE_NAME` (";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ','; 
        $query .= "`$key`";
        $first = false;
      } 
      $query .=") values (";
      $first = true;
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ','; 
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= "$value";
        $first = false;
      } 
      $query .=")";
      $this->mDb->query($query);
      $role_id = $this->mDb->getOne("select max(`role_id`) from `$TABLE_NAME` where `p_id`=$p_id and `last_modified`=$now"); 
    }
    // Get the id
    return $role_id;
  }
}

?>
