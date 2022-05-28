<?php namespace BL_MatchMaker_Algorithm;
/**
 * Does the year match between these dates
 * @author preston
 *
 */
class YearEqual extends AlgorithmAbstract{
    
    
    /**
     *
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        
        return ((isset($first_value) && $first_value) &&
                (isset($second_value) && $second_value)) &&
                ((new \DateTime($first_value))->format('Y') == (new \DateTime($second_value))->format('Y'))?$this->weight:0;
    }
}
