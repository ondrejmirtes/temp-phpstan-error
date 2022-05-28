<?php
class BL_Hub_DataCleaner_MySql_NullDateFormat extends BL_Hub_DataCleaner_Abstract {
    
    function filter($data) {
        
        return $data?(new DateTime($data))->format(\commons\dates\MYSQL_DATE_FORMAT):NULL;
    }
    
}