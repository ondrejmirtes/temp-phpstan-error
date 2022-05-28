<?php
/**
 * This dependency will redirect to login page
 * @author preston
 */
class Controller_Dependency_HasSecurityToken extends Controller_Dependency_Action{
    
    const   RECOVER_ACCOUNT__SALT       = 'halfateaspoon';
        
    /**
     *  If token isset and exists in Redis move forward
     */
    public function validate_dependency() {
        if($this->request->get('token')){
            $RedisToken = new Redis_User_RecoverAccount($this->request->get('token'));
            if(hash('md5',$RedisToken->get_simple_transient().self::RECOVER_ACCOUNT__SALT) == $this->request->get('token')){
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return Response_Redirect to the contact us page directing users to help desk
     */
    public function act_fail() {
        return new Response_Redirect($this->request,'/user/profile/recover/contactus/');
    }
    
    /**
     * @return Request_Default
     */
    public function act_success() {
        return $this->request;
    }
}