<?php
class Exception_HTTP_TemporaryNotAvailable extends Exception{
    public function __construct () {
        parent::__construct('Server temporarily unavailable');
    }
}