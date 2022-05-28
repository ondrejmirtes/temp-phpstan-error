<?php namespace SiTEL\Api\Oberon;
/**
 * A general rest request and response.
 * Provide shortcuts mainly and some debug handling
 *
 * @author Itay Moav
 *
 */
class ClientWrapper{
    /**
     * @var boolean If marked true, it will redirect the request to Aybara to the REST Proxy action
     */
    protected bool $use_aybara_as_proxy = false;
    
    /**
     * @var \GuzzleHttp\Client
     */
    protected \GuzzleHttp\Client $client;
    
    /**
     * Headers to always add to each request
     *
     * @var array<string, string>
     */
    protected array $forever_headers = [];
    
    /**
     * Seconds to try to connect to server.
     *
     * @var float
     */
    protected float $connection_timeout =  5.14;
    
    /**
     * @var bool in case of request time out, should I retry the request?
     */
    protected bool $should_retry;
    
    /**
     * @var int in case of request time out, how many times should I retry before giving up all hope and resign myself to higher powers of law and chaos.
     */
    protected int $no_of_tries;
    
    /**
     * @var int in case of request time out, how long should I wait until next try
     */
    protected $wait_for_in_seconds = 0;
    
    /**
     * This pair is comming from the environment ['external sources'][....]
     * @var string
     */
    protected string $key = '';
    /**
     *
     * @var string
     */
    protected string $secret = '';
    /**
     *
     * @var string
     */
    protected string $bearer = '';
    
    /**
     * @var string
     */
    protected string $base_url;
    
    /**
     * Setting this to true will obfuscate responses when http ret code is >299.
     * It will throw an Exception instead. Most APIs send error explanations in the
     * body of the response, so FALSE is much better most of the time.
     *
     * @var boolean
     */
    protected bool $http_errors = false;
    
    /**
     *
     * @param string $base_url
     * @param boolean $should_retry
     * @param int $no_of_retries
     * @param int $wait_for_in_seconds
     */
    public function __construct(string $base_url = '',$should_retry=false, $no_of_retries=5, $wait_for_in_seconds=3){
        $config = [
            'verify'            => true,
            'allow_redirects'   => false,
            'timeout'           => $wait_for_in_seconds,
            'base_uri'          => $base_url
        ];
        
        $this->client               = new \GuzzleHttp\Client($config);
        $this->no_of_tries          = $no_of_retries;
        $this->wait_for_in_seconds  = $wait_for_in_seconds;
        $this->base_url             = $base_url;
        $this->should_retry         = $should_retry;
    }
    
    /**
     *
     * @param \SiTEL\Api\Oberon\Request $request
     * @throws \Exception
     * @return \SiTEL\Api\Oberon\Response
     */
    public function send(\SiTEL\Api\Oberon\Request $request):\SiTEL\Api\Oberon\Response{
        if(!$this->should_retry){
            return $this->internal_call($request);
        }
        
        //try to send if we have more than one try
        $tries = 0;
        while(true){
            dbgn("Try sending request, iteration [{$tries}]");
            $tries++;
            try{
                return $this->internal_call($request);
                
            } catch (\GuzzleHttp\Exception\ConnectException $e){
                if($tries >= $this->no_of_tries){
                    throw $e;
                }
            }
            sleep($this->wait_for_in_seconds);
        }
    }
    
    /**
     * Package the request and send to Aybara (the proxy) which will unpack it and send to the real
     * destination.
     *
     * @param \SiTEL\Api\Oberon\Request $request
     * @throws \Exception
     * @return \SiTEL\Api\Oberon\Response
     */
    private function aybara_call(\SiTEL\Api\Oberon\Request $request):\SiTEL\Api\Oberon\Response{
        dbgr('AYBARA PROXI REQUEST',$request);
        
        $raw_data = \SiTEL\array_to_object([
            //'action'        => 'ProxyAwayPSR',
            'params'        => null
        ]);
        $raw_data->params = [
            'psr_request'               => base64_encode(serialize($request)),
            'config_connection_timeout' => $this->connection_timeout
        ];
        \dbgr("1. RAW DATA BEFORE ENCODE", $raw_data);
        $json_encode_raw_data = json_encode($raw_data);
        if (!$json_encode_raw_data){
            \error('1. json_encode failed on aybra proxy call ['. print_r($raw_data,true).']');
            throw new \Exception('json_encode failed on aybra proxy call');
        }
        
        $c = app_env()['external sources']['aybara'];
        $config = [
            'verify'            => true,
            'allow_redirects'   => false,
            'timeout'           => $this->connection_timeout,
            'base_uri'          => "{$c['endpoint_url']}/corwin/proxy/awaypsr7",
            'auth'              => [$c['key'],$c['secret']]
            ];
        
        $client  = new \GuzzleHttp\Client($config);
        $request = new \GuzzleHttp\Psr7\Request(
            'post',
            '',
            [
                'User-Agent'    => 'LMS_ProxyAybara',
                'Content-type'  => 'application/json'
            ],
            $json_encode_raw_data
        );
        $response_from_aybara_proxy = new \SiTEL\Api\Oberon\Response($client->send($request,['http_errors'=>$this->http_errors,'timeout' => $this->connection_timeout]));
        
        if (isset($response_from_aybara_proxy->body)){
            dbgr('FULL AYBARA RESPONSE',$response_from_aybara_proxy);
            $real_response = unserialize(base64_decode($response_from_aybara_proxy->body->params->response));
            return $real_response;
        }else {
            \error("Client wrapper is unable to get response. Error code: {$response_from_aybara_proxy->code}, CodePhrase: {$response_from_aybara_proxy->codePhrase}  happend.".print_r($response_from_aybara_proxy,true));
            throw new \Exception('Unable to get real response from aybara proxy');
        }
        
    }
    
    /**
     * I need this logic to do all real calls from one place, and use Aybara Proxy when needed
     * I instantiate a seprate Client here for Proxy and put in the right details,
     * I also parse here the return so as to be able to enable the calling app to process this
     * as if there was no proxy in the middle
     *
     * @return \SiTEL\Api\Oberon\Response
     */
    final protected function internal_call(\SiTEL\Api\Oberon\Request $request):\SiTEL\Api\Oberon\Response{
        //add auth headers if they exists
        if($this->key && $this->secret){
            $request->setBasicAuth($this->key,$this->secret);
        } elseif($this->bearer){
            $request->setJWTAuth($this->bearer);
        }
        
        //forever headers. Notice request headers will override the defaults
        if($this->forever_headers){
            $headers = $request->headers();
            $request->headers(array_merge($this->forever_headers,$headers));
        }
        
        if($this->use_aybara_as_proxy){
            return $this->aybara_call($request);
        }else{
            dbgr('internal call with ',$request);
            $body = '';
            if($request->body()){
                $body = json_encode($request->body());
                if(!$body){
                    $body = '';
                    \error('2. json_encode body failed on ['. print_r($body,true).']');
                }
            }
            
            $request_guzzle = new \GuzzleHttp\Psr7\Request(
                $request->method(),
                $request->url(),
                $request->headers(),
                $body
            );
            return new \SiTEL\Api\Oberon\Response($this->client->send($request_guzzle,['http_errors'=>$this->http_errors,'timeout' => $this->connection_timeout]));
        }
    }
}
