<?php namespace TheKof;
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