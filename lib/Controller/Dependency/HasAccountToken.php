<?php
/**
 * This dependency will redirect to login page
 * @author preston
 */
class Controller_Dependency_HasAccountToken extends Controller_Dependency_Action{
    
    /**
     *  If token isset and exists in Redis move forward
     */
    public function validate_dependency() {
        $token  = $this->request->get('token');
        if($token){
            $RedisToken = new Redis_User_RecoverAccount($token);
            if(Redis_User_RecoverAccount::generate_hash($RedisToken->get_simple_transient()) == $token){
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