<?php namespace Reports\Lib\FilterElementSchema;

/**
 * @return array<string,mixed>
 */
function course_id(): array
{
    return [
        'name'          => 'Course',
        'filter_id'     => \Reports\Lib\FilterElementSql\Course\CourseId::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
        'values' => [
            'url' => \Reports\URL_SEARCH__COURSE,
            'treshold' => 3 //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__PRE_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 * 
 * @return array<string,mixed>
 */
function course_completion_status(): array
{
    return [
        'name'          => 'Enrollment Status',
        'filter_id'     => \Reports\Lib\FilterElementSql\Enrollment\CourseCompletionStatus::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_SINGLE,
        'values' => [
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__COMPLETE,   'label' => 'Complete'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__INCOMPLETE, 'label' => 'Incomplete'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__ALL,        'label' => 'All']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 *
 * @return array<string,mixed>
 */
function course_enrollment_status(): array
{
    return [
        'name'          => 'Enrollment Status',
        'filter_id'     => \Reports\Lib\FilterElementSql\Enrollment\CourseEnrollmentStatus::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_SINGLE,
        'values' => [
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__COMPLETE,    'label' => 'Complete'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__ENROLLED,    'label' => 'Enrolled'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__NOT_ENROLLED,'label' => 'Not Enrolled'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__ASSIGNED,    'label' => 'Assigned'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__OVERDUE,     'label' => 'Overdue'],
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__ALL,         'label' => 'All']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 *
 * @return array<string,mixed>
 */
function course_activity_type():array
{
    return [
        'name'          => 'Course Type',
        'filter_id'     => \Reports\Lib\FilterElementSql\Course\CourseActivityType::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_MULTIPLE,
        'values' => [
            ['id' => \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ON_DEMAND,     'label' => 'On Demand Course'],
            ['id' => \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__SIMULATION,    'label' => 'Clinical Simulation Course'],
            ['id' => \IDUHub_LMs2prod_Course::ACTIVITY_TYPE__ILC,           'label' => 'Live Event Course'],
            
            ['id' => \Reports\UI_FILTER_COURSE_STATUS__ALL,                 'label' => 'All']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 *
 * @return array<string,mixed>
 */
function course_activity_director():array
{
    return [
        'name'          => 'Activity Director',
        'filter_id'     => \Reports\Lib\FilterElementSql\Course\CourseActivityDirector::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE,
        'values' => [
            'url'      => \Reports\URL_SEARCH__ACTIVITY_DIRECTOR,
            'treshold' => 3 //minimun amound of characters. below that, filter will be ignored
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

/**
 *
 * @return array<string,mixed>
 */
function latest_enrollment():array
{
    return [
        'name'          => 'Latest enrollment Vs. All enrollments',
        'filter_id'     => \Reports\Lib\FilterElementSql\Enrollment\LatestEnrollment::id(),
        'filter_type'   => \Reports\UI_FILTER_TYPE__LIST_STATIC_SINGLE,
        'values' => [
            ['id' => 'course_enrollment_latest', 'label' => 'Latest enrollment'],
            ['id' => 'course_enrollment',        'label' => 'All Enrollments']
        ],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}
