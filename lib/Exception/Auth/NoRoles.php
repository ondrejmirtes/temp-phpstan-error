<?php
/**
 * A user who an owner of an element and try to access it
 */
class Exception_Auth_NoRoles extends Exception{
	public function __construct(){
		$user_id = User_Current::id();
		parent::__construct("User:[{$user_id}] has no roles");
	}
}