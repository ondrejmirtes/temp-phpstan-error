<?php
/**
 * @author itaymoav
 */
class Form_Validator_notFraudEmail extends Form_Validator_Abstract{
    /**
     * @var string
     */
	protected $message = 'This user email is marked as fake';
	
	/**
	 * @param string $value username
	 * @return boolean
	 */
	public function validate($value){
		
	    //  Check for fraudulent email addresses
	    $user = IDUHub_Lms3users_RbacUser::quickSelect(['username'=> trim($value),['is_email_fraud']]);
	    if($user && isset($user->is_email_fraud) && $user->is_email_fraud){
	        return false;
	    }
	    //This means a new user, so his email is not in the system yet
	    return true;
	}
}

