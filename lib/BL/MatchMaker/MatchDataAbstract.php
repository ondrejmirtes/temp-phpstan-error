<?php namespace BL_MatchMaker;

abstract class MatchDataAbstract implements iAllData{

    /**
     * Values returned must be protected or public
     * Private values not returned
     * Protected is preferred 
     * @return array
     */
    public function getAllValues():array{
        
        return get_object_vars($this);
    }
}
