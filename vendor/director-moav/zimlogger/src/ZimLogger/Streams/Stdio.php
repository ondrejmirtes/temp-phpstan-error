<?php namespace ZimLogger\Streams;
class Stdio extends aLogStream{
    
    /**
     * 
     * {@inheritDoc}
     * @see \ZimLogger\Streams\aLogStream::log()
     */
    protected function log(string $inp,int $severity,array $full_stack_data = []):void{
		echo $inp . "\n";
		if($full_stack_data){
		    echo "=============================== FULL STACK ======================================\n";
		    print_r($full_stack_data);
		}
		
	}
}