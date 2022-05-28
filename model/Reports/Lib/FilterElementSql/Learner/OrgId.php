<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * entities/org id learner is in
 * 
 * @author itay
 *
 */
class OrgId extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'dim_user_hr_snapshot.primary_organization_id';
    /**
     * @var string
     */
    static protected $filter_id = 'learner_org_id';
}

