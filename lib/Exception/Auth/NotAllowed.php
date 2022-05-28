<?php
/**
 * A user who is not logged in got into the wrong place
 */
class Exception_Auth_NotAllowed extends Exception{
	public function __construct($controller,$action){
		$user_id = User_Current::id();
		parent::__construct("Accessing a restricted action. C:[{$controller}] A: [{$action}] U:[{$user_id}]");
	}
}