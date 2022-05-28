<?php namespace Reports\Lib\FilterElementSql\Enrollment;

/**
 * This one is a bit comp0lex, it involves multiple fields and in some cases left joins
 * Check the code below to see.
 * Read the code.
 * 
 * @author itay
 *
 */
class CourseEnrollmentStatus extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = '';
    /**
     * @var string
     */
    static protected $filter_id = 'course_enrollment_status';
    
    /**
     * This filter's WHERE
     * 
     * @var string
     */
    private string $sql_where = '';
    
    /**
     *
     * @param mixed $values_to_filter_by
     */
    public function __construct($values_to_filter_by){
        if(\Reports\UI_FILTER_COURSE_STATUS__ALL === $values_to_filter_by){
            $this->values_to_filter_by = \Reports\FILTER_ELEMENT_EMPTY_VALUE;
        } else {
            $this->values_to_filter_by = $values_to_filter_by;
            $this->calculate_filter();
        }
    }
    
    /**
     * Tells outsiders if they should left join or just join to the course enropllment mv
     * This depends on filter value
     * @return string
     */
    public function join_type():string{
        if(\Reports\UI_FILTER_COURSE_STATUS__NOT_ENROLLED === $this->values_to_filter_by || \Reports\UI_FILTER_COURSE_STATUS__INCOMPLETE === $this->values_to_filter_by){
            return 'LEFT';
        }
        return '';
    }
    
    /**
     * Decide which fields and values we filter by
     * into sql_fields (private)
     */
    private function calculate_filter():void{
        switch($this->values_to_filter_by){
            case \Reports\UI_FILTER_COURSE_STATUS__COMPLETE:
                $this->sql_where = 'is_completed = 1';
                break;
                
            case \Reports\UI_FILTER_COURSE_STATUS__INCOMPLETE:
                $this->sql_where = '(is_completed = 0 OR is_completed IS NULL)';
                break;
                
            case \Reports\UI_FILTER_COURSE_STATUS__ENROLLED: //enrolled, not complete
                $this->sql_where = "is_completed = 0 AND enrolled_or_assigned = 'enrolled'";
                break;
                
            case \Reports\UI_FILTER_COURSE_STATUS__NOT_ENROLLED:
                $this->sql_where = 'course_enrollment_id IS NULL';
                break;
                
            case \Reports\UI_FILTER_COURSE_STATUS__ASSIGNED: //not enrolled, not completed
                $this->sql_where = "is_completed = 0 AND enrolled_or_assigned = 'assigned'";
                break;
                
            case \Reports\UI_FILTER_COURSE_STATUS__OVERDUE: //enrolled, not completed and past date
                $today_date_id = (new \DateTime())->format('Ymd');
                $this->sql_where = "due_date_id<{$today_date_id} AND (is_completed = 0 OR is_completed IS NULL)";
                break;
                
            default:
                throw new \Exception("Unknown filter value for course enrolment status [{$this->values_to_filter_by}]");
        }
    }
    
    /**
     * overwrite - silence
     * 
     * {@inheritDoc}
     * @see \Reports\Lib\aMayhemFilterElement::add_escaped_values()
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
       //booo haahahahahaha bohuhkfdghqweq crap!
    }
    
    
    /**
     * @return string
     */
    public function sql_where():string{
        if($this->values_to_filter_by !== \Reports\FILTER_ELEMENT_EMPTY_VALUE){
            return $this->sql_where;
        }
        return '';
    }
}

