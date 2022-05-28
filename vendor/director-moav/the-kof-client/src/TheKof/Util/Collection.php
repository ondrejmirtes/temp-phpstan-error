<?php namespace TheKof;
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
