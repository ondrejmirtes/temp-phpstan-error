<?php namespace TheKof;
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
