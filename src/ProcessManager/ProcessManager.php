<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! ProcessManager
//! A class to maniplate processes.
/*!
  This class is used to add,remove,modify and list
  processes.
*/
class ProcessManager extends BaseManager {
  var $parser;
  var $tree;
  var $current;
  var $buffer;
  
  function ProcessManager() 
  {
	BaseManager::BaseManager();
  }
 
  /*!
    Sets a process as active
  */
  function activate_process($p_id)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."processes` set `is_active`='y' where `p_id`=$p_id";
    $this->mDb->query($query);  
    $msg = sprintf(tra('Process %d has been activated'),$p_id);
    $this->notify_all(3,$msg);
  }
  
  /*!
    De-activates a process
  */
  function deactivate_process($p_id)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."processes` set `is_active`='n' where `p_id`=$p_id";
    $this->mDb->query($query);  
    $msg = sprintf(tra('Process %d has been deactivated'),$p_id);
    $this->notify_all(3,$msg);
  }
  
  /*!
    Creates an XML representation of a process.
  */
  function serialize_process($p_id)
  {
    // <process>
    $out = '<process>'."\n";
    $proc_info = $this->get_process($p_id);
    $procname = $proc_info['normalized_name'];
    $out.= '  <name>'.htmlspecialchars($proc_info['procname']).'</name>'."\n";
    $out.= '  <is_valid>'.htmlspecialchars($proc_info['is_valid']).'</is_valid>'."\n";
    $out.= '  <version>'.htmlspecialchars($proc_info['version']).'</version>'."\n";
    $out.= '  <is_active>'.htmlspecialchars($proc_info['is_active']).'</is_active>'."\n";
    $out.='   <description>'.htmlspecialchars($proc_info['description']).'</description>'."\n";
    $out.= '  <last_modified>'.date("d/m/Y [h:i:s]",$proc_info['last_modified']).'</last_modified>'."\n";
    $out.= '  <sharedCode><![CDATA[';
    if( ($fp=@fopen(GALAXIA_PROCESSES."/$procname/code/shared.php","r")) ) {
      while(!feof($fp)) {
        $line=fread($fp,8192);
        $out.=$line;
      }
      fclose($fp);
    }
    $out.= '  ]]></sharedCode>'."\n";
    // Now loop over activities
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$p_id";
    $result = $this->mDb->query($query);
    $out.='  <activities>'."\n";
    $am = new ActivityManager();
    while($res = $result->fetchRow()) {      
      $name = $res['normalized_name'];
      $out.='    <activity>'."\n";
      $out.='      <name>'.htmlspecialchars($res['name']).'</name>'."\n";
      $out.='      <type>'.htmlspecialchars($res['type']).'</type>'."\n";
      $out.='      <description>'.htmlspecialchars($res['description']).'</description>'."\n";
      $out.='      <last_modified>'.date("d/m/Y [h:i:s]",$res['last_modified']).'</last_modified>'."\n";
      $out.='      <is_interactive>'.$res['is_interactive'].'</is_interactive>'."\n";
      $out.='      <is_auto_routed>'.$res['is_auto_routed'].'</is_auto_routed>'."\n";
      $out.='	   <expiration_time>'.$res['expiration_time'].'</expiration_time>'."\n";
      $out.='      <roles>'."\n";

      $roles = $am->get_activity_roles($res['activity_id']);
      foreach($roles as $role) {
        $out.='        <role>'.htmlspecialchars($role['name']).'</role>'."\n";
      }  
      $out.='      </roles>'."\n";
      $out.='      <code><![CDATA[';
      if( ($fp=@fopen(GALAXIA_PROCESSES."/$procname/code/activities/$name.php","r")) ) {
        while(!feof($fp)) {
          $line=fread($fp,8192);
          $out.=$line;
        }
        fclose($fp);
      }
      $out.='      ]]></code>';
      if($res['is_interactive']=='y') {
        $out.='      <template><![CDATA[';
        if( ($fp=@fopen(GALAXIA_PROCESSES."/$procname/code/templates/$name.tpl","r")) ) {
          while(!feof($fp)) {
            $line=fread($fp,8192);
            $out.=$line;
          }
          fclose($fp);
        }
        $out.='      ]]></template>';
      }
      $out.='    </activity>'."\n";    
    }
    $out.='  </activities>'."\n";
    $out.='  <transitions>'."\n";
    $transitions = $am->get_process_transitions($p_id);
    foreach($transitions as $tran) {
      $out.='     <transition>'."\n";
      $out.='       <from>'.htmlspecialchars($tran['actfromname']).'</from>'."\n";
      $out.='       <to>'.htmlspecialchars($tran['acttoname']).'</to>'."\n";
      $out.='     </transition>'."\n";
    }     
    $out.='  </transitions>'."\n";
    $out.= '</process>'."\n";
    //$fp = fopen(GALAXIA_PROCESSES."/$procname/$procname.xml","w");
    //fwrite($fp,$out);
    //fclose($fp);
    return $out;
  }
  
  /*!
    Creates  a process PHP data structure from its XML 
    representation
  */
  function unserialize_process($xml) 
  {
    // Create SAX parser assign this object as base for handlers
    // handlers are private methods defined below.
    // keep contexts and parse
    $this->parser = xml_parser_create(); 
    xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
    xml_set_object($this->parser, $this);
    xml_set_element_handler($this->parser, "_start_element_handler", "_end_element_handler");
    xml_set_character_data_handler($this->parser, "_data_handler"); 
    $aux=Array(
      'name'=>'root',
      'children'=>Array(),
      'parent' => 0,
      'data'=>''
    );
    $this->tree[0]=$aux;
    $this->current=0;
    if (!xml_parse($this->parser, $xml, true)) {
       $error = sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($this->parser)),
                    xml_get_current_line_number($this->parser));
       trigger_error($error,E_USER_WARNING);
    }
    xml_parser_free($this->parser);   
    // Now that we have the tree we can do interesting things
    //print_r($this->tree);
    $process=Array();
    $activities=Array();
    $transitions=Array();
    for($i=0;$i<count($this->tree[1]['children']);$i++) {
      // Process attributes
      $z=$this->tree[1]['children'][$i];
      $name = trim($this->tree[$z]['name']);
      if($name=='activities') {
        for($j=0;$j<count($this->tree[$z]['children']);$j++) {
          $z2 = $this->tree[$z]['children'][$j];
          // this is an activity $name = $this->tree[$z2]['name'];
          if($this->tree[$z2]['name']=='activity') {
            for($k=0;$k<count($this->tree[$z2]['children']);$k++) {
              $z3 = $this->tree[$z2]['children'][$k];
              $name = trim($this->tree[$z3]['name']);
              $value= trim($this->tree[$z3]['data']);
              if($name=='roles') {
                $roles=Array();
                for($l=0;$l<count($this->tree[$z3]['children']);$l++) {
                  $z4 = $this->tree[$z3]['children'][$l];
                  $name = trim($this->tree[$z4]['name']);
                  $data = trim($this->tree[$z4]['data']);
                  $roles[]=$data;
                }                
              } else {
                $aux[$name]=$value;
                //print("$name:$value<br />");
              }
            }
            $aux['roles']=$roles;
            $activities[]=$aux;
          }
        }
      } elseif($name=='transitions') {
        for($j=0;$j<count($this->tree[$z]['children']);$j++) {
          $z2 = $this->tree[$z]['children'][$j];
          // this is an activity $name = $this->tree[$z2]['name'];
          if($this->tree[$z2]['name']=='transition') {
            for($k=0;$k<count($this->tree[$z2]['children']);$k++) {
              $z3 = $this->tree[$z2]['children'][$k];
              $name = trim($this->tree[$z3]['name']);
              $value= trim($this->tree[$z3]['data']);
              if($name == 'from' || $name == 'to') {
                $aux[$name]=$value;
              }
            }
          }
          $transitions[] = $aux;
        }
      } else {
        $value = trim($this->tree[$z]['data']);
        //print("$name is $value<br />");
        $process[$name]=$value;
      }
    }
    $process['activities']=$activities;
    $process['transitions']=$transitions;
    return $process;
  }

  /*!
   Creates a process from the process data structure, if you want to 
   convert an XML to a process then use first unserialize_process
   and then this method.
  */
  function import_process($data)
  {
    //Now the show begins
    $am = new ActivityManager();
    $rm = new RoleManager();
    // First create the process
    if (isset($data['last_modified'])) {
	$last_modified = $data['last_modified'];
    } elseif (isset($data['lastModif'])) {
	// if Bonnie or TW
	$last_modified = $data['lastModif'];
    }

    if (isset($data['is_active'])) {
	$is_active = $data['is_active'];
    } elseif (isset($data['isActive'])) {
	// if Bonnie or TW
	$is_active = $data['isActive'];
    }

    if (isset($data['is_valid'])) {
	$is_valid = $data['is_valid'];
    } elseif (isset($data['isValid'])) {
	// if Bonnie or TW
	$is_valid = $data['isValid'];
    }

    $vars = Array(
      'procname' => $data['name'],
      'version' => $data['version'],
      'description' => $data['description'],
      'last_modified' => $last_modified,
      'is_active' => $is_active,
      'is_valid' => $is_valid
    );
    $pid = $this->replace_process(0,$vars,false);
    //Put the shared code 
    $proc_info = $this->get_process($pid);
    $procname = $proc_info['normalized_name'];
    $fp = fopen(GALAXIA_PROCESSES."/$procname/code/shared.php","w");
    fwrite($fp,$data['sharedCode']);
    fclose($fp);
    $actids = Array();
    // Foreach activity create activities
    foreach($data['activities'] as $activity) {
	    if (isset($activity['last_modified'])) {
		$last_modified = $activity['last_modified'];
	    } elseif (isset($activity['lastModif'])) {
		// if Bonnie or TW
		$last_modified = $activity['lastModif'];
	    }

	    if (isset($activity['is_interactive'])) {
		$is_interactive = $activity['is_interactive'];
	    } elseif (isset($activity['isInteractive'])) {
		// if Bonnie or TW
		$is_interactive = $activity['isInteractive'];
	    }

	    if (isset($activity['is_auto_routed'])) {
		$is_auto_routed = $activity['is_auto_routed'];
	    } elseif (isset($activity['isAutoRouted'])) {
		// if Bonnie or TW
		$is_auto_routed = $activity['isAutoRouted'];
	    }

      $expiration_time = (isset($activity['expiration_time'])) ? $activity['expiration_time'] : 0;
      $vars = Array(
        'name' => $activity['name'],
        'description' => $activity['description'],
        'type' => $activity['type'],
        'last_modified' => $last_modified,
        'is_interactive' => $is_interactive,
        'is_auto_routed' => $is_auto_routed,
        'expiration_time' => $expiration_time
      );    
      $actname=$am->_normalize_name($activity['name']);
      
      $actid = $am->replace_activity($pid,0,$vars);
      $fp = fopen(GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php',"w");
      fwrite($fp,$activity['code']);
      fclose($fp);
      if($activity['is_interactive']=='y') {
        $fp = fopen(GALAXIA_PROCESSES."/$procname/code/templates/$actname".'.tpl',"w");
        fwrite($fp,$activity['template']);
        fclose($fp);
      }
      $actids[$activity['name']] = $am->_get_activity_id_by_name($pid,$activity['name']);
      $actname = $am->_normalize_name($activity['name']);
      $now = date("U");

      foreach($activity['roles'] as $role) {
        $vars = Array(
          'name' => $role,
          'description' => $role,
          'last_modified' => $now,
        );
        if(!$rm->role_name_exists($pid,$role)) {
          $rid=$rm->replace_role($pid,0,$vars);
        } else {
          $rid = $rm->get_role_id($pid,$role);
        }
        if($actid && $rid) {
          $am->add_activity_role($actid,$rid);
        }
      }
    }
    foreach($data['transitions'] as $tran) {
      $am->add_transition($pid,$actids[$tran['from']],$actids[$tran['to']]);  
    }
    $am->compile_process_activities($pid);
    $valid = $am->validate_process_activities($pid);
    if (!$valid) {
      $this->deactivate_process($pid);
    }
    // create a graph for the new process
    $am->build_process_graph($pid);
    unset($am);
    unset($rm);
    $msg = sprintf(tra('Process %s %s imported'),$proc_info['procname'],$proc_info['version']);
    $this->notify_all(2,$msg);
  }

  /*!
   Creates a new process based on an existing process
   changing the process version. By default the process
   is created as an unactive process and the version is
   by default a minor version of the process.
   */
  ///\todo copy process activities and so     
  function new_process_version($p_id, $minor=true)
  {
    $oldpid = $p_id;
    $proc_info = $this->get_process($p_id);
    $name = $proc_info['procname'];
    if(!$proc_info) return false;
    // Now update the version
    $version = $this->_new_version($proc_info['version'],$minor);
    while($this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes` where `procname`='$name' and `version`='$version'")) {
      $version = $this->_new_version($version,$minor);
    }
    // Make new versions unactive
    $proc_info['version'] = $version;
    $proc_info['is_active'] = 'n';
    // create a new process, but don't create start/end activities
    $pid = $this->replace_process(0, $proc_info, false);
    // And here copy all the activities & so
    $am = new ActivityManager();
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=$oldpid";
    $result = $this->mDb->query($query);
    $newaid = array();
    while($res = $result->fetchRow()) {    
      $oldaid = $res['activity_id'];
      $newaid[$oldaid] = $am->replace_activity($pid,0,$res);
    }
    // create transitions
    $query = "select * from `".GALAXIA_TABLE_PREFIX."transitions` where `p_id`=$oldpid";
    $result = $this->mDb->query($query);
    while($res = $result->fetchRow()) {    
      if (empty($newaid[$res['act_from_id']]) || empty($newaid[$res['act_to_id']])) {
        continue;
      }
      $am->add_transition($pid,$newaid[$res['act_from_id']],$newaid[$res['act_to_id']]);
    }
    // create roles
    $rm = new RoleManager();
    $query = "select * from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=$oldpid";
    $result = $this->mDb->query($query);
    $newrid = array();
    while($res = $result->fetchRow()) {
      if(!$rm->role_name_exists($pid,$res['name'])) {
        $rid=$rm->replace_role($pid,0,$res);
      } else {
        $rid = $rm->get_role_id($pid,$res['name']);
      }
      $newrid[$res['role_id']] = $rid;
    }
    // map users to roles
    if (count($newrid) > 0) {
      $query = "select * from `".GALAXIA_TABLE_PREFIX."group_roles` where `p_id`=$oldpid";
      $result = $this->mDb->query($query);
      while($res = $result->fetchRow()) {
        if (empty($newrid[$res['role_id']])) {
          continue;
        }
        $rm->map_group_to_role($pid,$res['group_id'],$newrid[$res['role_id']]);
      }
    }
    // add roles to activities
    if (count($newaid) > 0 && count($newrid ) > 0) {
      $query = "select * from ".GALAXIA_TABLE_PREFIX."activity_roles where activity_id in (" . join(', ',array_keys($newaid)) . ")";
      $result = $this->mDb->query($query);
      while($res = $result->fetchRow()) {
        if (empty($newaid[$res['activity_id']]) || empty($newrid[$res['role_id']])) {
          continue;
        }
        $am->add_activity_role($newaid[$res['activity_id']],$newrid[$res['role_id']]);
      }
    }

    //Now since we are copying a process we should copy
    //the old directory structure to the new directory
    $oldname = $proc_info['normalized_name'];
    $newname = $this->_get_normalized_name($pid);
    $this->_rec_copy(GALAXIA_PROCESSES."/$oldname",GALAXIA_PROCESSES."/$newname");

    // create a graph for the new process
    $am->build_process_graph($pid);
    return $pid;
  }
  
  /*!
   This function can be used to check if a process name exists, note that
   this is NOT used by replace_process since that function can be used to
   create new versions of an existing process. The application must use this
   method to ensure that processes have unique names.
  */
  function process_name_exists($name,$version)
  {
    $name = addslashes($this->_normalize_name($name,$version));
    return $this->mDb->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes` where `normalized_name`='$name'");
  }
  
  
  /*!
    Gets a process by p_id. Fields are returned as an asociative array
  */
  function get_process($p_id)
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."processes` where `p_id`=$p_id";
    $result = $this->mDb->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
  }
  
  /*!
   Lists processes (all processes)
  */
  function list_processes($offset,$maxRecords,$sort_mode,$find,$where='')
  {
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
    $query = "select * from `".GALAXIA_TABLE_PREFIX."processes` $mid order by $sort_mode";
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."processes` $mid";
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
   Marks a process as an invalid process
  */
  function invalidate_process($pid)
  {
    $query = "update `".GALAXIA_TABLE_PREFIX."processes` set `is_valid`='n' where `p_id`=$pid";
    $this->mDb->query($query);
  }
  
  /*! 
    Removes a process by p_id
  */
  function remove_process($p_id)
  {
    $this->deactivate_process($p_id);
    $name = $this->_get_normalized_name($p_id);
    // Remove process activities
    $aM = new ActivityManager();
    $query = "select `activity_id` from `".GALAXIA_TABLE_PREFIX."activities` where `p_id`=?";
    $result = $this->mDb->query($query,array($p_id));
    while($res = $result->fetchRow()) {
      $aM->remove_activity($p_id,$res['activity_id']);
    }

    // Remove process roles
    $rM = new RoleManager();
    $query = "select `role_id` from `".GALAXIA_TABLE_PREFIX."roles` where `p_id`=?";
    $result = $this->mDb->query($query,array($p_id));
    while($res = $result->fetchRow()) {
      $rM->remove_role($p_id,$res['role_id']);
    }

    $query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `p_id`=?";
    $this->mDb->query($query, array($p_id));
    
    // Remove the directory structure
    if (!empty($name) && is_dir(GALAXIA_PROCESSES."/$name")) {
      $this->_remove_directory(GALAXIA_PROCESSES."/$name",true);
    }
    if (GALAXIA_TEMPLATES && !empty($name) && is_dir(GALAXIA_TEMPLATES."/$name")) {
      $this->_remove_directory(GALAXIA_TEMPLATES."/$name",true);
    }
    // And finally remove the proc
    $query = "delete from `".GALAXIA_TABLE_PREFIX."processes` where `p_id`=$p_id";
    $this->mDb->query($query);
    $msg = sprintf(tra('Process %s removed'),$name);
    $this->notify_all(5,$msg);
    
    return true;
  }
  
  /*!
    Updates or inserts a new process in the database, $vars is an asociative
    array containing the fields to update or to insert as needed.
    $p_id is the processId
  */
  function replace_process($p_id, $vars, $create = true)
  {
    $TABLE_NAME = GALAXIA_TABLE_PREFIX."processes";
    $now = date("U");
    $vars['last_modified']=$now;
    $vars['normalized_name'] = $this->_normalize_name($vars['procname'],$vars['version']);
    foreach($vars as $key=>$value)
    {
        $vars[$key]=addslashes($value);
    }
  
    if($p_id) {
      // update mode
      $old_proc = $this->get_process($p_id);
      $first = true;
      $query ="update `$TABLE_NAME` set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)||strstr($value,'.')) $value="'".$value."'";
        $query.= " `$key`=$value ";
        $first = false;
      }
      $query .= " where `p_id`=$p_id ";
      $this->mDb->query($query);
      // Note that if the name is being changed then
      // the directory has to be renamed!
      $oldname = $old_proc['normalized_name'];
      $newname = $vars['normalized_name'];
      if ($newname != $oldname) {
        rename(GALAXIA_PROCESSES."/$oldname",GALAXIA_PROCESSES."/$newname");
        $am = new ActivityManager();
        $am->compile_process_activities($p_id);
      }
      $msg = sprintf(tra('Process %s has been updated'),$vars['procname']);     
      $this->notify_all(3,$msg);
    } else {
      unset($vars['p_id']);
      // insert mode
      $name = $this->_normalize_name($vars['procname'],$vars['version']);
      $this->_create_directory_structure($name);
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
          if(!is_numeric($value)||strstr($value,'.')) $value="'".$value."'";
          $query.= "$value";
          $first = false;
        }
      } 
      $query .=")";
      $this->mDb->query($query);
      $p_id = $this->mDb->getOne("select max(`p_id`) from `$TABLE_NAME` where `last_modified`=$now"); 
      // Now automatically add a start and end activity 
      // unless importing ($create = false)
      if($create) {
        $aM= new ActivityManager();
        $vars1 = Array(
          'name' => 'start',
          'description' => 'default start activity',
          'type' => 'start',
          'is_interactive' => 'y',
          'is_auto_routed' => 'y'
        );
        $vars2 = Array(
          'name' => 'end',
          'description' => 'default end activity',
          'type' => 'end',
          'is_interactive' => 'n',
          'is_auto_routed' => 'y'
        );
  
        $aM->replace_activity($p_id,0,$vars1);
        $aM->replace_activity($p_id,0,$vars2);
      }
    $msg = sprintf(tra('Process %s has been created'),$vars['procname']);     
    $this->notify_all(4,$msg);
    }
    // Get the id
    return $p_id;
  }
   
  /*!
   \private
   Gets the normalized name of a process by pid
  */
  function _get_normalized_name($p_id)
  {
    $info = $this->get_process($p_id);
    return $info['normalized_name'];
  }
   
  /*!
   \private
   Normalizes a process name
  */
  function _normalize_name($name, $version)
  {
    $name = $name.'_'.$version;
    $name = str_replace(" ","_",$name);
    $name = preg_replace("/[^0-9A-Za-z\_]/",'',$name);
    return $name;
  }
   
  /*!
   \private
   Generates a new minor version number
  */
  function _new_version($version,$minor=true)
  {
    $parts = explode('.',$version);
    if($minor) {
      $parts[count($parts)-1]++;
    } else {
      $parts[0]++;
      for ($i = 1; $i < count($parts); $i++) {
        $parts[$i] = 0;
      }
    }
    return implode('.',$parts);
  }
   
  /*!
   \private
   Creates directory structure for process
  */
  function _create_directory_structure($name)
  {
    // Create in processes a directory with this name
    (!file_exists(GALAXIA_PROCESSES."/$name")) ? mkdir_p(GALAXIA_PROCESSES."/$name",0770) : '';
    (!file_exists(GALAXIA_PROCESSES."/$name/graph")) ? mkdir_p(GALAXIA_PROCESSES."/$name/graph",0770) : '';
    (!file_exists(GALAXIA_PROCESSES."/$name/code")) ? mkdir_p(GALAXIA_PROCESSES."/$name/code",0770) : '';
    (!file_exists(GALAXIA_PROCESSES."/$name/compiled")) ? mkdir_p(GALAXIA_PROCESSES."/$name/compiled",0770) : '';
    (!file_exists(GALAXIA_PROCESSES."/$name/code/activities")) ? mkdir_p(GALAXIA_PROCESSES."/$name/code/activities",0770) : '';
    (!file_exists(GALAXIA_PROCESSES."/$name/code/templates")) ? mkdir_p(GALAXIA_PROCESSES."/$name/code/templates",0770) : '';
    if (defined(GALAXIA_TEMPLATES)) {
      (!file_exists(GALAXIA_TEMPLATES."/$name")) ? mkdir_p(GALAXIA_TEMPLATES."/$name",0770) : '';
    }
    // Create shared file
    $fp = fopen(GALAXIA_PROCESSES."/$name/code/shared.php","w");
    fwrite($fp,'<'.'?'.'php'."\n".'?'.'>');
    fclose($fp);
  }
   
  /*!
   \private
   Removes a directory recursively
  */
  function _remove_directory($dir,$rec=false)
  {
    // Prevent a disaster
    if(trim($dir) == '/'|| trim($dir)=='.' || trim($dir)=='templates' || trim($dir)=='templates/') return false;
    $h = opendir($dir);
    while(($file = readdir($h)) != false) {
      if(is_file($dir.'/'.$file)) {
        @unlink($dir.'/'.$file);
      } else {
        if($rec && $file != '.' && $file != '..') {
          $this->_remove_directory($dir.'/'.$file, true);
        }
      }
    }
    closedir($h);   
    @rmdir($dir);
    @unlink($dir);
  }

  function _rec_copy($dir1,$dir2)
  {
    @mkdir_p($dir2,0777);
    $h = opendir($dir1);
    while(($file = readdir($h)) !== false) {
      if(is_file($dir1.'/'.$file)) {
        copy($dir1.'/'.$file,$dir2.'/'.$file);
      } else {
        if($file != '.' && $file != '..') {
          $this->_rec_copy($dir1.'/'.$file, $dir2.'/'.$file);
        }
      }
    }
    closedir($h);   
  }

  function _start_element_handler($parser,$element,$attribs)
  {
    $aux=Array('name'=>$element,
               'data'=>'',
               'parent' => $this->current,
               'children'=>Array());
    $i = count($this->tree);           
    $this->tree[$i] = $aux;

    $this->tree[$this->current]['children'][]=$i;
    $this->current=$i;
  }


  function _end_element_handler($parser,$element)
  {
    //when a tag ends put text
    $this->tree[$this->current]['data']=$this->buffer;           
    $this->buffer='';
    $this->current=$this->tree[$this->current]['parent'];
  }


  function _data_handler($parser,$data)
  {
    $this->buffer.=$data;
  }

}


?>
