<?php namespace SiTEL\DataSources\Sql;
/**
 * Wrapper for easy access to stored procedures in Omega Supreme.
 * This depends adding the auto completion file to the Eclipse language directory for core php
 * 
 * @author itaymoav
 *
 */
class Omega{
	/**
	 * @var \SiTEL\DataSources\Sql\MySqlClient
	 */
    private $DB;
    
    /**
     * DO NOT CHANGE THIS RETURN TYPE. It allows for the auto completion to work with RCOM
     * @return Omega
     */
    static public function Supreme(string $db_name=''):Omega{
        return new self($db_name);
    }
    
    /**
     * 
     * @param string $db_name
     */
    public function __construct(string $db_name=''){
    	if($db_name){
    		$this->DB = \SiTEL\DataSources\Sql\Factory::getConnectionMySQL($db_name);
    	}
    	$this->DB = \SiTEL\DataSources\Sql\Factory::getDefaultConnectionMySql();
    }
    
    /**
     * Close cursor for buggy Stored Procedure calls
     * @return \SiTEL\DataSources\Sql\MySqlClient
     */
    public function closeCursor(){
    	return $this->DB->closeCursor();
    }
    
    /**
     * @return Omega
     */
    public function s():Omega{
    	return $this;
    }
    
    /**
     * @param string $sp
     * @param array<int, mixed> $args
     * 
     * @return \SiTEL\DataSources\Sql\MySqlClient
     */
    public function __call($sp,$args){
        $sp = str_replace('__','.',$sp);
        return $this->DB->callArr($sp, $args);
    }
}

