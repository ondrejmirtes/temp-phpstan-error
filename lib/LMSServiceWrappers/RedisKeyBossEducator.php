<?php
/**
 * Educator persistant objects, like quiz schema , survey schema, scorm schema, course content tasks etc 
 * @author itay
 * @date 2021-12-02
 *
 */
abstract class LMSServiceWrappers_RedisKeyBossEducator extends LMSServiceWrappers_RedisKeyBoss{
    /**
     * Returns the db number 5
     *
     * @return int
     */
    public function get_db():int{
        return LMSServiceWrappers_RedisDatabase::EDUCATOR;
    }
}