<?php namespace Reports\Lib\FilterElementSql\Course;

class CourseActivityType extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'course_enrollment.course_activity_type';
    /**
     * @var string
     */
    static protected $filter_id = 'course_activity_type';
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     *
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        $ret = [];
        foreach ($values_to_filter_by as $course_type){
            switch($course_type){
                case \Reports\UI_FILTER_COURSE_STATUS__ALL:
                    return \Reports\FILTER_ELEMENT_EMPTY_VALUE;
                    
                case \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ON_DEMAND:
                    $ret[]=\IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ON_DEMAND;
                    break;
                    
                case \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__SIMULATION:
                    $ret[]=\IDUHub_LMs2prod_Course::ACTIVITY_TYPE__SIMULATION;
                    break;
                    
                case \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__CONFERENCE:
                    $ret[]=\IDUHub_LMs2prod_Course::ACTIVITY_TYPE__CONFERENCE;
                    break;
                    
                case \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ILC:
                    $ret[]=\IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ILC;
                    break;
            }
        }
        
        return $ret;
    }
}
