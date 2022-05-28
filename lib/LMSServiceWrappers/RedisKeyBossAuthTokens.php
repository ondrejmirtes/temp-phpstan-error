<?php
/**
 * Database specific abstract key 
 * for: auth tokens for linkact from feed user creation, lms quick linka that log you in and quick/direct links into a medstarapps service.
 * 
 * @author itay
 *
 */
abstract class LMSServiceWrappers_RedisKeyBossAuthTokens extends LMSServiceWrappers_RedisKeyBoss{
    /**
     * Returns the db number, defaults to 0
     *
     * @return int
     */
    public function get_db():int{
        return LMSServiceWrappers_RedisDatabase::AUTHTOKENS;
    }
}
