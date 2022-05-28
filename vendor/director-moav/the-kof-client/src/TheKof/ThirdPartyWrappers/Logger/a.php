<?php namespace TheKof;
/**
 * There are different loggers out there, you might have your own.
 * You can use your own logger with TheKof, just create
 * and adapter between your logger and TheKof and set it up in init
 * 
 * If your logger is a bunch of functions, you will need to wrap them in a class or array
 * of functions before sending them to the adapter
 * 
 * This logger supports five levels of log output
 * - debug
 * - info
 * - warning
 * - error
 * - fatal
 * 
 * Check Examples
 * 
 * @author Itay Moav
 * @Date 12-02-2018
 */
abstract class ThirdPartyWrappers_Logger_a{
	
	/**
	 * @var mixed the actual Logger class
	 */
	protected $concrete_logger_class = null;

	/**
	 * Just send in the instantiated with logger
	 * 
	 * @param mixed $concrete_logger_class
	 */
	public function __construct($concrete_logger_class = null){
	    $this->concrete_logger_class = $concrete_logger_class;
	}
	
	/****************************************************************************************************
	 * The following methods are what you have to implement.
	 * They will all get a message (string) and an optional data structure I will print_r($data,true)
	 * What you do with those, is yours to decide.
	 ****************************************************************************************************/
	
	abstract public function debug(string $msg,$data_structure=null):void;
	
	abstract public function info(string $msg,$data_structure=null):void;
	
	abstract public function warning(string $msg,$data_structure=null):void;
	
	abstract public function error(string $msg,$data_structure=null):void;
	
	abstract public function fatal(string $msg,$data_structure=null):void;
}

