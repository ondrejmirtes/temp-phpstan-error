<?php namespace TheKof;
/**
 * @author Itay Moav
 * @Date Nov 17 - 2017
 */
class ThirdPartyWrappers_HTTPClient_ZendFW2 extends ThirdPartyWrappers_HTTPClient_a{
	
	/**
	 * I have it here for sake of documentation
	 * 
	 * @var \Zend\Http\Client
	 */
	protected $concrete_http_client = null;
	
	/**
	 * This is where the actual translation from DryRequest info to the actual client
	 * is happening.
	 *
	 * @param \TheKof\Util_DryRequest $DryRequest
	 * @return \TheKof\Util_RawResponse
	 */
	protected function execute_dry_request_internal(Util_DryRequest $DryRequest):Util_RawResponse{
	    SurveyMonkey::$L->debug("\n==================================================\nDOing " . $DryRequest->method() . ': ' . $DryRequest->url());
	    
		$this->concrete_http_client->setMethod($DryRequest->method());
		$this->concrete_http_client->setUri($DryRequest->url());
		$this->concrete_http_client->setHeaders($DryRequest->headers());
		
		switch($DryRequest->method()){
			case self::METHOD_GET:
				break;
				
			default:
				$body_encoded = json_encode($DryRequest->body());
				$this->concrete_http_client->setRawBody($body_encoded);
				break;
		}
		
		$res = $this->concrete_http_client->send();
		$Response = new Util_RawResponse;
		$Response->http_code 			= $res->getStatusCode();
		$Response->http_code_message	= $res->getReasonPhrase();
		$Response->headers				= $res->getHeaders()->toArray();
		$Response->body					= json_decode($res->getBody());
		return $Response;
	}
}