<?php
ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('log_errors', true);
function autoload($class) {
    $file_path = __DIR__ . '/../../../src/' .  str_replace(['_','\\'],'/',$class) . '.php';
    if(!include_once $file_path){
        throw new Exception("ClassNotFound {$file_path} {$class}");
    }
    
}
spl_autoload_register('autoload');
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../vendor/director-moav/zimlogger/src/ZimLogger/shortcuts.php';

\ZimLogger\MainZim::$CurrentLogger = new \ZimLogger\Streams\Stdio('SiTEL_API_CORWIN', \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_DEBUG);
dbgn('----------------- BOOTSTRAP INITIALIZED -----------------------');

