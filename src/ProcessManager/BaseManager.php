<?php
include_once(GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Abstract class representing the base of the API
//! An abstract class representing the API base
/*!
This class is derived by all the API classes so they get the
database connection, database methods and the Observable interface.
*/
class BaseManager extends Base {

  // Constructor receiving an ADODB database object.
  function BaseManager()
  {
  	Base::Base();
  }
   /* Make ending day - this makes the ending date for an instance to be done taking the date when it was created an
   * the expirationTime from the activity */
   function make_ending_date ($initTime, $expirationTime) {
   	if ($expirationTime == 0) {
   		return 0;
   	}
   	$years = (int)($expirationTime/535680);
   	$months = (int)(($expirationTime-($years*53680))/44640);
   	$days = (int)(($expirationTime-($years*53680)-($months*44640))/1440);
   	$hours = (int)(($expirationTime-($years*53680)-($months*44640)-($days*1440))/60);
   	$minutes = (int)($expirationTime-($years*53680)-($months*44640)-($days*1440)-($hours*60));
   	$endingDate = $initTime;
   	$endingDate = strtotime ("+ $years year",$endingDate);
   	$endingDate = strtotime ("+ $months month",$endingDate);
   	$endingDate = strtotime ("+ $days day",$endingDate);
   	$endingDate = strtotime ("+ $hours hour",$endingDate);
   	$endingDate = strtotime ("+ $minutes minute",$endingDate);
   	return $endingDate;
   }

} //end of class

?>
