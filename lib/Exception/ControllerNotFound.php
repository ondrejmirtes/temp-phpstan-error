<?php
/**
 * Could not find a fn action in specified controller
 */
class Exception_ControllerNotFound extends Exception{
	public function __construct($controller){
		parent::__construct("The following controller [{$controller}] does not exists.",ERRORCODE::UNKNOWN_CONTROLLER);
	}
}