<?php
class BL_Hub_DataCleaner_VarCharLength100 extends BL_Hub_DataCleaner_Abstract {
    
    function filter($data) {
        return substr($data,0,100);
    }
    
}