<?php
/**
 * A user who an owner of an element and try to access it
 */
class Exception_Auth_NotOwner extends Exception{
	public function __construct($element_type,$element_id){
		$user_id = User_Current::id();
		parent::__construct("Accessing an element you do not own Elem[{$element_type}] Elem id [{$element_id}] U:[{$user_id}]");
	}
}