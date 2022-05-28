<?php
abstract class ViewSnipets_BLElems_Course{
	
	static private $course_activity_short_map = [
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ON_DEMAND		=> 'Od',
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__SIMULATION	=> 'Cs',
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__CONFERENCE	=> 'Cn',
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ILC			=> 'Li',
			
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_EDUCATION	=> 'Pr',
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_QUIZ		=> 'Qu',
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY	=> 'Su',
			
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY_TEMPLATE	=> 'Rd'
	];
	
	static private $course_activity_css_label = [
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ON_DEMAND		=> ['online-course-label','OnDemand Course'],
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__SIMULATION	=> ['clinSim-course-label','Clinical Simulation'],
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ILC			=> ['live-course-label','Live Event'],
			
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_EDUCATION	=> ['presentation-label','Online Module'],
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY	=> ['survey-label','Survey Module'],
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_QUIZ		=> ['quiz-label','Quiz Module'],
										   IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY_TEMPLATE	=> ['return-demo-label','Return Demonstration']
	];
	
	static private $course_activity_css_header = [
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ON_DEMAND		=> ['course-type-online','OnDemand Course'],
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__SIMULATION	=> ['course-type-clinSim','Clinical Simulation'],
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__ILC			=> ['course-type-live','Live Event'],
		
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_EDUCATION	=> ['course-type-presentation','Online Module'],
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY		=> ['course-type-survey','Survey Module'],
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_QUIZ			=> ['course-type-quiz','Quiz Module'],
	IDUHub_Lms2prod_Course::ACTIVITY_TYPE__WRAPPER_SURVEY_TEMPLATE	=> ['course-type-return-demo', 'Return Demonstration']
	];
	
	/**
	 * formats course id in a constant way thorughout the system
	 * 
	 * @param integer $id
	 * @param string enum $activity_type
	 * @return string html of format  <span class='course-id'> (Od-014397)</span>
	 */
	static public function formatted_course_id($id,$activity_type){
		return sprintf('<span class="course-id"> (%s-%06d)</span>',self::$course_activity_short_map[$activity_type],$id);
	}
	
	/**
	 *  Generates the first td in curriculum, catalog and saved sections
	 *  @param unknown $activity_type
	 *  @return string
	 */
	static public function course_type_td_label($activity_type){
		return sprintf('<td class="%s">%s</td>',self::$course_activity_css_label[$activity_type][0],self::$course_activity_css_label[$activity_type][1]);
	}
	
	/**
	 *  Gets the course id in a human readable format
	 *  @param integer $id
	 *  @param string enum $activity_type
	 *  @return string formatted course id
	 */
	static public function readable_course_id($id, $activity_type){
		return sprintf('%s-%06d', self::$course_activity_short_map[$activity_type],$id);
	}
	
	/**
	 *  Box header for use in course details screen
	 *  @param string enum $activity_type
	 *  @return string html of course details side box header div
	 */
	static public function course_details_box_header($activity_type){
		return sprintf('<div class="course-info-box-header %s">%s</div>', self::$course_activity_css_header[$activity_type][0], self::$course_activity_css_header[$activity_type][1]);
	}
	
	
}
