<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * Supervisor rbac_user_id (primary)
 * 
 * @author itay
 *
 */
class SupervisorId extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'dim_user_hr_snapshot.primary_supervisor_rbac_user_id';
    /**
     * @var string
     */
    static protected $filter_id = 'supervisor_id';
}

