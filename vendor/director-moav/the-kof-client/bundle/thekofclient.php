<?php namespace TheKof;


/**
 * Collectors client
 * 
 * @author Itay Moav
 * @date 17-11-2017
 */
class Client_Collectors extends Client_a{
	protected function add_url_part():void{
		$this->current_dry_request->url_add('/collectors');
	}
	
	protected function translate_to_model(\stdClass $single_item):Model_a{
		return new Model_Collector($single_item);
	}
}


/**
 * Surveys client
 * 
 * @author Itay Moav
 * @date 17-11-2017
 */
class Client_Surveys extends Client_a{
	protected function add_url_part():void{
		$this->current_dry_request->url_add('/surveys');
	}
	
	/**
	 * Drills into the current survey(s) collectors
	 * Calling the collector client REQUIRES you to send a survey id
	 * 
	 * @param int $collector_id
	 * @return Client_Collectors
	 */
	public function collectors(int $collector_id=0):Client_Collectors{
		if(!$this->asset_id_received){
			throw new \LogicException('Missing survey id when drilldown into collectors');
		}
		//survey is a major object -> I reset the requests
		$CollectorsClient = new Client_Collectors($this->current_dry_request);
		$CollectorsClient->set_id($collector_id);
		return $CollectorsClient;
	}
	
	/**
	 * Drills into the current survey(s) responses
	 * Calling the responses client REQUIRES you to send a survey id
	 *
	 * @param int $response_id
	 * @return Client_Responses
	 */
	public function responses(int $response_id=0):Client_Responses{
	    if(!$this->asset_id_received){
	        throw new \LogicException('Missing survey id when drilldown into responses');
	    }
	    //survey is a major object -> I reset the requests
	    $ResponsesClient = new Client_Responses($this->current_dry_request);
	    $ResponsesClient->set_id($response_id);
	    return $ResponsesClient;
	}

	/**
	 * Load the survey fully with extrended details (pages and questions)
	 * 
	 * @throws \LogicException
	 * @return Client_Surveys current obj
	 */
	public function details():Client_Surveys{
	    if(!$this->asset_id_received){
	        throw new \LogicException('Missing survey id when drilldown into collectors');
	    }
	    $this->current_dry_request->url_add('/details');
	    return $this;
	}

	/**
	 * Sends the data of a single item to the right model class
	 * 
	 * {@inheritDoc}
	 * @see \TheKof\Client_a::translate_to_model()
	 */
	protected function translate_to_model(\stdClass $single_item):Model_a{
		return new Model_Survey($single_item);
	}
}


/**
 * base abstract for each asset client and for the main client
 * 
 * @author Itay Moav
 * @date 17-11-2017
 */
abstract class Client_a{
	/**
	 * Client builds requests, according to called methods and params.
	 * That request is then sent to method or method_dry to be executed.
	 * This variable is where I store the current request the client is
	 * working on.
	 *
	 * @var Util_DryRequest
	 */
	protected $current_dry_request = null;
	
	/**
	 * Some drill down elements (like collectors)
	 * requires the existance of the parent elemend id.
	 * This var is tracking this.
	 * It is being set in the set_id() method
	 * 
	 * @var bool
	 */
	protected $asset_id_received   = false;
	
	/**
	 * @param Util_DryRequest $current_dry_request bubbled from the previous client
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function __construct(Util_DryRequest $current_dry_request = null){
		$this->current_dry_request = $current_dry_request;
		$this->add_url_part();//for each asset, adds the API point for it
	}
	
	/**
	 * Adds paging (if values entered and return a Dry reques
	 * with the needed info to construct an http request
	 * (You could technically create a wrapper on top of this method
	 *  to generate curl, Zend CLient or any other way to do the actual http).
	 *
	 * @param int $page if zero, parameter will be ommited and SM defaults will be used
	 * @param int $per_page if zero, parameter will be ommited and SM defaults will be used
	 *
	 * @return Util_DryRequest
	 */
	public function get_dry(int $page=0,int $per_page=0,?Client_QueryParts_i $query_part=null):Util_DryRequest{
		$this->current_dry_request->method(ThirdPartyWrappers_HTTPClient_a::METHOD_GET);
		$this->current_dry_request->set_url_param('page',$page);
		$this->current_dry_request->set_url_param('per_page',$per_page);
		$query_part?$this->current_dry_request->add_url_query_parts($query_part):'I do nothing, Will listen to Therion, good band.';
		return $this->current_dry_request;
	}
	
