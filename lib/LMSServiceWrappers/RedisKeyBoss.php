<?php
abstract class LMSServiceWrappers_RedisKeyBoss extends \SiTEL\DataSources\Redis\aKeyBoss{
    /**
     * Get's config from app_env()
     */
    protected function host():string{
        return app_env()['database']['redis']['host'];
    }

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataSources\Redis\aKeyBoss::logger()
     */
    protected function logger():\ZimLogger\Streams\aLogStream{
        return \ZimLogger\MainZim::$CurrentLogger;
    }
}