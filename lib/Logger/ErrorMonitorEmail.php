<?php
/**
 * Use of tabl error_monitor + emails on info | warning | error | fatal
 * Will always use full stack for warning | error | fatal
 * 
 * @author itaymoav
 */
class Logger_ErrorMonitorEmail extends \ZimLogger\Streams\aLogStream{
    
    /**
     * Maximum Length of the error strings before we truncate and store in BLACK_LOG
     * @var integer
     */
    const ERROR_TRIM = 5000;
    
    public function error($inp,bool $full_stack):void{
        parent::error($inp,true);
    }
    
    public function fatal($inp,bool $full_stack):void{
        parent::fatal($inp,true);
    }
    	
    protected function log($inp,$severity,$full_stack_data = null):void{
	    //making sure nothing triggers this logger from here (NO TO recursion!)
        \ZimLogger\MainZim::$CurrentLogger = new \ZimLogger\Streams\Nan('nan',self::VERBOSITY_LVL_FATAL);
	    if($inp instanceof Exception){
		    $bctr = $inp->getTraceAsString();
	    } else {
		    $bctr = debug_backtrace(BACKTRACE_MASK);
		    $bctr = 'odedejoy' . print_r(array_slice($bctr,4),true);
        }
        
        $raw_data = [
            'severity'=>$severity,
            'exception_message' => ($inp instanceof Exception)?$inp->getMessage():$inp,
            'exception_trace' => $bctr,//($inp instanceof Exception)?print_r($inp->getTraceAsString(),true): 'odedejoy' . print_r(debug_backtrace(BACKTRACE_MASK),true),
            'request' => $full_stack_data? print_r($full_stack_data['request'],true) . ' XXXXXXX ' . file_get_contents('php://input'):'',
            'session' => $full_stack_data? print_r($full_stack_data['session'],true):'',
            'server'  => $full_stack_data? print_r($full_stack_data['server'],true):'',
            'queries' => $full_stack_data? print_r($full_stack_data['subscribers'] + rwdb()->getDebugData(),true):''
        ];
        
        $data = [
            'severity'=>$severity,
            'exception_message' => substr(($inp instanceof Exception)?$inp->getMessage():$inp,0,self::ERROR_TRIM),
            'exception_trace' => substr($bctr,0,self::ERROR_TRIM),//($inp instanceof Exception)?print_r($inp->getTraceAsString(),true): 'odedejoy' . print_r(debug_backtrace(BACKTRACE_MASK),true),
            'request' => substr($full_stack_data? print_r($full_stack_data['request'],true) . ' XXXXXXX ' . file_get_contents('php://input'):'',0,self::ERROR_TRIM),
            'session' => substr($full_stack_data? print_r($full_stack_data['session'],true):'',0,self::ERROR_TRIM),
            'server'  =>substr($full_stack_data? print_r($full_stack_data['server'],true):'',0,self::ERROR_TRIM),
            'queries' => substr($full_stack_data? print_r($full_stack_data['subscribers'] + rwdb()->getDebugData(),true):'',0,self::ERROR_TRIM)
        ];
        
        if( strlen($raw_data['exception_message']) > self::ERROR_TRIM ||
            strlen($raw_data['exception_trace']) > self::ERROR_TRIM ||
            strlen($raw_data['request']) > self::ERROR_TRIM ||
            strlen($raw_data['session']) > self::ERROR_TRIM ||
            strlen($raw_data['server']) > self::ERROR_TRIM ||
            strlen($raw_data['queries']) > self::ERROR_TRIM ){
           
                $data['exception_message'] .= " TRUNCATED";
                $c = app_env();
                $EmergencyLog = new \ZimLogger\Streams\File('BLACK_LOG_IS_DOWN_',self::VERBOSITY_LVL_WARNING,$c['log']['uri']);
                
                //Add DB FAILED to the message
                $raw_data['exception_message'] = 'INSERT TO DB TOO LARGE [' . $raw_data['exception_message'] . ']';
                $EmergencyLog->warning($raw_data,true);
        }
 
        //DB
	    try {//write to DB
	        IDUHub_Lms3transients_ErrorMonitoring::createRecord($data);
	        
		}catch (Exception $e){//making sure db crashes won't kill the email thingy
		    //This is a log just in case of a total crash!
		    $c = app_env();
		    $EmergencyLog = new \ZimLogger\Streams\File('BLACK_LOG_IS_DOWN_',self::VERBOSITY_LVL_FATAL,$c['log']['uri']);
		    
		    //Add DB FAILED to the message
		    $data['exception_message'] = 'INSERT ERROR TO DB FAILED [' . $data['exception_message'] . ']';
		    $EmergencyLog->fatal($e,true);
		}
		
		//EMAIL
		try{
		    run_async_proc('email/sendlog',[
		                          'subject'   => '[' . lifeCycle() . '] ' . $data['exception_message'],
		                          'body'      => $data['exception_trace']
            ]);
		}catch (Exception $e){
		    //This is a log just in case of a total crash!
		    $c = app_env();
		    $EmergencyLog = new \ZimLogger\Streams\File('BLACK_LOG_IS_DOWN_',self::VERBOSITY_LVL_FATAL,$c['log']['uri']);
		    
		    //WRITE TO LOGFILE, THIS IS LAST RESORT, as might be emails or Data base has failed
		    $EmergencyLog->fatal($e,true);
		}finally {
		    \ZimLogger\MainZim::$CurrentLogger = $this;
		}
	}
}
