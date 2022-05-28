<?php
/**
 * Organization_Current is not pointing to any org.
 */
class Exception_NoOrgSelected extends Exception{
	public function __construct($action_name){
		parent::__construct("[{$action_name}] logic requires Organization_Current. No Org was selected.",ERRORCODE::NO_ORG_SELECTED);
	}
}