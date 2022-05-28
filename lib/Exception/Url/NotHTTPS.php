<?php
class Exception_Url_NotHTTPS extends Exception{
    public function __construct(){
        parent::__construct("Url must use HTTPS schema", ERRORCODE::HTTPS);
    }
}