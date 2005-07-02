<?php
//!! Observable
//! An abstract class implementing observable objects
/*!
  \abstract
  Methods to override: NONE
  This class implements the Observer design pattern defining Observable
  objects, when a class extends Observable Observers can be attached to
  the class listening for some event. When an event is detected in any
  method of the derived class the method can call notifyAll($event,$msg)
  to notify all the observers listening for event $event.
  The Observer objects must extend the Observer class and define the
  notify($event,$msg) method.
*/

require_once( KERNEL_PKG_PATH.'BitBase.php' );

class Observable extends BitBase {
  var $_observers=Array();
  
  function Observable() {
 	BitBase::BitBase(); 
  }
  
  /*!
   This method can be used to attach an object to the class listening for
   some specific event. The object will be notified when the specified
   event is triggered by the derived class.
  */
  function attach($event, &$obj)
  {
    if (!is_object($obj)) {
    	return false;
    }
    $obj->_observer_id = uniqid(rand());
    $this->_observers[$event][$obj->_observer_id] = &$obj;
  }
  
  /*!
   Attaches an object to the class listening for any event.
   The object will be notified when any event occurs in the derived class.
  */
  function attach_all(&$obj)
  {
    if (!is_object($obj)) {
    	return false;
    }
    $obj->_observer_id = uniqid(rand());
    $this->_observers['all'][$obj->_observer_id] = &$obj;
  }
  
  /*!
   Detaches an observer from the class.
  */
  function dettach(&$obj)
  {
  	if (isset($this->_observers[$obj->_observer_id])) {
    	unset($this->_observers[$obj->_observer_id]);
    }
  }
  
  /*!
  \protected
  Method used to notify objects of an event. This is called in the
  methods of the derived class that want to notify some event.
  */
  function notify_all($event, $msg)
  {
  	//reset($this->_observers[$event]);
  	if(isset($this->_observers[$event])) {
    	foreach ($this->_observers[$event] as $observer) {
    		$observer->notify($event,$msg);
    	}
    }
	if(isset($this->_observers['all'])) {
    	foreach ($this->_observers['all'] as $observer) {
    		$observer->notify($event,$msg);
    	}
    }
    
  } 

}
?>
