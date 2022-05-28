<?php namespace Reports\Lib\FilterElementSchema;
require_once __DIR__ . '/schema_filter_functions_dates.php';

/** 
 * Learner Source  (multi select - Checkbox: PeopleSoft, AMN, Guest, GUSOM, Manual(contractor, student, volunteer )
 * @return array<string,mixed>
 */
function learner_source(): array
{
    return [
        'name'          => 'Learner Source',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\LmsSource::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_MULTIPLE,
        'values' => [
            ['id' => 'peoplesoft',  'label' => 'Peoplesoft'],
            ['id' => 'amn',         'label' => 'AMN'],
            ['id' => 'gusom',       'label' => 'GUSOM']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * Learner Hire Date FROM - TO (date range)
 * @return array<string,mixed>
 */
function learner_hire_date(): array
{
    return date_range(\Reports\Lib\FilterElementSql\Learner\HireDate::id(),'Hire Date');
}

/**
 * @return array<string,mixed>
 */
function learner_status():array{
    return [
        'name'          => 'Learner Status',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\LmsStatus::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_SINGLE,
        'values' => [
            ['id' => '11', 'label' => 'Active'],
            ['id' => '12', 'label' => 'Terminated'],
            ['id' => 'all','label' => 'All']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
    
}


/**
 * @return array<string,mixed>
 */
function learner_identifier():array{
    return [
        'name'          => 'Learner ???',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\Identifier::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__FREE_TEXT,
        'values' => [
            'treshold' => \Reports\SEARCH_MIN_CHAR_CNT_TO_SEARCH //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * @return array<string,mixed>
 */
function learner_org_id():array{
    $org_values = \IDUHub_Lms2prod_Organization::select(['contract_status' => \IDUHub_Lms2prod_Organization::CONTRACT_STATUS__ACTIVE],
        ['id','organization_name AS label'],
        ' AND id NOT IN(30,1,35) ORDER BY 2 ');
    return [
        'name'          => 'Learner entities',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\OrgId::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_MULTIPLE,
        'values'        => $org_values,
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}


/**
 * @return array<string,mixed>
 */
function learner_dep_id():array{
        return [
            'name'          => 'Learner Department',
            'filter_id'     => \Reports\Lib\FilterElementSql\Learner\DepId::id(),
            'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
            'values'        => [
                'url' => \Reports\URL_SEARCH__DEPARTMENT,
                'treshold' => 2, //minimun amound of characters. below that, filter will be ignored
                'meta_expected_patters' => 'xxxx?;xxxxx-?????;xxxx'
            ],
            'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
            'ui_suggested_location' => '?',
            'ui_suggested_order'    => '?'
        ];
}

/**
 * @return array<string,mixed>
 */
function learner_cost_center():array{
    return [
        'name'          => 'Learner Cost Center',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\CostCenter::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
        'values'        => [
            'url'      => \Reports\URL_SEARCH__COSTCENTER,
            'treshold' => \Reports\SEARCH_MIN_CHAR_CNT_TO_SEARCH_COST_CENTER //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * @return array<string,mixed>
 */
function learner_supervisor_id():array{
    return [
        'name'          => 'Supervisor',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\SupervisorId::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
        'values'        => [
            'url' => \Reports\URL_SEARCH__SUPERVISOR,
            'treshold' => \Reports\SEARCH_MIN_CHAR_CNT_TO_SEARCH, //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * @return array<string,mixed>
 */
function learner_job_title():array{
    return [
        'name'          => 'Job Title',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\JobCode::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
        'values'        => [
            'url' => \Reports\URL_SEARCH__JOB_TITLE,
            'treshold' => \Reports\SEARCH_MIN_CHAR_CNT_TO_SEARCH, //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * @return array<string,mixed>
 */
function learner_job_function():array{
    $function_values = \IDUHub_Lms3users_JobFunction::select([],
        ['func_code AS id',"CONCAT(description,' (',func_code,')') AS label"],
        ' ORDER BY 2 ');
    
    return [
        'name'          => 'Function',
        'filter_id'     => \Reports\Lib\FilterElementSql\Learner\JobFunction::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_MULTIPLE,
        'values'        => [$function_values],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