	/**
	 * Performs the actual GET http, uses dry request to generate the values
	 * for the http request
	 *
	 * @param int $page
	 * @param int $per_page
	 * 
	 * @return Util_Collection
	 */
	public function get(int $page=0,int $per_page=0,?Client_QueryParts_i $query_part=null):Util_Collection{
	    $this->get_dry($page,$per_page,$query_part);
	    return $this->build_asset(SurveyMonkey::$HttpClientWrapper->execute_dry_request($this->current_dry_request));
	}
	
	/**
	 * If u expect only one item (you set the item id), or u just need the first item in
	 * a collection, use this method, which will return the Model object for the specific item you are looking for.
	 * It will also trigger the full data load on this object (TODO verify it is needed)
	 * 
	 * @return ?Model_a returns null if no result available
	 */
	public function get_one():?Model_a{
		return $this->get()->current();
	}
	
	/**
	 * POST dry will generate the request object, but wont post it.
	 * 
	 * @param Model_a $model
	 * @return Util_DryRequest
	 */
	public function post_dry(Model_a $model):Util_DryRequest{
		$this->current_dry_request->method(ThirdPartyWrappers_HTTPClient_a::METHOD_POST);
		$this->current_dry_request->body($model->get_raw_data());
		return $this->current_dry_request;
		
	}
	
	/**
	 * Takes the input model, creates in SM
	 * and populate the rest of the values in it from the response.
	 * 
	 * @param Model_a $model
	 * @return Model_a
	 */
	public function post(Model_a $model):Model_a{
		$this->post_dry($model);
		$raw_response = SurveyMonkey::$HttpClientWrapper->execute_dry_request($this->current_dry_request);
		return $model->change_state($raw_response->body);
	}
	
	/**
	 * Alias for POST
	 *
	 * @param Model_a $model
	 * @return Model_a
	 */
	public function create(Model_a $model):Model_a{
	    return $this->post($model);
	}
	
	/**
	 * Patch is updating the current element.
	 * I will use the entire existing raw_data to update.
	 * This is not a single field selective update.
	 * 
	 * @param Model_a $model
	 * @return Util_DryRequest
	 */
	public function patch_dry(\stdClass $sub_structure):Util_DryRequest{
	    $this->current_dry_request->method(ThirdPartyWrappers_HTTPClient_a::METHOD_PATCH);
	    $this->current_dry_request->body($sub_structure);
	    return $this->current_dry_request;
	}
	
	/**
	 * Takes the current model, Use it to update current element in SM.
	 *
	 * @param model_a $model of the element we modify
	 * @param \stdClass $sub_structure Just the part you wish to update encapsulated in a stdClass - see example update_one_survey_add_custome_variable.php
	 * @return Model_a
	 */
	public function patch(Model_a $model,\stdClass $sub_structure):Model_a{
	    $this->patch_dry($sub_structure);
	    $raw_response = SurveyMonkey::$HttpClientWrapper->execute_dry_request($this->current_dry_request);
	    return $model->change_state($raw_response->body);
	}
	
	/**
	 * Alias to patch()
	 * 
	 * @param Model_a $model
	 * @return Model_a
	 */
	public function update(Model_a $model):Model_a{
	    return $this->patch($model);
	}
	
	
	/**
	 * If requesting a specific id, add it to the url
	 * @param integer $asset_id
	 * @return Client_a
	 */
	public function set_id(int $asset_id):Client_a{
		if($asset_id || $this->asset_id_received){
			$this->current_dry_request->url_add("/{$asset_id}");
			$this->asset_id_received = true;
		}
		return $this;
	}
	
	/**
	 * adds query params
	 * 
	 * title=xxxx This also works with partial names, will return all matching
	 * 
	 * @param Client_QueryParts_i $query_part
	 * @return Client_a
	 */
	public function query(Client_QueryParts_i $query_part):Client_a{
	    $this->current_dry_request->add_url_query_parts($query_part);
		return $this;
	}
	
