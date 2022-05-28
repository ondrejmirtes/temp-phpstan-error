<?php
/**
 *A translator for dates formmating for display purposes
 * @author itaymoav
 */
abstract class ViewSnipets_Data_Dates{
	static public function mysql_to_lms($mysql_date){
		$D=new DateTime(substr($mysql_date,0,10));
		return $D->format('M j, Y');	
	}
	

}

/*

/**
 * translate all times into What format we expect to see in the system
 * ex: 9:30 AM
 *
 * @param string $mysql_date yyyymmdd hh:mm:ss
 * @deprecated use SiTEL_Dates_Format
 *
function sitel_time_format($mysql_time){
	$D=new DateTime($mysql_time);
	return $D->format('g:i A');	
}





 */