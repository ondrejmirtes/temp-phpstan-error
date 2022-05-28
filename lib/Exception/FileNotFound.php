<?php
/**
 * Could not find file for inclusion
 */
class Exception_FileNotFound extends Exception{
	public function __construct($file){
		parent::__construct("failed to include [{$file}]");
	}
}