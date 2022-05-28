<?php namespace BL_MatchMaker;
/**
 *  Matches data and scores them based on algorithms to determine their similarity
 *  @author preston
 *
 */
class Matcher{
    
 
    /**
     *  Generate a score based on algorithms to determine the similarity between records
     *  @param MatchDataAbstract $FirstMatchData
     *  @param MatchDataAbstract $SecondMatchData
     *  @param array $MatchAlgorithms
     *  @return int
     */
    public static function matchScore(MatchDataAbstract $FirstMatchData, MatchDataAbstract $SecondMatchData, array $MatchAlgorithms):int{
        
        $score              = 0;
        $FirstMatchData     = $FirstMatchData->getAllValues();
        $SecondMatchData    = $SecondMatchData->getAllValues();
        $match_attributes   = array_intersect(array_keys($FirstMatchData),array_keys($SecondMatchData));
        
        foreach($match_attributes as $match_attribute){
            if($FirstMatchData[$match_attribute] && $SecondMatchData[$match_attribute]){
                if(isset($MatchAlgorithms[$match_attribute]) &&
                    is_array($MatchAlgorithms[$match_attribute])){
                    
                        foreach($MatchAlgorithms[$match_attribute] as $Alg){
                        $score  += $Alg->compare($FirstMatchData[$match_attribute],$SecondMatchData[$match_attribute]);
                    }
                }else{
                    $Alg    = new \BL_MatchMaker_Algorithm\Equal(10);
                    $score  += $Alg->compare($FirstMatchData[$match_attribute],$SecondMatchData[$match_attribute]);
                }
            }
        }
        
        return $score;
    }
}
