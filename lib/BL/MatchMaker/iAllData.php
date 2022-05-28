<?php namespace BL_MatchMaker;
/**
 * Interface to make sure getAllValues function exists in match data
 * @author preston
 *
 */
interface iAllData {
    /**
     * Values returned must be protected or public
     * Private values not returned
     * Protected is preferred
     * @return array
     */
    public function getAllValues();
}