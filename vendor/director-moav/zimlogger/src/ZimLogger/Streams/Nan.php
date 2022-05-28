<?php namespace ZimLogger\Streams;
/**
 * Not a real logger -> this is a sink
 * 
 * @author itaymoav
 */
class Nan extends aLogStream{
    	
    /**
     * 
     * {@inheritDoc}
     * @see \ZimLogger\Streams\aLogStream::log()
     */
    protected function log(string $inp,int $severity,array $full_stack_data = []):void{
        //abba nagila
	}
}