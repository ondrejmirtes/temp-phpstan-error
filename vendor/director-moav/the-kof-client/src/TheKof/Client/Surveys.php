<?php namespace TheKof;
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
