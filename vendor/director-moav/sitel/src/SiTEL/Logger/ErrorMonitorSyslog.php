<?php namespace SiTEL\Logger;

/**
 * Sends logs to Apache/syslog
 *
 * @author itaymoav
 * @date 2021-02-02
 */
class ErrorMonitorSyslog extends \ZimLogger\Streams\aLogStream{
    public function warning($inp,$full_stack):void{
        parent::warning($inp,true);
    }
    
    public function error($inp,$full_stack):void{
        parent::error($inp,true);
    }
    
    public function fatal($inp,$full_stack):void{
        parent::fatal($inp,true);
    }
    
    protected function log(string $inp,int $severity,array $full_stack_data=[]):void{
        //making sure nothing triggers this logger from here (NO TO recursion!)
        \ZimLogger\MainZim::$CurrentLogger = new \ZimLogger\Streams\File($this->log_name,self::VERBOSITY_LVL_INFO,app_env()['log']['uri']);
        
        //SYSLOG
        try{
            $priority = \LOG_NOTICE;
            switch($severity){
                case \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_FATAL:
                    $priority = \LOG_EMERG;
                    break;
                    
                case \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_ERROR:
                    $priority = \LOG_ERR;
                    break;
                    
                case \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_WARNING:
                    $priority = \LOG_NOTICE;
                    break;
                    
                case \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_INFO:
                    $priority = \LOG_INFO;
                    break;
                    
                case \ZimLogger\Streams\aLogStream::VERBOSITY_LVL_DEBUG:
                    $priority = \LOG_DEBUG;
                    break;
            }
            //TODO do this in an init function next time
            openlog(app_env()['log']['name'], LOG_PID | LOG_PERROR, LOG_LOCAL0);
            syslog($priority,$inp . '');
            closelog();
        }catch (\Exception $e){
            //This is a log just in case of a total crash!
            //WRITE TO LOGFILE, THIS IS LAST RESORT, as might be emails or Data base has failed
            \ZimLogger\MainZim::$CurrentLogger->fatal($e,true);
        }
        
        if($this->use_low_memory_footprint){
            $bctr = 'low_memory_footprint=true, no backtrace available';
        } else {
            $bctr = print_r(array_slice(debug_backtrace(0),4),true);
        }
        
        $data = [
            'severity'          => $severity,
            'exception_message' => $inp,
            'exception_trace'   => $bctr,
            'request' => $full_stack_data? print_r($full_stack_data['request'] ,true)  :'',
            'session' => $full_stack_data? print_r($full_stack_data['session'] ,true)  :'',
            'server'  => $full_stack_data? print_r($full_stack_data['server']  ,true)  :'',
            'queries' => $full_stack_data? print_r($full_stack_data['database'],true)  :''
        ];
        
        try {
            \ZimLogger\MainZim::$CurrentLogger->fatal($data,false);
            
        }catch (\Exception $e){//making sure db crashes won't kill the email thingy
            //let us pray, nothing has worked!
        }finally {
            \ZimLogger\MainZim::$CurrentLogger = $this;
            }
    }
}
