<?php
class Exception_Validate extends Exception{
    public function __construct($msg=''){
        parent::__construct("Failed validating: " . $msg,ERRORCODE::DATA_VALIDATION);
        
        
    }
    
}
