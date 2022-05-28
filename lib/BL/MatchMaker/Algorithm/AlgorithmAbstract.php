<?php namespace BL_MatchMaker_Algorithm;

/**
 * Abstract for comparison algorithms used in the matchmaker
 * @author preston
 *
 */
abstract class AlgorithmAbstract{
    
    protected   $weight;
    
    /**
     * 
     * @param int $weight
     */
    public function __construct(int $weight){
        $this->weight   = $weight;
    }
    /**
     * 
     * @param mixed $first_value
     * @param mixed $second_value
     * @return int
     */
    abstract public function compare($first_value,$second_value):int;
}