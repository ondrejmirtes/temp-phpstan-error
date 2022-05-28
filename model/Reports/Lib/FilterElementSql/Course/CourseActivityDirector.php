<?php namespace Reports\Lib\FilterElementSql\Course;

/** 
 * @author itay
 *
 */
class CourseActivityDirector extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'course_enrollment.owner_rbac_user_id';
    /**
     * @var string
     */
    static protected $filter_id = 'activity_director_id';
}
