<?php namespace Reports\Lib\FilterElementSchema;
/**
 * Set of generic date related filters.
 * Can be configured to serve multiple purposes
 */

/**
 * Generates the values for a date range filter and the values of the 
 * shortcuts for current FY, previous FY, current calendar and previous calendar.
 * 
 * @param string $filter_id
 * @return array<string,mixed>
 */
function date_range(string $filter_id,string $filter_name):array{
   $d = new \DateTime();
   $year  = $d->format('Y');
   $month = $d->format('n');
   $previous_year = $year-1;
   $current_fy_start = $year;
   $current_fy_end   = $year;
    
   //calendar years
   $values = [
       'current_yr'  => ['from'=>"{$year}0101",'to'=>"{$year}1231"],
       'previous_yr' => ['from'=>"{$previous_year}0101",'to'=>"{$previous_year}1231"],
   ];
   
   //current FY
   if($month>6){
       $current_fy_end++;
   } else {
       $current_fy_start--;
   }
   $values['current_fy'] = ['from'=>"{$current_fy_start}0701",'to'=>"{$current_fy_end}0630"];
   
   //Next FY
   $current_fy_end--;
   $current_fy_start--;
   $values['previous_fy'] = ['from'=>"{$current_fy_start}0701",'to'=>"{$current_fy_end}0630"];
   
   return [
       'name'          => $filter_name,
       'filter_id'     => $filter_id,
       'filter_type'   => \Reports\UI_FILTER_TYPE__DATERANGE,
       'values' => $values,
       'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
       'ui_suggested_location' => '?',
       'ui_suggested_order'    => '?'
   ];
}

/**
 * A generic date filter (single date)
 * 
 * @param string $filter_id
 * @param string $filter_name
 * @return array<string,mixed>
 */
function date_filter(string $filter_id,string $filter_name):array{
    return [
        'name'          => $filter_name,
        'filter_id'     => $filter_id,
        'filter_type'   => \Reports\UI_FILTER_TYPE__DATE,
        'values'        => [],
        'ui_suggested_step'     =>  \Reports\SCHEMA_SECTION__FILTERS__MAIN_REPORT_PAGE,
        'ui_suggested_location' => '?',
        'ui_suggested_order'    => '?'
    ];
}

