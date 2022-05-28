<?php
class Exception_NoDataFound extends Exception{
	public function __construct(){
		$msg = "Requested data brought back an empty result.";
		parent::__construct($msg,ERRORCODE::NO_INFORMATION_FOUND);
	}
}
