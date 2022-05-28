<?php namespace SiTEL\Api\Oberon;
/**
 * Client Wrapper for Aybara
 * @author Itay Moav
 */
abstract class AWS extends \SiTEL\Api\Oberon\ClientWrapper{
    /**
     * 
     * @var boolean
     */
    protected bool $use_aybara_as_proxy = true;
    
    /**
     * Seconds to try to connect to server.
     *
     * @var float
     */
    protected float $connection_timeout = 11;
    
    /**
     * 
     * @var array<string, mixed>
     */
    private        $config         = [];
    /**
     * 
     * @var string
     */
    private        $endpoint       = '';
    /**
     * 
     * @var string
     */
    private        $method_type    = '';
    /**
     * 
     * @var string
     */
    private        $url            = '';   
    
        
    /**
     * Constructor
     * Set up headers and url
     * @param string $endpoint
     */
    public function __construct(string $endpoint, string $method_type = \SiTEL\Api\Oberon\Request::METHOD__GET) {
        $this->config       = app_env()['external sources']['aws'];
        $this->method_type  = $method_type == \SiTEL\Api\Oberon\Request::METHOD__GET ? \SiTEL\Api\Oberon\Request::METHOD__GET : \SiTEL\Api\Oberon\Request::METHOD__POST;
        $this->endpoint     = strpos($endpoint, '/') === 0 ? $endpoint : "/{$endpoint}";
        $this->url          = "https://{$this->config['host']}{$this->endpoint}";
        
        parent::__construct($this->url, true, 2);
    }

    /**
     * @param array<mixed> $params
     * @return \SiTEL\Api\Oberon\Response
     */
    public function get(array $params = []) : \SiTEL\Api\Oberon\Response {
        $req = new \SiTEL\Api\Oberon\Request($this->base_url, \SiTEL\Api\Oberon\Request::METHOD__GET, $this->createHeaders($params));
        
        if ($params) {
            $req->body($params);
        }
        
        return $this->send($req);
    }

    /**
     * @param array<mixed> $data
     * @return \SiTEL\Api\Oberon\Response
     */
    public function post(array $data) : \SiTEL\Api\Oberon\Response {
        $req = new \SiTEL\Api\Oberon\Request($this->base_url, \SiTEL\Api\Oberon\Request::METHOD__POST, $this->createHeaders($data));
        $req->body($data);
        
        return $this->send($req);
    }
    
    /**
     * Create headers for authentication
     * 
     * @param array<mixed> $params
     * @return array<string>
     */
    private function createHeaders(array $params):array {
        $env                = $this->config;
        $method             = $this->method_type;
        $service            = 'execute-api';
        $host               = $env['host'];
        $region             = $env['region'];
        $request_parameters = $params && $method !== \SiTEL\Api\Oberon\Request::METHOD__GET ? json_encode($params) : '';
        
        $access_key = $env['key_id'];
        $secret_key = $env['access_key'];
        
        $date       = new \DateTime('UTC');
        $amzdate    = $date->format('Ymd\THis\Z');
        $datestamp  = $date->format('Ymd');
        
        $canonical_uri          = $this->endpoint;
        $canonical_querystring  = '';
        
        // set content type
        $content_type = 'application/json';
        
        // headers
        $canonical_headers  = "content-type:{$content_type}\nhost:{$host}\nx-amz-date:{$amzdate}\n";
        $signed_headers     = 'content-type;host;x-amz-date';
        
        $alg            = 'sha256';
        $payload        = $request_parameters;
        $payload_hash   = hash($alg, $payload);
        
        $algorithm          = 'AWS4-HMAC-SHA256';
        $canonical_request  = "{$method}\n{$canonical_uri}\n{$canonical_querystring}\n{$canonical_headers}\n{$signed_headers}\n{$payload_hash}";
        $credential_scope   = "{$datestamp}/{$region}/{$service}/aws4_request";
        $string_to_sign     = "{$algorithm}\n{$amzdate}\n{$credential_scope}\n" . hash($alg, $canonical_request);
        
        $kSecret    = 'AWS4' . $secret_key;
        $kDate      = hash_hmac( $alg, $datestamp, $kSecret, true );
        $kRegion    = hash_hmac( $alg, $region, $kDate, true );
        $kService   = hash_hmac( $alg, $service, $kRegion, true );
        $kSigning   = hash_hmac( $alg, 'aws4_request', $kService, true );
        $signature  = hash_hmac( $alg, $string_to_sign, $kSigning );
        
        $authorization_header = "{$algorithm} Credential={$access_key}/{$credential_scope}, SignedHeaders={$signed_headers}, Signature={$signature}";
        
        $headers = [
            'content-type'  => $content_type,
            'x-amz-date'    => $amzdate,
            'Authorization' => $authorization_header
        ];
        
        return $headers;
    }
    
    /**
     * Get data
     * @return array<int|string, mixed>
     */
    public static function getData() : array {
        $client = new static(static::endpoint_url_get());
        return static::convert_obj_to_array($client->get()->body);
    }
    
    /**
     * Post processed data
     * @param array $data
     * @return array<int|string, mixed>
     */
    public static function sendProcessedData(array $data) : array {
        $client = new static(static::endpoint_url_post(), \SiTEL\Api\Oberon\Request::METHOD__POST);
        return static::convert_obj_to_array($client->post($data));
    }
    
    /**
     * Requests always return stdClass object but we want array
     * Converts stdClass obj to array
     * @param array|\stdClass  $data_to_convert
     * @return array<int|string, mixed>
     */
    protected static function convert_obj_to_array($data_to_convert) {
        $converted_data = [];
        foreach($data_to_convert as $indx => $data) {
            if(!is_object($data) && !is_array($data)) {
                $converted_data[$indx] = $data;
            } else {
                $processing = (array) $data;
                $converted_data[$indx] = static::convert_obj_to_array($processing);
            }
        }
        
        return $converted_data;
    }
    
    /**
     * URL for get
     * @return string
     */
    abstract protected static function endpoint_url_get():string;
    
    /**
     * URL for post
     * @return string
     */
    abstract protected static function endpoint_url_post():string;
    
}