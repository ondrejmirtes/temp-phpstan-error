<?php namespace BL_MatchMaker_Algorithm;

/**
 * Determine if one name contains the other or vice versa
 * @author preston
 *
 */
class LevenshteinEmail extends AlgorithmAbstract{
    
    /**
     *
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        return levenshtein($first_value,$second_value)<4?$this->weight:0;
    }
}
