<?php namespace ZimLogger\Streams;
class ColoredFile extends aLogStream{
    const   VI_COLOR__RED   = 9,
            VI_COLOR__BLUE  = 12,
            VI_COLOR__GREEN = 2,
            VI_COLOR__WHITE = 14,
            VI_COLOR__GRAY  = 7
    ;
    
    /**
     * @var array<string>
     */
    static private $sql_keywords = [
                            '/INSERT INTO/', 
                            '/UPDATE/',
                            '/VALUES/',
                            '/UNION/',
                            '/SELECT/',
                            '/SQL_CALC_FOUND_ROWS/',
                            '/AND/',
                            '/FROM/',
                            '/OR\s/',
                            '/WHERE/',
                            '/LIMIT/',
                            '/OFFSET/',
                            '/JOIN/',
                            '/GROUP BY/',
                            '/LEFT/',
                            '/ON\s/',
                            '/\sAS\s/',
                            '/ IN /',
                            '/DISTINCT/',
                            '/ORDER BY/',
                            '/SET/',
                            '/ ASC/',
                            '/DUPLICATE KEY/',
                            '/BETWEEN/',
                            '/UNIX_TIMESTAMP/',
                            '/FROM_UNIXTIME/',
                            '/COUNT/',
                            '/ROUND/',
                            '/FOUND_ROWS/',
                            '/RAND/',
                            '/GROUP_CONCAT/',
                            '/CONCAT/',
                            '/NOW/',
                            '/TIME/'];
    
    /**
     * @return ColoredFile
     * 
     * {@inheritDoc}
     * @see \ZimLogger\Streams\aLogStream::init()
     */
    protected function init():\ZimLogger\Streams\aLogStream{
		$this->log_name = $this->target_stream . $this->log_name . @date('m_d_Y', time()).'.log';
		return $this;	
	}
	
	/**
	 * {@inheritDoc}
	 * @see \ZimLogger\Streams\aLogStream::log()
	 */
	protected function log(string $inp,int $severity,array $full_stack_data = []):void{
	    //TODO move to a formatter later.
	    $inp = str_replace("\t","    ",$inp);
	    if($severity == self::VERBOSITY_LVL_DEBUG){
	        $severity_out = self::colorize($inp,self::VI_COLOR__GREEN);
	        $inp = preg_replace(self::$sql_keywords,self::colorize("$0",self::VI_COLOR__WHITE),$inp);// var_dump($inp);
	    }else{
	        $severity_out = self::colorize($inp,self::VI_COLOR__RED);
	    }
	    	
	    if($severity < self::VERBOSITY_LVL_INFO){
	        $inp = $inp?:' INP IS EMPTY? ';
	        $inp = self::colorize($inp,self::VI_COLOR__RED);
	    }
	    
		$stream = fopen($this->log_name, 'a');
		if(!$stream){
		    throw new \Exception("Could not open log file [{$this->log_name}]");
		}
		fwrite($stream, "[{$severity_out}][" . self::colorize(@date('h:i:s', time()),self::VI_COLOR__GRAY) . "] " . $inp . PHP_EOL);
		
		if($full_stack_data){
		    fwrite($stream, "[FULL STACK] \n" . print_r($full_stack_data,true) . PHP_EOL);
		}
		fclose($stream);
	}
	
	/**
	 * @param string $txt
	 * @param int $color
	 * @return string
	 */
	static private function colorize(string $txt,int $color):string{
	    return "\033[38;5;{$color}m{$txt}\033[0m";
	}
}
