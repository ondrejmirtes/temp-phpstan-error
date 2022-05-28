<?php
ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('log_errors', true);
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once '/etc/medstarapps/environment.php';//to get the lms endpoint
require_once __DIR__ . '/../../../src/SiTEL/Api/CorwinClient.php';
//require_once '/home/itay/dev-workspace-php/aybara/core/environments/fastweb.php';

if(app_env()['paths']['lms_api_url'] == 'api.sitelms.org/corwin/r2d0'){
    echo 'You are about to hit production. I die!';
    die;
}
$Logger = new \ZimLogger\Streams\Stdio('SiTEL_API_CORWIN', \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_DEBUG);
$Logger->debug('-------------------------------------------- BABA WAS HERE -----------------------------------------');