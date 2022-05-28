<?php namespace SiTEL\DataSources\Redis;

class Exception_NoId extends \Exception{
    /**
     * 
     * @param string $key_boss
     */
	public function __construct($key_boss){
		parent::__construct('No main ID supplied, can not work with Redis for ' . $key_boss);
	}
}