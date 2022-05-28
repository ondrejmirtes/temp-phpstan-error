<?php
/**
 * Database specific abstract key 
 * for (semi) persistant data, like Org data ans container <=> wrapper links and similar 
 * persistant datra structures
 * 
 * @author itay
 *
 */
abstract class LMSServiceWrappers_RedisKeyBossData extends LMSServiceWrappers_RedisKeyBoss{
    /**
     * Returns the db number, defaults to 0
     *
     * @return int
     */
    public function get_db():int{
        return LMSServiceWrappers_RedisDatabase::DATA;
    }
}