	/**
	 * adds query string, free form
	 *
	 *
	 * @param string $query_string
	 * @return Client_a
	 */
	public function query_freeform(string $query_string):Client_a{
	    $this->current_dry_request->add_url_query_parts($query_string);
	    return $this;
	}
	    
	/**
	 * THIS IS ALWAYS TO LOAD THE CURRENT ITEM, ONLY ONE!
	 * 
	 * Overrides any previously entered URL with the entered one.
	 * You can also use that directly, comes handy especially when u use the hrefs 
	 * in the reponse
	 * 
	 * @param string $href
	 * @return Client_a
	 */
	public function set_href(string $href):Client_a{
		$this->current_dry_request->url($href);
		return $this;
	}
	
	/**
	 * Takes the raw response and sends the appropriate translator
	 * to the collection object.
	 * The translation to the right model is done LAZY
	 * 
	 * @param Util_RawResponse $RawResponse
	 * @return Util_Collection
	 */
	protected function build_asset(Util_RawResponse $RawResponse):Util_Collection{
		$that = $this;
		$translation_func = function(\stdClass $single_item) use ($that){
			return $that->translate_to_model($single_item);
		};
		return new Util_Collection($RawResponse,$translation_func);
	}

	abstract protected function add_url_part():void;
	
	/**
	 * For each item/asset type there is one translator from stdClass object to 
	 * a specific Model object 
	 *  
	 * @param \stdClass $single_item
	 * @param Client_a $client used to fully load the model if there is a need. If the model is fully loaded, it will be discarded.
	 * 
	 * @return Model_a
	 */
	abstract protected function translate_to_model(\stdClass $single_item):Model_a;
}


/**
 * Help implementing the sort by for the query
 * 
 * @author itay
 */
class Client_QueryParts_SortBy implements Client_QueryParts_i{
    const QUERY_SORT_ORDER__ASC     = 'ASC',
          QUERY_SORT_ORDER__DESC    = 'DESC'
    ;              
    
    private $sort_by    = '',
            $sort_order = ''
    ;
    
    /**
     * @param string $order_by
     * @param string $sort_order
     */
    public function __construct(string $sort_by,string $sort_order){
        $this->sort_by    = $sort_by;
        $this->sort_order = $sort_order;
    }
    
    public function __toString(){
        return "sort_by={$this->sort_by}&sort_order={$this->sort_order}";
    }
}



/**
 * @author itay
 */
interface Client_QueryParts_i{
    public function __toString();
}


/**
 * Collectors client
 * 
 * @author Itay Moav
 * @date 17-11-2017
 */
class Client_Responses extends Client_a{
	protected function add_url_part():void{
		$this->current_dry_request->url_add('/responses/bulk');
	}
	
	protected function translate_to_model(\stdClass $single_item):Model_a{
		return new Model_Response($single_item);
	}
}


class Model_Collector extends Model_a{
	const REDIRECT_TYPE__URL	= 'url',
		  REDIRECT_TYPE__CLOSE  = 'close',
		  REDIRECT_TYPE__LOOP   = 'loop',
		  		
		  TYPE__WEBLINK			= 'weblink',
		  TYPE__EMAIL			= 'email' 
	;
	
	/**
	 * Returns a client where the current 
	 * item is the top of the drill down.
	 * 
	 * @return Client_Collectors
	 */
	protected function get_client():Client_Collectors{
		return SurveyMonkey::collectors($this->item_data->id);
	}
	
	protected function set_if_fully_loaded(){
		$this->is_fully_loaded = isset($this->item_data->id) && isset($this->item_data->date_created);
	}
	
	/**
	 * The url you redirect user to take survey
	 * 
	 * @return string
	 */
	public function url():string{
		$this->fully_load();
		return $this->item_data->url;
	}
	
	/**
	 * collector name
	 * @return string
	 */
	public function name():string{
	    return $this->item_data->name;
	}
	
	/**
	 * alias for name()
	 * @return string
	 */
	public function title():string{
	    return $this->name();
	}
	
}

/**
 * This model object represents ONE SURVEY
 * It might not be fully loaded, notice the [details] method
 * 
 * @author itay
 *
 */
