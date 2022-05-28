<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * Learner status in the system (active/terminated/all)
 * 
 * @author itay
 *
 */
class LmsStatus extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'is_terminated';
    /**
     * @var string
     */
    static protected $filter_id = 'learner_status';
   
    /**
     * @return string
     */
    public function sql_where():string{
        switch($this->values_to_filter_by){
            case \User_RBAC_Roles_Learner::ID:
                return 'is_terminated=0';
            case \User_RBAC_Roles_TerminatedLearner::ID:
                return 'is_terminated=1';
            default:
                return '';
        }
    }
}
