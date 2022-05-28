<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * Supervisor rbac_user_id (primary)
 * 
 * @author itay
 *
 */
class JobCode extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'dim_user_hr_snapshot.primary_job_code';
    /**
     * @var string
     */
    static protected $filter_id = 'job_code';
}

