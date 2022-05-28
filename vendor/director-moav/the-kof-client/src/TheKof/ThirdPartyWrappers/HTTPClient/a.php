<?php namespace TheKof;
/**
 * @author Itay Moav
 * @Date Nov 17 - 2017
 */
abstract class ThirdPartyWrappers_HTTPClient_a{
	/**
	 * HTTP method types
	 * 
	 * @var string $METHOD_GET
	 * @var string $METHOD_POST
	 * @var string $METHOD_PUT
	 * @var string $METHOD_DELETE
	 * @var string $METHOD_OPTIONS
	 * @var string $METHOD_HEAD
	 * @var string $METHOD_PATCH
	 */
	const METHOD_GET	 = 'GET',
		  METHOD_POST	 = 'POST',
		  METHOD_PUT	 = 'PUT',
		  METHOD_DELETE  = 'DELETE',
		  METHOD_OPTIONS = 'OPTIONS',
		  METHOD_HEAD	 = 'HEAD',
		  METHOD_PATCH   = 'PATCH'
	;
	
	/**
	 * @var mixed the actual http client
	 */
	protected $concrete_http_client = null;

	/**
	 * Just send in the instantiated http client
	 * 
	 * @param mixed $concrete_http_client
	 */
	public function __construct($concrete_http_client){
		$this->concrete_http_client = $concrete_http_client;
	}
	
	/**
	 * Calls the actual execution functions. Captures errors, do pre  and post actions.
	 * 
	 * @param Util_DryRequest $DryRequest
	 * @return Util_RawResponse
	 */
	final public function execute_dry_request(Util_DryRequest $DryRequest):Util_RawResponse{
	    SurveyMonkey::$requests_counter++;
// 	    dbgn('REQUEST [' . SurveyMonkey::$requests_counter . ']');
	    return $this->execute_dry_request_internal($DryRequest);
	}
	
	/**
	 * This is where the actual translation from DryRequest info to the actual client
	 * is happening.
	 * 
	 * @param \TheKof\Util_DryRequest $DryRequest
	 * TODO what do I return here? a dry response?
	 */
	abstract protected function execute_dry_request_internal(Util_DryRequest $DryRequest):Util_RawResponse;
}

