<?php namespace TheKof;
/**
 * Data structure for holding a request details.
 * Usefull for mocking up tests, overriding the default use
 * of Zend FW for the HTTP request.
 * Very similar to the TalisMS Request object, but is to be used here internaly, no depndencies.
 * 
 * @author Itay Moav
 * @date   14/11/2017
 *
 */
class Util_DryRequest{
	
	private $url='',$url_params=[],$url_query_parts=[],$method='',$body=null,$headers=[];
	
	public function __construct($access_token){
		$this->headers([
				'Authorization' => "bearer {$access_token}",
				'Content-type'  => 'application/json'
		]);
	}
	
	/**
	 * @return string json encoded of this entire object. for debug purposes.
	 */
	public function __toString(){
		$res = new \stdClass;
		$res->url     = $this->url;
		$res->method  = $this->method;
		$res->headers = $this->headers;
		$res->body    = $this->body;
		return json_encode($res);
	}
	
	/**
	 * Sets the url to input value, resets the url params and query parts
	 * 
	 * @param string $url
	 * @return string url
	 */
	public function url(string $url=''):string{
	    if($url){
	        $this->url = $url;
	        $this->url_params=[];
	        $this->url_query_parts = [];
	    }
	    $sep = '?';
	    $url_params = '';
	    if($this->url_params){
	        foreach($this->url_params as $k => $v){
	            if($v){
    	            $url_params .= "{$sep}{$k}={$v}";
    	            $sep='&';
	            }
	        }
	    }
	    
	    if($this->url_query_parts){
	        foreach($this->url_query_parts as $v){
	            if($v){
	                $url_params .= "{$sep}{$v}";
	                $sep='&';
	            }
	        }
	    }
	    
	    return $this->url . $url_params;
	}
	
	/**
	 * Concats to the current url
	 * 
	 * @param string $concate_url
	 * @return string the modified url
	 */
	public function url_add(string $concate_url):string{
		return $this->url    .= $concate_url;
	}
	
	
	public function method(string $method=''):string{
		return $this->method  = $method?:$this->method;
	}
	
	public function body($body=null){
		return $this->body    = $body?:$this->body;
	}
	
	public function headers(array $headers=[]):array{
		return $this->headers = $headers?:$this->headers;
	}
	
	/**
	 * Url params
	 * 
	 * @param string $k param Name
	 * @param mixed $v param Value
	 * @return Util_DryRequest
	 */
	public function set_url_param(string $k,$v):Util_DryRequest{
	    if($v){
    	    $this->url_params[$k] = $v;
	    }
	    return $this;
	}
	
	/**
	 * A query is a k=v string which has to be recognized by SM.
	 * 
	 * @param Client_QueryParts_i|string $query
	 * @return Util_DryRequest
	 */
	public function add_url_query_parts($query):Util_DryRequest{
	   $this->url_query_parts[] = $query;
	   return $this;
	}
}
