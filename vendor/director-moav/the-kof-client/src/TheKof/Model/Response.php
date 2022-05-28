<?php namespace TheKof;
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