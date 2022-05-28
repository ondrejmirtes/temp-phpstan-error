<?php namespace Reports\Lib\FilterElementSql\Enrollment;

class CourseCompletionStatus extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'course_enrollment.is_completed';
    /**
     * @var string
     */
    static protected $filter_id = 'course_completion_status';
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     *
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        switch($values_to_filter_by){
            case \Reports\UI_FILTER_COURSE_STATUS__ALL:
                return \Reports\FILTER_ELEMENT_EMPTY_VALUE;
                
            case \Reports\UI_FILTER_COURSE_STATUS__COMPLETE:
                return '1';
                
            case \Reports\UI_FILTER_COURSE_STATUS__INCOMPLETE:
                return '0';
                
            default:
                return \Reports\FILTER_ELEMENT_EMPTY_VALUE;
            
        }
    }
}
