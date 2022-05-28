<?php
/**
 * Could not find class for inclusion
 */
class Exception_ClassNotFound extends Exception{
	public function __construct($file){
		parent::__construct("failed to include [{$file}]");
	}
}