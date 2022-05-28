<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * All departments in the population fetched - will use hr snapshot, will see how it goes
 * 
 * @author itay
 *
 */
class DepId extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'dim_user_hr_snapshot.primary_organization_department_id';
    /**
     * @var string
     */
    static protected $filter_id = 'learner_dep_id';
}

