<?php
class BL_Hub_DataCleaner_Mysql_DateRange extends BL_Hub_DataCleaner_Abstract {
    
    const   MIN_DATE    = '1000-01-01',
            MAX_DATE    = '9999-12-31';
    
    function filter($data) {
        $date   = ((new DateTime($data))->format('Y-m-d') <  self::MIN_DATE)?self::MIN_DATE:$data;
        $date   = ((new DateTime($data))->format('Y-m-d') >  self::MAX_DATE)?self::MAX_DATE:$data;
        
        $date_array = explode('-',$date);
        $date   = strlen($date_array[0]) > 4?self::MAX_DATE:$date;
        
        return $date;
    }
    
}