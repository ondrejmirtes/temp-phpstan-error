<?php namespace BL_MatchMaker_Algorithm;
/**
 * Are the values an exact match
 * @author preston
 *
 */
class Equal extends AlgorithmAbstract{
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        
        return $first_value == $second_value?$this->weight:0;
    }
}
