<?php
/**
 * A user who is in the wrong organization for there actions.  Point them the right way.
*/
class Exception_Auth_WrongOrgTask extends Exception{
	
	private $error_data;
	
	/**
	 * Exception when the user does not have a needed task for the current organization but has it for another
	 * @param String $controller
	 * @param String $action
	 * @param array $organization
	 */
	public function __construct($controller,$action, array $organizations){
		$user_id = User_Current::id();
		$this->error_data = $organizations; 
		parent::__construct("User access from wrong organization.  Not a real exception! C:[{$controller}] A: [{$action}] U:[{$user_id}]",ERRORCODE::WRONG_ORG_TASK);
	}
	
	/**
	 *  Gets the exceptions error data
	 *  @return array
	 */
	public function getErrorData(){
		return $this->error_data;
	}
}