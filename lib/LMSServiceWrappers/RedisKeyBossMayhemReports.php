<?php
/**
 * For now , this uses the DATA db, but if in the future we want to separate, we can.
 * All report keys should be expirable
 * @author itay
 *
 */
abstract class LMSServiceWrappers_RedisKeyBossMayhemReports extends LMSServiceWrappers_RedisKeyBoss{
    /**
     * Returns the db number, defaults to 0
     *
     * @return int
     */
    public function get_db():int{
        return LMSServiceWrappers_RedisDatabase::DATA;
    }
}