<?php namespace SiTEL\Api\Oberon;
/**
 * CLient to call Opal auth sewrver
 * @date 2020-12-02
 *
 * @author Itay Moav
 *
 */
class Opal extends \SiTEL\Api\Oberon\ClientWrapper{
	
    /**
     * @param bool $should_retry
     * @param int $no_of_retries
     * @param int $wait_for_in_seconds
     */
    public function __construct(bool $should_retry=true,int $no_of_retries=5,int $wait_for_in_seconds=10){
        $this->forever_headers = [
            'User-Agent'    => 'LMS_Opal',
            'Content-type'  => 'application/json'
        ];
        parent::__construct(app_env()['external sources']['opal']['endpoint_url'],$should_retry,$no_of_retries,$wait_for_in_seconds);
    }

    /**
     * Request the oauth2 access code/JWT in return to the token.
     * @param string $code
     * @param string $grant_type
     * @param string $redirect_uri
     * @param string $client_id
     * @param string $code_challenge
     * @return string|NULL
     */
    public function get_access_code(string $code,string $grant_type,string $redirect_uri,string $client_id,string $code_challenge) :?string{
        $body = new \stdClass;
        $body->code         = $code;
        $body->grant_type   = $grant_type;
        $body->redirect_uri = $redirect_uri;
        $body->client_id    = $client_id;
        $body->code_challenge = $code_challenge;
        
        $url = "{$this->base_url}/module.php/opal/get_access.php";
        $request = new \SiTEL\Api\Oberon\Request($url, 'POST',[],$body);
        $res = $this->send($request);
        if(!$res->body || !isset($res->body->access_token)){
            \info('Bad user input');
            return null;
        }
        return $res->body->access_token;
    }
}
