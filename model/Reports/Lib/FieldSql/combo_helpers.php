<?php namespace Reports\Lib\FieldSql;

/**
 * @return string
 */
function course_id_activity_combo():string{
    return "course_id,'',course_title,course_activity_type";
}

/**
 * 
 * @return string
 */
function activity_director():string
{
    return 'activity_director_name';
}

/**
 * 
 * @return string
 */
function learner_default_demographics():string{
    return "
            dim_user.rbac_user_id,
            dim_user.last_name,
            dim_user.first_name,
            dim_user.middle_name,
            dim_user.learner_status,
            dim_user.primary_job_name,
            dim_user.primary_function_name,
            dim_user.medstar_employee_id,
            dim_user.medstar_email,
            dim_user.primary_organization_name,
            dim_user.primary_organization_department_name,
            dim_user.primary_cost_center,
            dim_user.hired_date,
            dim_user.lms_username,
            dim_user.pmid,
            dim_user.primary_supervisor_name,
            dim_user.primary_supervisor_email,
            dim_user.primary_supervisor_manager_level,
            dim_user.primary_user_source";
}

