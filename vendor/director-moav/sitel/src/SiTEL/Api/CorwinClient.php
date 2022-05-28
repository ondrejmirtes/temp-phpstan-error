<?php namespace SiTEL\Api;

/**
 *  Client Library for contacting the LMS API.
 *  Uses GuzzleHTTP to make requests to sitelms
 *  
 *  See the demo folder for examples how this works.
 *  Please add there new functionality tests too.  
 *  
 *  @author preston
 *
 */
class CorwinClient
{
    private const 
            CALL_TYPE__GET                      = 'get',
            CALL_TYPE__POST                     = 'post',
            CALL_TYPE__AUTH                     = 'auth',
            SIMPLE_SIG_PASSPHRASE               = 'say friend and pass'
    ;
    
    public const 
            CALL_STATE__STATEFULL               = 'statefull',
            CALL_STATE__STATELESS               = 'stateless',
            
            USER_AGENT__MEDSTARAPPS_LIVEACCESS  = 'MEDSTARAPPS_LiveAccess',
            USER_AGENT__SMIAGOL_LIVEACCESS      = 'SMIAGOL_LiveAccess',
            USER_AGENT__TALISMS01_LIVEACCESS    = 'TALISMS01_LiveAccess'
    ;

    /**
     * @var int
     */
    private int $request_time_out;
    /**
     * @var ?string $auth_code
     **/
    private ?string $auth_code  = null;
    /**
     * @var string $call_state
     **/
    private string $call_state = 'stateless'; //hardcoded until we get rid of the statefull
    /**
     * 
     * @var string $call_type
     */
    private string $call_type;
    /**
     * 
     * @var string $user_agent
     */
    private string $user_agent;
    /**
     * 
     * @var ?string $voucher
     */
    private ?string $voucher    = null;
    /**
     * 
     * @var \ZimLogger\Streams\aLogStream $l
     */
    private \ZimLogger\Streams\aLogStream $l;
    /**
     * 
     * @var bool $add_signature whether to add a simple encrypted passphrase to the request, this very very minor security precaution 
     */
    private bool $add_signature = false;

    static public function verifySignature(\stdClass $raw_response):bool{
        $Enc = new \SiTEL\Encryption\AES256CBC(self::getSignatureKey(),$raw_response->ivy);
        $pass_phrase = $Enc->decrypt($raw_response->friend);
        
        if(self::SIMPLE_SIG_PASSPHRASE != $pass_phrase){
            throw new \Exception('request is not properly signed');
        }
        
        return true;
    }
    
    /**
     *
     * @param \ZimLogger\Streams\aLogStream $Logger
     * @param string $user_agent
     * @param int $exceptional_timeout If we need a longer than normal ttl on the request.
     */
    public function __construct(\ZimLogger\Streams\aLogStream $Logger, string $user_agent = self::USER_AGENT__MEDSTARAPPS_LIVEACCESS,int $exceptional_timeout=5){
        $this->l            = $Logger;
        $this->user_agent   = $user_agent;
        $this->request_time_out = $exceptional_timeout;
    }
    
    /**
     * GET method wrapper to CALL
     * 
     * @param string $action
     * @param array<string, string> $params
     * @return \stdClass
     */
    public function get(string $action,array $params=[]):\stdClass{
        return $this->setCallType(self::CALL_TYPE__GET)->call($action,$params);
    }

    /**
     * AUTH method wrapper to CALL
     * 
     * @param string $action
     * @param array<string, string> $params
     * @return \stdClass
     */
    public function auth(string $action,array $params=[]):\stdClass{
        return $this->setCallType(self::CALL_TYPE__AUTH)->call($action,$params);
    }
    
    /**
     * 
     * POST method wrapper to CALL
     * 
     * @param string $action
     * @param array<string, string> $params
     * @return \stdClass
     */
    public function post(string $action,array $params=[]):\stdClass{
        return $this->setCallType(self::CALL_TYPE__POST)->call($action,$params);
    }
    

