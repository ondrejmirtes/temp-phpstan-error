<?php
class BL_Hub_DataCleaner_Mysql_DateTimeRange extends BL_Hub_DataCleaner_Abstract {
    
    const   MIN_DATETIME    = '1000-01-01 00:00:00',
            MAX_DATETIME    = '9999-12-31 23:59:59';
    
    function filter($data) {
        $date   = ((new DateTime($data))->format('Y-m-d') <  BL_Hub_DataCleaner_MySql_DateRange::MIN_DATE)?self::MIN_DATETIME:$data;
        $date   = ((new DateTime($data))->format('Y-m-d') >  BL_Hub_DataCleaner_MySql_DateRange::MAX_DATE)?self::MAX_DATETIME:$data;
        
        $date_array = explode('-',$date);
        $date   = strlen($date_array[0]) > 4?self::MAX_DATETIME:$date;
        
        return $date;
    }
    
}