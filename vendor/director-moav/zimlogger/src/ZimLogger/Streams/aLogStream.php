<?php namespace ZimLogger\Streams;
abstract class aLogStream{
	public const	VERBOSITY_LVL_DEBUG		= 4,
					VERBOSITY_LVL_INFO		= 3,
					VERBOSITY_LVL_WARNING	= 2,
					VERBOSITY_LVL_ERROR		= 1,
					VERBOSITY_LVL_FATAL		= 0
	;

	/**
	 * @var string $log_name
	 * @var int $verbosity_level
	 * @var string $target_stream
	 * @var boolean $use_low_memory_footprint
	 * @var array<callable> $full_stack_subscribers list of functions that will be called when the full stack data log is triggered - to return data to be dumped into log
	 */
	protected $log_name, 
			  $verbosity_level, 
	          $target_stream,
	          $use_low_memory_footprint = false,
	          $full_stack_subscribers   = []
	;
	
	/**
	 * @param string $inp
	 * @param int $severity
	 * @param array<array> $full_stack_data
	 */
	abstract protected function log(string $inp,int $severity,array $full_stack_data=[]):void;
	
	/**
	 * Translate to string the input, how to output? that depends on how
	 * you implemented the `log` method
	 *
	 * @param mixed $inp
	 * @param int $severity
	 * @param bool $full_stack
	 */
	protected function tlog($inp,int $severity,bool $full_stack=false):void{
	    $text_inp = '';
		if ($inp === null){
		    $text_inp = 'NULL';
			
		}elseif($inp instanceof \Throwable){
		    $text_inp = $inp . '';//cast to string
			
		}elseif(!is_string($inp) && !is_numeric($inp)){
			if($this->use_low_memory_footprint){
				switch (gettype($inp)){
					case 'array':
					    $text_inp = print_r($inp,true);
						break;
						
					case 'object':
					    $text_inp = get_class($inp);
						break;
						
					default:
					    $text_inp = ' GOT TYPE OF VAR ' . gettype($inp);
						break;
				}
			} else {
			    $text_inp = print_r($inp,true);
			}
		} else {
		    $text_inp = $inp.'';
		}
		
		$full_stack_data = [];
		if($full_stack){
			$full_stack_data['session']   = $_SESSION ?? [];
			$full_stack_data['request']   = $_REQUEST;//it is always set
			$full_stack_data['request']['AND THE RAW BODY IS'] = file_get_contents('php://input');
			$full_stack_data['server']    = $_SERVER;
			$full_stack_data['subscribers']  = $this->get_full_stack_subscribers_data();
		}
		$this->log($text_inp,$severity,$full_stack_data);
	}

	/**
	 * @param string $log_name
	 * @param int $verbosity_level
	 * @param string $target_stream
	 */
	final public function __construct(string $log_name,int $verbosity_level,string $target_stream='',bool $use_low_memory_footprint=false){
		$this->log_name 				= $log_name;
		$this->verbosity_level 			= $verbosity_level;
		$this->target_stream 			= $target_stream;
		$this->use_low_memory_footprint = $use_low_memory_footprint;
		$this->init();
	}
	
	/**
	 * 
	 * @return \ZimLogger\Streams\aLogStream
	 */
	protected function init():\ZimLogger\Streams\aLogStream{
		return $this;
	}
	
	/**
	 * Function to fetch more data when full stack is triggered.
	 * 
	 * @param callable $func
	 * @param string $label
	 */
	public function full_stack_subscribe(callable $func,string $label):void{
	    $this->full_stack_subscribers[$label] = $func;
	}
	
	/**
	 * Loops on callables and put it into an array
	 * @return array<mixed>
	 */
	protected function get_full_stack_subscribers_data():array{
	    $debug_data = [];
	    foreach($this->full_stack_subscribers as $label=>$subscriber){
	        $debug_data[$label] = print_r($subscriber(),true);
	    }
	    return $debug_data;
	}
	
	/**
	 * 
	 * @param mixed $inp
	 */
	public function debug($inp):void{
		if($this->verbosity_level >= self::VERBOSITY_LVL_DEBUG){
			$this->tlog($inp,self::VERBOSITY_LVL_DEBUG);
		}
	}
	
	/**
	 * 
	 * @param mixed $inp
	 * @param bool $full_stack
	 */
	public function info($inp,bool $full_stack):void{
		if($this->verbosity_level >= self::VERBOSITY_LVL_INFO){
			$this->tlog($inp,self::VERBOSITY_LVL_INFO,$full_stack);
		}
	}
	
	/**
	 * 
	 * @param mixed $inp
	 * @param bool $full_stack
	 */
	public function warning($inp,bool $full_stack):void{
		if($this->verbosity_level >= self::VERBOSITY_LVL_WARNING){
			$this->tlog($inp,self::VERBOSITY_LVL_WARNING,$full_stack);
		}
	}

	/**
	 * 
	 * @param mixed $inp
	 * @param bool $full_stack
	 */
	public function error($inp,bool $full_stack):void{
		if($this->verbosity_level >= self::VERBOSITY_LVL_ERROR){
			$this->tlog($inp,self::VERBOSITY_LVL_ERROR,$full_stack);
		}
	}
	
	/**
	 * 
	 * @param mixed $inp
	 * @param bool $full_stack
	 */
	public function fatal($inp,bool $full_stack):void{
		if($this->verbosity_level >= self::VERBOSITY_LVL_FATAL){
			$this->tlog($inp,self::VERBOSITY_LVL_FATAL,$full_stack);
		}
	}
	
	/**
	 * This will prevent some big log dumps on crashes.
	 * Turn this on only after memory issues are found.
	 * 
	 * @param bool $use_low_memory_footprint
	 */
	final public function setUseLowMemoryFootprint(bool $use_low_memory_footprint):void{
		$this->use_low_memory_footprint = $use_low_memory_footprint;
	}
}
