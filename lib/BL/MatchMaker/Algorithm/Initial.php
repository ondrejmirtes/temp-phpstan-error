<?php namespace BL_MatchMaker_Algorithm;
/**
 * Do the values share the same starting letter
 * @author preston
 *
 */
class Initial extends AlgorithmAbstract{
    
    /**
     * 
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        return $first_value[0] == $second_value[0]?$this->weight:0;
    }
}