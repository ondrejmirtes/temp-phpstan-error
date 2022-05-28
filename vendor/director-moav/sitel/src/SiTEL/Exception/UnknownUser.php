<?php namespace SiTEL\Exception;
/**
 * Could not find class for inclusion
 */
class UnknownUser extends \Exception{
    /**
     * @param int|string $user_id
     */
	public function __construct($user_id){
		parent::__construct("Unknown user id in system [{$user_id}]");
	}
}