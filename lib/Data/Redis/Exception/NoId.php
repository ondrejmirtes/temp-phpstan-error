<?php
class Data_Redis_Exception_NoId extends Exception{
	public function __construct($key_boss){
		parent::__construct('No main ID supplied, can not work with Redis for ' . $key_boss);
	}
}