<?php namespace TheKof;
/**
 * Class is the "boss" of this entire system.
 * It provides the API to build and execute the queries to Survey monkey
 * 
 * @author Itay Moav
 * @Date 13-11-2017
 *
 */
class SurveyMonkey{

    const SURVEY_MONKEY_SERVICE_URL = 'https://api.surveymonkey.net/v3';
    
    /**
     * Configure values for this group of classes
     *      access_token string : get it from SurveyMonkey app settings page
     *
     * @var array
     */
    static protected $config = [];
    
    /**
     * Http client Wrapper to handle actual http request.
     * Make sure to configure that object ahead of sending it to this class
     * with the actual http client
     *
     * @var ThirdPartyWrappers_HTTPClient_a
     */
    static public $HttpClientWrapper = null;
    
    /**
     * Logger to use in TheKof code, by default it will be the ThirdPartyWrappers_Logger_EchoNative
     * @var ThirdPartyWrappers_Logger_a
     */
    static public $L = null;
    
    /**
     * Counts the number of SM requests per process.
     * If u want to carry this value between processes, find a shared storage solution (memory would be best).
     * 
     * @var integer
     */
    static public $requests_counter = 0;
	
	/**
	 * Init system and return a ready survey monkey client
	 * 
	 * @param array $config
	 * @param ThirdPartyWrappers_HTTPClient_a $HttpClientWrapper
	 * @return bool true for success
	 */
    static public function init(array $config,ThirdPartyWrappers_HTTPClient_a $HttpClientWrapper,ThirdPartyWrappers_Logger_a $Logger = null):bool{
        if(!$Logger){
            $Logger = new ThirdPartyWrappers_Logger_EchoNative;
        }
        self::$L = $Logger;
		self::megatherion_init($config, $HttpClientWrapper);//init the client
		return true;
	}
	
	/**
	 * Inits the client system
	 * The values entered here are gobal and immutable
	 *
	 * @param array $config
	 * @param ThirdPartyWrappers_HTTPClient_a $HttpClientWrapper
	 * @throws \InvalidArgumentException
	 */
	static public function megatherion_init(array $config,ThirdPartyWrappers_HTTPClient_a $HttpClientWrapper){
	    self::megatherion_validate_config_attributes($config);
	    self::$config = $config;
	    self::$HttpClientWrapper = $HttpClientWrapper;
	}
	
	/**
	 * Validates the $config array that has the necessary values
	 *
	 * @param array $config ['access_token']
	 *
	 * @throws \InvalidArgumentException
	 */
	static private function megatherion_validate_config_attributes(array $config):void{
	    if(!isset($config['access_token'])){
	        throw new \InvalidArgumentException('Missing access_token in $config');
	    }
	}
	
	    
	/**
	 * Initiate a surveys dry request
	 * 
	 * @param int $survey_id
	 * @return Client_Surveys
	 */
	static public function surveys(int $survey_id = 0):Client_Surveys{
		$dry_request = new Util_DryRequest(self::$config['access_token']);
		$dry_request->url(self::SURVEY_MONKEY_SERVICE_URL);// ($survey_id?"/{$survey_id}":''));
		$SurveyClient = new Client_Surveys($dry_request);
		$SurveyClient->set_id($survey_id);
		return $SurveyClient;
	}
	
	/**
	 * this is not a drill down, this is to get 
	 * a client for a known collector.
	 * This is the top method
	 * 
	 * @param int $collector_id
	 * @return Client_Collectors
	 */
	static public function collectors(int $collector_id):Client_Collectors{
		$dry_request = new Util_DryRequest(self::$config['access_token']);
		$dry_request->url(self::SURVEY_MONKEY_SERVICE_URL);// ($survey_id?"/{$survey_id}":''));
		$CollectorClient = new Client_Collectors($dry_request);
		$CollectorClient->set_id($collector_id);
		return $CollectorClient;
	}
}
