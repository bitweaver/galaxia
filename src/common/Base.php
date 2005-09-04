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

	/* Make ending day - this makes the ending date for an instance to be done taking the date when it was created an
   * the expiration_time from the activity */
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
   
   /*Get expiration members - this returns an array with the representation in years, months, days, hours
   * and minutes of the expirationTime that is stored in the db in minutes*/
   function get_expiration_members ($expirationTime) {
   	$time = array();
   	$time['year'] = (int)($expirationTime/535680);
	$time['month'] = (int)(($expirationTime-($time['year']*535680))/44640);
	$time['day'] = (int)(($expirationTime-($time['year']*535680)-($time['month']*44640))/1440);
	$time['hour'] = (int)(($expirationTime-($time['year']*535680)-($time['month']*44640)-($time['day']*1440))/60);
	$time['minute'] = (int)($expirationTime-($time['year']*535680)-($time['month']*44640)-($time['day']*1440)-($time['hour']*60));
	return $time;
   }

} //end of class

?>
