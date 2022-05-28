<?php namespace SiTEL\Api\Oberon;

/**
 *
 * @author itay
 *
 */
class Request
{
    CONST 
        METHOD__POST    = 'POST',
        METHOD__GET     = 'GET',
        METHOD__PUT     = 'PUT',
        METHOD__PATCH   = 'PATCH',
        METHOD__DELETE  = 'DELETE'
    ;

    /**
     * 
     * @var string $url
     */
    private string $url;
    /**
     *  @var string $method
     */
    private string $method;
    
    /**
     *  @var ?\stdClass $body
     */
    private ?\stdClass $body;
    /**
     *  @var array<string,mixed>
     */
    private array $headers;

    /**
     * 
     * @param string $url
     * @param string $method
     * @param array<string,mixed> $headers   
     * @param ?\stdClass $body
     */
    public function __construct(string $url,string $method,array $headers,?\stdClass $body=null)
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     *
     * @return string json encoded of this entire object. for debug purposes.
     */
    public function __toString()
    {
        $res = new \stdClass();
        $res->url = $this->url;
        $res->method = $this->method;
        $res->headers = $this->headers;
        $res->body = $this->body;
        $encoded = json_encode($res);
        if(!$encoded){
            $encoded='';
            \error('Failed json encoding Response ' . print_r($res,true));
        }
        return $encoded;
    }
    
    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public function setBasicAuth(string $username,string $password):string{
        $encoded =  base64_encode("{$username}:{$password}");
        return $this->headers['Authorization'] = "basic {$encoded}";
    }
    
    /**
     * 
     * @param string $jwt
     * @return string
     */
    public function setJWTAuth(string $jwt):string{
        return $this->headers['Authorization'] = "bearer {$jwt}";
    }
    
    
    /**
     * Sets the url to input value, resets the url params and query parts
     *
     * @param string $url
     * @return string url
     */
    public function url(string $url = ''): string
    {
        return $this->url = $url ?: $this->url;    
    }

    /**
     * Concats to the current url
     *
     * @param string $concate_url
     * @return string the modified url
     */
    public function url_add(string $concate_url): string
    {
        return $this->url .= $concate_url;
    }
    /**
     * @param string $method
     * @return string
     */
    
    public function method(string $method = ''): string
    {
        return $this->method = $method ?: $this->method;
    }
    /**
     * @param mixed $body
     * @return mixed
     */
    public function body($body = null)
    {
        return $this->body = $body ?: $this->body;
    }
    /**
     * @param array<string,mixed> $headers
     * @return array<string,mixed>
     */
    
    public function headers(array $headers = []): array
    {
        return $this->headers = $headers ?: $this->headers;
    }
}