    /**
     * Make the actual call
     * @param string $action
     * @param array<string, string> $params
     * @return \stdClass
     */
    private function call(string $action,array $params=[]):\stdClass
    {
        $client_config  = [
            'verify'            => false,
            'allow_redirects'   => false,
            'timeout'           => $this->request_time_out
        ];
        
        //Voucher?
        if($this->voucher){
            $params['voucher'] = $this->voucher;
        }

        if($this->add_signature){
            $params = array_merge($params,$this->getSimpleSignature());
        }
        
        //target:
        $url = 'https://' . app_env()['paths']['lms_api_url'] . "/{$this->call_state}/{$this->call_type}/";
        
        // Data structure to pass along parameters and action in the body
        $body           = new \stdClass;
        $body->action   = $action;
        $body->params   = $params;
        
        $request_headers = [
            'header'    => [
                'User-Agent'    => $this->user_agent,
                'Content-type'  => 'application/json'
            ]
        ];
        
        $this->l->debug("Calling LMS [{$url}]");
        $this->l->debug($body);
        
        $Client   = new \GuzzleHttp\Client($client_config);
        $enc_body = json_encode($body);
        if(!$enc_body){
            \error('Failed encoding body for a Corwin request ' . print_r($body,true));
            throw new \Exception('Failed encoding body for a Corwin request');
        }
        $Request  = new \GuzzleHttp\Psr7\Request('post', $url, $request_headers,$enc_body);
        $Response = $Client->send($Request);
        $response_as_json = json_decode($Response->getBody()->getContents());
        if(!$response_as_json){
            throw new \Exception('Bad response from LMS [' . print_r($response_as_json,true) . ']');
        }
        $this->l->debug('RESPONSE IS:');
        $this->l->debug($response_as_json);
        
        return $response_as_json;
    }
    
    /**
     *  Set the call type for the client
     *  @param string $call_type
     *            [get | post | auth]
     *            
     * @return \SiTEL\Api\CorwinClient           
     */
    private function setCallType(string $call_type):\SiTEL\Api\CorwinClient{
        $this->call_type = $call_type;
        return $this;
    }
    
    /**
     *  Set the auth_code for the call function.  Leave empty to set as null
     *  @deprecated
     *  @param string $auth_code
     *            if statefull, u might need an authcode (this is the session identifier)
     */
    public function setAuthCode(string $auth_code = null):void{
        $this->auth_code = $auth_code;
    }
    
    /**
     *  Set the resource voucher code for a one time call. Leave empty to set to null
     *  @param string $voucher
     */
    public function setVoucher(string $voucher):\SiTEL\Api\CorwinClient{
        $this->voucher  = $voucher;
        return $this;
    }
    
    /**
     * Sets the simple signature to true
     * 
     * @return \SiTEL\Api\CorwinClient
     */
    public function addSimpleSignature():\SiTEL\Api\CorwinClient{
        $this->add_signature = true;
        return $this;
    }
    
    /**
     * Returns the array part of the signature to add to params
     * @return array<string, string>
     */
    public function getSimpleSignature():array{
        $Enc = new \SiTEL\Encryption\AES256CBC(self::getSignatureKey());
        return ['ivy'    => $Enc->iv(),
                'friend' => $Enc->encrypt('say friend and pass')
        ];
    }
    
    /**
     * 
     * @return string
     */
    static private function getSignatureKey():string{
        return base64_decode(\app_env()['federated_login']['jain farstrider']['key']);
    }
    
    /**
     *  Set the user_agent for the call function.  Leave empty to set as MEDSTARAPPS_LiveAccess
     * @param string $user_agent
     *            user_agent for headers
     */
    public function setUserAgent(string $user_agent = self::USER_AGENT__MEDSTARAPPS_LIVEACCESS):void{
        $this->user_agent = $user_agent;
    }
}
