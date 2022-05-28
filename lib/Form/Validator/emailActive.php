<?php
class Form_Validator_emailActive extends Form_Validator_Abstract{
    /**
     * @var string
     */
	protected $message = "Email address is invalid.";

	/**
	 * {@inheritDoc}
	 * @see Form_Validator_Abstract::validate()
	 */
	public function validate($email){
	    $user = IDUHub_Lms3users_RbacUser::quickSelect(['username'=>$email],['id']);
		$user_id = isset($user->id)?$user->id:0;
		if(!$user_id) return false;
		if(!IDUHub_Lms3users_OrganizationUserEnrollment::count(['rbac_user_id'=>$user_id, 'status'=>IDUHub_Lms2prod_Organization_User_Enrollment::STATUS__APPROVED])) return false;	
		return true;
	}
}
