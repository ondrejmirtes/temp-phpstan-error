<?php
class BL_Hub_DataCleaner_Mysql_TimestampRange extends BL_Hub_DataCleaner_Abstract {
    
    const   MIN_TIMESTAMP_DATE  = '1970-01-01',
            MAX_TIMESTAMP_DATE  = '2038-01-19',
            MIN_TIMESTAMP       = '1970-01-01 00:00:01',
            MAX_TIMESTAMP       = '2038-01-19 03:14:07';
    
    function filter($data) {
        $date   = ((new DateTime($data))->format('Y-m-d') <  self::MIN_TIMESTAMP_DATE)?self::MIN_TIMESTAMP_DATE:$data;
        $date   = ((new DateTime($data))->format('Y-m-d') >  self::MAX_TIMESTAMP_DATE)?self::MAX_TIMESTAMP_DATE:$data;
        
        $date_array = explode('-',$date);
        $date   = strlen($date_array[0]) > 4?self::MAX_TIMESTAMP_DATE:$date;
        
        return $date;
    }
    
}