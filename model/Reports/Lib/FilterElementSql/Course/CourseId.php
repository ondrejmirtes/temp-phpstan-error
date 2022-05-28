<?php namespace Reports\Lib\FilterElementSql\Course;

/** 
 * @author itay
 *
 */
class CourseId extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'course_enrollment.course_id';
    /**
     * @var string
     */
    static protected $filter_id = 'course_id';
}
