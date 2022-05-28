<?php namespace BL_MatchMaker_Algorithm;
/**
 * Does the year match between these dates
 * @author preston
 *
 */
class WrongFormatDate extends AlgorithmAbstract{
    
    
    /**
     *
     * {@inheritDoc}
     * @see \BL_MatchMaker_Algorithm\AlgorithmAbstract::compare()
     */
    public function compare($first_value,$second_value):int{
        
        return ($first_value &&
            $second_value) &&
            ((new \DateTime($first_value))->format('mdY') == (new \DateTime($second_value))->format('dmY'))?$this->weight:0;
    }
}
