<?php namespace Reports;
//API KEY WORDS
const API_KEYWORD__SORT_BY        = 'sortby';
const API_KEYWORD__SORT_DIRECTION = 'sortdir';
const API_KEYWORD__SORT_DIRECTION__ASC  = 'asc';
const API_KEYWORD__SORT_DIRECTION__DESC = 'desc';
const API_KEYWORD__PERSPECTIVE           = 'perspective';
const API_KEYWORD__PERSPECTIVE__USERS    = 'users';      //users who somehow report to you or u track
const API_KEYWORD__PERSPECTIVE__ACTIVITY = 'activity';   //Activities, Courses, you own
const API_KEYWORD__PERSPECTIVE__CONTENT  = 'content';    //Content you own
const API_KEYWORD__PERSPECTIVE__MANAGER  = 'manager';    //RA, DH, DHC (Users on Organizations or departments you manage)
const API_KEYWORD__PERSPECTIVE__SUPERVISOR = 'supervisor'; //Supervisor (Users who report to you directly)
const API_KEYWORD__PERSPECTIVE__EDUSPEC  = 'eduspec';    //Education Specialist (Users in education groups
const API_KEYWORD__PERSPECTIVE__HR       = 'hr';         //HR Smartviews
const API_KEYWORD__PERSPECTIVE__ALL      = 'all';        //All, no perspective

const API_KEYWORD__SEARCH__SMARTSEARCH_TERM = 'term'; //what paramname to use to smart search 
const API_KEYWORD__SEARCH__FETCH       = 'fetch';     //Param to tell how much to load (number/all) if ommited, behaves as a smart search with term
const API_KEYWORD__SEARCH__FETCH__ALL  = 'all';       //Tells the search to load the entire list
const API_KEYWORD__SEARCH__FETCH__DEFAULT_SIZE  = 20; //Default limit on search result

const API_KEYWORD__PAGER_TOTAL_COUNT  = 'totalcount'; //We cache in this value the total size of the dataset. Lack of this value will trigger a recount (we do not want this too much)
const API_KEYWORD__PAGER_CURRENT_PAGE = 'currentpage';

//SIZES
const DATA_DEFAULT_PAGE_SIZE = 500;
const DATA_BATCH_SIZE = 10;


//SCHEMA KEY WORDS
const SCHEMA_DEFAULT_SORT_BY   = 'default_sort_by';
const SCHEMA_DEFAULT_SORT_DIR  = 'default_sort_dir';
const SCHEMA_REPORT_ID         = 'report_id';
const SCHEMA_REPORT_NAME       = 'report_name';
const SCHEMA_REPORT_DESCRIPTION = 'report_description';
const SCHEMA_BATCH_SIZE        = 'batch_size';

const SCHEMA_SECTION__PERSPECTIVES          = 'perspectives';//will hold array of perspective this report supports.
const SCHEMA_SECTION__FILTERS               = 'filters';
const SCHEMA_SECTION__FILTERS__PRE_REPORT_PAGE  = 'pre_report_page';
const SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE = 'main_report_page';

const SCHEMA_SECTION__FIELDS                = 'fields';
const SCHEMA_SECTION__REPEATABLE_FIELDS     = 'repeatable_fields';

//UI KEYWORDS
const UI_FILTER_TYPE__FREE_TEXT                  = 'free-text';
const UI_FILTER_TYPE__AUTOCOMPLETION_ASYNC       = 'autocompletion-async';
const UI_FILTER_TYPE__LIST_STATIC_SINGLE         = 'list-static-single';
const UI_FILTER_TYPE__LIST_STATIC_MULTIPLE       = 'list-static-multiple';
const UI_FILTER_TYPE__LIST_ASYNC_SINGLE          = 'list-async-single';
const UI_FILTER_TYPE__LIST_ASYNC_MULTIPLE        = 'list-async-multiple';
const UI_FILTER_TYPE__DATE                       = 'date';
const UI_FILTER_TYPE__DATERANGE                  = 'date-range';

const UI_FILTER_DATERANGE__FROM            = 'FROM';
const UI_FILTER_DATERANGE__TO              = 'TO';

const UI_FILTER_COURSE_STATUS__COMPLETE      = 'complete';
const UI_FILTER_COURSE_STATUS__ENROLLED      = 'enrolled';
const UI_FILTER_COURSE_STATUS__NOT_ENROLLED  = 'not-enrolled';
const UI_FILTER_COURSE_STATUS__ASSIGNED      = 'assigned';
const UI_FILTER_COURSE_STATUS__OVERDUE       = 'overdue';
const UI_FILTER_COURSE_STATUS__INCOMPLETE    = 'incomplete';
const UI_FILTER_COURSE_STATUS__ALL           = 'all';

const UI_DATA_FIELD_TYPE__HIDDEN        = 'hidden';
const UI_DATA_FIELD_TYPE__STRING        = 'string';
const UI_DATA_FIELD_TYPE__PERCENTAGE    = 'percentage';
const UI_DATA_FIELD_TYPE__STATIC        = 'static';
const UI_DATA_FIELD_TYPE__COURSE_ID_ACTIVITY_COMBO = 'course_id_activity';
const UI_DATA_FIELD_TYPE__DATEID        = 'dateid';//yyyymmdd
const UI_DATA_FIELD_TYPE__FLAG1_OR_0    = 'flag10';// 1 positive 0 bad



//URLs
const URL_SEARCH__COURSE                         = '/reports/search/course';
const URL_SEARCH__DEPARTMENT                     = '/reports/search/department'; 
const URL_SEARCH__COSTCENTER                     = '/reports/search/costcenter'; 
const URL_SEARCH__SUPERVISOR                     = '/reports/search/supervisor';
const URL_SEARCH__ACTIVITY_DIRECTOR              = '/reports/search/ad';
const URL_SEARCH__JOB_TITLE                      = '/reports/search/jobtitle';

//BACKEND ONLY :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//SIZES
const SEARCH_MIN_CHAR_CNT_TO_SEARCH = 4;
const SEARCH_MIN_CHAR_CNT_TO_SEARCH_COST_CENTER = 3; 


//FLAGS
const FLAGS__PAGER_TOTAL_COUNT_NOT_CALCULATED = -1;
const FILTER_ELEMENT_EMPTY_VALUE              = -666;

//MACROS
/**
 * @param string $report_id
 * @return string
 */
function report_namespace(string $report_id):string{
    return '\Reports\Data\Standard\\' . str_replace('::','\\',$report_id);
}

                    
