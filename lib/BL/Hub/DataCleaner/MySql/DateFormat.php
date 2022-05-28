<?php
class BL_Hub_DataCleaner_MySql_DateFormat extends BL_Hub_DataCleaner_Abstract {
    
    function filter($data) {
        
        return (new DateTime($data))->format(\commons\dates\MYSQL_DATE_FORMAT);
    }
    
}