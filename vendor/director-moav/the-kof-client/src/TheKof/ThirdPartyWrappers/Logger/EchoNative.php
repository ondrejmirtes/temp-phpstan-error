<?php namespace TheKof;
/**
 * The default logger - just echoes to stdio
 * 
 * @author Itay Moav
 * @Date 12-02-2018
 */
class ThirdPartyWrappers_Logger_EchoNative extends ThirdPartyWrappers_Logger_a{
	
	/****************************************************************************************************
	 * The following methods are what you have to implement.
	 * They will all get a message (string) and an optional data structure I will print_r($data,true)
	 * What you do with those, is yours to decide.
	 ****************************************************************************************************/
	
	public function debug(string $msg,$data_structure=null):void{
	    echo $msg . "\n";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function info(string $msg,$data_structure=null):void{
	    echo $msg . "\n";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function warning(string $msg,$data_structure=null):void{
	    echo $msg . "\n";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function error(string $msg,$data_structure=null):void{
	    echo $msg . "\n";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
	
	public function fatal(string $msg,$data_structure=null):void{
	    echo $msg . "\n";
	    if($data_structure){
	        print_r($data_structure);
	    }
	}
}

