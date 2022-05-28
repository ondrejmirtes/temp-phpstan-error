<?php
/**
 * A user who is not logged in got into the wrong place
 */
class Exception_Auth_NotLoggedin extends Exception{
	public function __construct(){
		parent::__construct('Trying to access members only areas without being logged in',ERRORCODE::AUTH_FAILED);
	}
}