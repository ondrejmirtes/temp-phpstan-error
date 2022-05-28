<?php namespace Reports\Lib\FieldSchema;

/**
 * This combo is the one that generates Od-020344 ...
 * @return array<array<string,mixed>>
 */
function course_id_activity_combo():array{
    return [
        [
            'id'    => 'course_id',
            'name'  => 'course_id',
            'sortable'=> 0,
            'renderAs'=> \Reports\UI_DATA_FIELD_TYPE__HIDDEN,
            'filter'=> 1
        ],
        [
            'id'    => 'course',
            'name'  => 'ID',
            'sortable'=> 1,
            'renderAs'=> \Reports\UI_DATA_FIELD_TYPE__COURSE_ID_ACTIVITY_COMBO,
            'filter'=> 0
        ],
        [
            'id'    => 'course_title',
            'name'  => 'Course Name',
            'sortable'=> 1,
            'renderAs'=> \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'=> 0
        ],
        [
            'id'       => 'course_activity_type',
            'name'     => 'Course Type',
            'sortable' => 1,
            'renderAs' => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'   => 0
        ]
    ];
}

/**
 * 
 * @return array<array<string,mixed>>
 */
function activity_director(): array
{
    return [
        [
            'id'        => 'activity_director_fullname',
            'name'      => 'Activity Director',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ]
    ];
}

/**
 * 
 * @return array<array<string,mixed>>
 */
function learner_default_demographics():array{
    return [
        [
            'id'        => 'rbac_user_id',
            'name'      => 'rbac_user_id',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__HIDDEN,
            'filter'    => 1
        ],
        [
            'id'        => 'last_name',
            'name'      => 'Last Name',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'first_name',
            'name'      => 'First Name',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'middle_name',
            'name'      => 'Middle Name',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'learner_status',
            'name'      => 'Learner status', //active, terminated, contractor
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_job_name',
            'name'      => 'Job Title',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_function_name',
            'name'      => 'Job Function',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'medstar_employee_id',
            'name'      => 'Employee Id',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'medstar_email',
            'name'      => 'Medstar Email',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_organization_name',
            'name'      => 'Entity/Org',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_organization_department_name',
            'name'      => 'Department',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_cost_center',
            'name'      => 'Cost-Center',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'hired_date',
            'name'      => 'Hired Date',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__DATEID,
            'filter'    => 0
        ],
        [
            'id'        => 'lms_username',
            'name'      => 'Sitel User Name',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'pmid',
            'name'      => 'Medstar Network ID',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_supervisor_name',
            'name'      => 'Supervisor Name',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_supervisor_email',
            'name'      => 'Supervisor Email',
            'sortable'  => 0,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_supervisor_manager_level',
            'name'      => 'Supervisor manager level',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ],
        [
            'id'        => 'primary_user_source',
            'name'      => 'Source',
            'sortable'  => 1,
            'renderAs'  => \Reports\UI_DATA_FIELD_TYPE__STRING,
            'filter'    => 0
        ]
    ];
    							
}