class Model_Survey extends Model_a{

    /**
     * Returns a client where the current 
	 * item is the top of the drill down.
	 * 
     * @return Client_Surveys
     */
	protected function get_client():Client_Surveys{
		return SurveyMonkey::surveys($this->item_data->id);
	}
	
	protected function set_if_fully_loaded(){
		$this->is_fully_loaded = isset($this->item_data->id) && isset($this->item_data->response_count);
	}
	
	/**
	 * Get the collectors client for the current survey
	 * collectors
	 * 
	 * @param int $collector_id
	 * @return Client_Collectors
	 */
	public function collectors(int $collector_id=0):Client_Collectors{
	    return $this->get_client()->collectors($collector_id);
	}
	
	/**
	 * get the drill down responses client
	 * 
	 * @param int $response_id
	 * @return Client_Responses
	 */
	public function responses(int $response_id=0):Client_Responses{
	    return $this->get_client()->responses($response_id);
	}
	
	/**
	 * Load the current survey details, BEAWARE - this is another request!
	 * 
	 * @return Model_Survey
	 */
	public function details():Model_Survey{
	    if(!isset($this->item_data->pages)){
	        $this->item_data = $this->get_client()->details()->get_one()->item_data;
	        $this->set_if_fully_loaded();
	    }
	    return $this;
	}
	
	/**
	 * @return string
	 */
	public function title():string{
	    return $this->get_raw_data()->title;
	}
	
	/**
	 * The preview link for this survey
	 * 
	 * @return string
	 */
	public function preview():string{
	    return $this->details()->get_raw_data()->preview;
	}
	
	/**
	 * 
	 * @return \stdClass
	 */
	public function custom_variables(?\stdClass $custome_variables=null):\stdClass{
	    if($custome_variables){
	        $this->item_data->custom_variables = $custome_variables;
	    }
	    return $this->item_data->custom_variables;
	}

	/**
	 * return array of the pages
	 * carefull, this is by ref
	 * 
	 * @return array
	 */
	public function pages():array{
	    return $this->details()->get_raw_data()->pages;
	}
	
	/**
	 * Returns all questions. this is a Generator
	 */
	public function all_questions(){
	    $pages = $this->details()->get_raw_data()->pages;
	    foreach($pages as $page){
	        foreach($page->questions as $question){
	            yield $question;
	        }
	    }
	}
	
	/**
	 * Loads the questions and cache them in the object
	 * indexed by their id, and return the array.
	 * The caching will happen only once.
	 * 
	 * @return array
	 */
	public function cached_questions():array{
	    static $cached_questions = [];
	    if(!$cached_questions){
	        foreach($this->all_questions() as $question){
	            //SurveyMonkey::$L->debug('RAW QUESTION',$question);
	            $cached_questions[$question->id] = $question;
	        }
	    }
	    
	    return $cached_questions;
	}
}

abstract class Model_a{

	/**
	 * When querying a collection (as opposed to one item by id) the result returns
	 * the minimum needed fields.
	 * To get the full item info, another request has to be done (with the full info url).
	 * @var bool
	 */
	protected $is_fully_loaded = false;
	
	/**
	 * The original data object from SM
	 * @var \stdClass
	 */
	protected $item_data;
	
	/**
	 * @param \stdClass $single_item Pre loaded data. If u create a new object, this can be null.
	 */
	public function __construct(\stdClass $single_item = null){
		$this->item_data = $single_item??new \stdClass;
		$this->set_if_fully_loaded();
	}
	
	/**
	 * Will fully load the item from SM and replace the existing item_data with the 
	 * return result
	 * 
	 * @return Model_a
	 */
	public function fully_load():Model_a{
		if(!$this->is_fully_loaded){
			$this->item_data = $this->get_client()->get_one()->item_data;
			$this->set_if_fully_loaded();
		}
		return $this;
	}
	
	/**
	 * CAREFULL - THIS IS read/write access, objects are transfered by REF.
	 * 
	 * @return \stdClass
	 */
	public function get_raw_data():\stdClass{
		return $this->item_data;
	}
	
