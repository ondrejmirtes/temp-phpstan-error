<?php namespace Reports\Lib\FilterElementSql\Enrollment;

class LatestEnrollment extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = '';
    /**
     * @var string
     */
    static protected $filter_id = 'enrollment_dataset';
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     *
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        switch($values_to_filter_by){
            case 'course_enrollment':
                return 'course_enrollment';
                
            case 'course_enrollment_latest':
                return 'course_enrollment_latest';
                
            default:
                return 'course_enrollment_latest';
        }
    }
    
    /**
     * No need for this value in this filter.
     * This filter actualy used just to validate user input
     * @return string
     */
    public function sql_where():string{
        return '';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Reports\Lib\aMayhemFilterElementArray::add_escaped_values()
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
        //silence this
    }
}
