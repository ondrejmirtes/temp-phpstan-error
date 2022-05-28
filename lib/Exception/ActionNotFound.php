<?php
/**
 * Could not find a fn action in specified controller
 */
class Exception_ActionNotFound extends Exception{
	public function __construct(Controller_Abstract $controller,$action){
		$controller = get_class($controller);
		parent::__construct("The following action [{$action}] does not exists in Controller [{$controller}].",ERRORCODE::UNKNOWN_ACTION);
	}
}