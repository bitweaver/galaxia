<?php
include_once(GALAXIA_LIBRARY.'/src/common/Observable.php');
//!! Abstract class representing the base of the API
//! An abstract class representing the API base
/*!
This class is derived by all the API classes so they get the
database connection, database methods and the Observable interface.
*/
class Base extends Observable {

  // Constructor receiving a ADODB database object.
  function Base()
  {
  	Observable::Observable();
  }

} //end of class

?>
