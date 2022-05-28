<?php
class Exception_HTTP_ServerError extends Exception{
    public function __construct ($message, $code, $previous=null) {
        $message = "Server stopped with code [{$code}] msg:[{$message}]";
        parent::__construct($message, $code, $previous);
    }
    
}