	/**
	 * When sending a model to a Client to create/update on SM
	 * The response is the updated data. I will refresh the Model
	 * with the new data.
	 * 
	 * @param \stdClass $raw_data
	 * @return Model_a
	 */
	public function change_state(\stdClass $raw_data):Model_a{
		$this->item_data       = $raw_data;
		$this->is_fully_loaded = false;
		$this->set_if_fully_loaded();
		return $this;
	}
	
	/**
	 * updates the element in survey monkey
	 * 
	 * @param \stdClass $sub_structure is the part you want to modify. The members of this element correspond to the memebers in the raw_data object
	 * @return Model_a this
	 */
	public function patch(\stdClass $sub_structure):Model_a{
	    $this->get_client()->patch($this,$sub_structure);
	    return $this;
	}

	/**
	 * get method for item id
	 * 
	 * @return integer
	 */
	public function id():int{
		return $this->item_data->id*1;
	}
	
	/**
	 * Sets the $is_fully_loaded flag according to the info found in item_data
	 */
	abstract protected function set_if_fully_loaded();
}

/**
 * This model object represents one user ALL RESPONSES for ONE SURVEY
 * 
 * @author itay
 *
 */
class Model_Response extends Model_a{
    
	protected function set_if_fully_loaded(){
	    $this->is_fully_loaded = true;//It has only a full view mode
	}
	
	/**
	 * Adds the question information to the Response raw data in the right place.
	 * 
	 * @param Model_Survey $SurveyModel
	 * @return array
	 */
	public function combine_responses_with_question(Model_Survey $SurveyModel):Model_Response{
	   $survey_questions = $SurveyModel->cached_questions();
	   $pages = $this->get_raw_data()->pages;
	   foreach($pages as $page){
	       foreach($page->questions as $question){
	           $question->question_full = $survey_questions[$question->id];
	       }
	   }
	   return $this;
	}
}

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


/**
 * Data structure for holding a unified response object
 * from which ever http client u wish to use.
 * No fancy stuff, plain and simple.
 * 
 * @author Itay Moav
 * @date   17/11/2017
 *
 */
class Util_RawResponse{
	public $http_code,
		   $http_code_message,
		   $headers,
		   $body
	;
	
}


/**
 * Takes a raw resonse with a translatore and translates 
 * each element in raw response to it's model. 
 * Provide utilities to iterate over the collection of item
 * and to fetch the next/previous page
 * 
 * @author Itay Moav
 * @date   20-11-2017
 */
class Util_Collection implements \Iterator,\Countable{
	/**
	 * Used to take the raw response and translate to the appropriate 
	 * model object
	 * 
	 * @var callable
	 */
	private $translation_func;
	
	/**
	 * Array of the data items fetched by the request
	 * 
	 * @var array
	 */
	private $data_collection = [];
	
	private $page = 1;
	
	private $page_size = 50;
	
	/**
	 * Total entries/items 
	 * 
	 * @var integer
	 */
	private $total_entries_in_query = 1;
	
	/**
	 * Url for the next page for this set
	 * @var string
	 */
	private $link_next = '';
	
	/**
	 * Url for the previous page, before this page
	 * @var string
	 */
	private $link_previous = '';
	
	public function __construct(Util_RawResponse $RawResponse,callable $translation_func){
		$this->translation_func = $translation_func;
		$this->parse_raw_response($RawResponse);
	}
	
	/**
	 * Disects the response into the relevant 
	 * members.
	 * 
	 * @param Util_RawResponse $RawResponse
	 */
	private function parse_raw_response(Util_RawResponse $RawResponse):void{
		$this->error_handle($RawResponse->http_code,$RawResponse->http_code_message);
		//NOTICE! if the query fetches only one result, then the response wont have [data].
		//It will have ONLY the one, fully loaded, object
		if(isset($RawResponse->body->id) && $RawResponse->body->id){//one full object was returned
			$this->data_collection 			= [$RawResponse->body];
			$this->total_entries_in_query 	= 1;
			$this->page_size 				= 1;
			$this->page 					= 1;
			$this->link_previous			= null;
			$this->link_next				= null;
		} else { //a real collection
			$this->data_collection 			= $RawResponse->body->data;
			$this->total_entries_in_query 	= $RawResponse->body->total;
			$this->page_size 				= $RawResponse->body->per_page;
			$this->page 					= $RawResponse->body->page;
			$this->link_previous			= $RawResponse->body->links->prev??null;//at the edges u can still get null here 
			$this->link_next				= $RawResponse->body->links->next??null;//at the edges u can still get null here
			if($this->total_entries_in_query === 0){
				$this->translation_func = function($nothing){return null;};
			}
		}
	}

