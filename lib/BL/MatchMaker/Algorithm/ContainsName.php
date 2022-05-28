<?php namespace BL_MatchMaker_Algorithm;

/**
 * Determine if one name contains the other or vice versa
 * @author preston
 *
 */
class ContainsName extends AlgorithmAbstract{
    
    /**
     * 
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        return (((strlen($first_value) > 2) && (strlen($second_value) > 2)) || $first_value == $second_value) && (strpos($first_value,$second_value) !== false || strpos($second_value,$first_value) !== false)?$this->weight:0;
    }
}
