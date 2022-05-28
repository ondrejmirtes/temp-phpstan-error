<?php
/**
 * Could not find a fn action in specified controller
 */
class Exception_ViewNotFound extends Exception{
	public function __construct($path){
		parent::__construct("The following view [{$path}] does not exists");
	}
}