	public function count(){
		return $this->total_entries_in_query; //CHECK THIS IS NOT THE GENERAL NUMBER FOR ALL PAGES
	}
	
	public function current(){
		$func = $this->translation_func;
		return $func(current($this->data_collection));
	}
	
	public function next(){
		return next($this->data_collection);
	}
	
	public function key(){
		return key($this->data_collection);
	}
	
	public function valid(){
		return current($this->data_collection);
	}
	
	public function rewind(){
		reset($this->data_collection);
	}
	
	/**
	 * TODO remove this to proper place!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!11111111111111111111111111111111111111111111111111111111111111111111111111111
	 * 
	 * @param int $http_code
	 * @param string $http_message
	 * @throws \RuntimeException
	 */
	private function error_handle(int $http_code,string $http_message):void{
		switch($http_code){
			case 200:
				break;
			
			default:
				throw new \RuntimeException($http_message,$http_code);
		}
	}
	
	/**
	 * Returns the link for the next set in the current query
	 * @return string|NULL
	 */
	public function next_link():?string{
	    return $this->link_next;
	}
	
	public function page(){
	    return $this->page;
	}
	
	public function page_size(){
	    return $this->page_size;
	}
}


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
	    SurveyMonkey::$L->debug("
==================================================
DOing " . $DryRequest->method() . ': ' . $DryRequest->url());
	    
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

/**
 * There are different loggers out there, you might have your own.
 * You can use your own logger with TheKof, just create
 * and adapter between your logger and TheKof and set it up in init
 * 
 * If your logger is a bunch of functions, you will need to wrap them in a class or array
 * of functions before sending them to the adapter
 * 
 * This logger supports five levels of log output
 * - debug
 * - info
 * - warning
 * - error
 * - fatal
 * 
 * Check Examples
 * 
 * @author Itay Moav
 * @Date 12-02-2018
 */
abstract class ThirdPartyWrappers_Logger_a{
	
	/**
	 * @var mixed the actual Logger class
	 */
	protected $concrete_logger_class = null;

	/**
	 * Just send in the instantiated with logger
	 * 
	 * @param mixed $concrete_logger_class
	 */
	public function __construct($concrete_logger_class = null){
	    $this->concrete_logger_class = $concrete_logger_class;
	}
	
	/****************************************************************************************************
	 * The following methods are what you have to implement.
	 * They will all get a message (string) and an optional data structure I will print_r($data,true)
	 * What you do with those, is yours to decide.
	 ****************************************************************************************************/
	
	abstract public function debug(string $msg,$data_structure=null):void;
	
	abstract public function info(string $msg,$data_structure=null):void;
	
	abstract public function warning(string $msg,$data_structure=null):void;
	
	abstract public function error(string $msg,$data_structure=null):void;
	
	abstract public function fatal(string $msg,$data_structure=null):void;
}



/**
 * The default logger - just echoes to stdio
 * 
 * @author Itay Moav
 * @Date 12-02-2018
 */
class ThirdPartyWrappers_Logger_EchoNative extends ThirdPartyWrappers_Logger_a{
	
	/****************************************************************************************************
	 * The following methods are what you have to implement.
	 * They will all get a message (string) and an optional data structure I will print_r($data,true)
	 * What you do with those, is yours to decide.
	 ****************************************************************************************************/
	
	public function debug(string $msg,$data_structure=null):void{
	    echo $msg . "
";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function info(string $msg,$data_structure=null):void{
	    echo $msg . "
";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function warning(string $msg,$data_structure=null):void{
	    echo $msg . "
";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function error(string $msg,$data_structure=null):void{
	    echo $msg . "
";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function fatal(string $msg,$data_structure=null):void{
	    echo $msg . "
";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
}



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
