<?php namespace SiTEL\DataSources\Redis\Mask;

trait tSet{
    /**
     * Restrict the key to be one type of Redis object
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    protected function get_redis_mask(\SiTEL\DataSources\Redis\Client $r):\SiTEL\DataSources\Redis\aClientMask{
        return new Set($r);
    }
}

/**
 * Simple one value key space
 * 
 * @author itay
 *
 */
class Set extends \SiTEL\DataSources\Redis\aClientMask implements \SiTEL\DataSources\Redis\iScannable{
    /**
     * @param array<int, array<string, array>> $members
     * @return int number of members added
     */
    public function sadd_array (array $members):int{
        return \call_user_func_array([$this->r,'sadd'],$members);
    }
    
    /**
     * @param array<string,array> $members
     * @return int number of members added
     */
    public function sadd (...$members):int{
        return $this->sadd_array($members);
    }
    
    /**
     * @param array<string,array> $members
     * @return int number of members removed
     */
    public function srem (...$members):int{
        return $this->srem_array($members);
    }
    
    /**
     * @param array<int, array<string, array>> $members
     * @return int number of members removed
     */
    public function srem_array (array $members):int{
        return \call_user_func_array([$this->r,'srem'],$members);
    }
    
    /**
     * @return int number of elements in set
     */
    public function scard():int{
        return $this->r->scard();
    }
    
    /**
     * @return array<int,mixed> of all set members
     */
    public function smembers():array{
        return $this->r->smembers();
    }
    
    /**
     * intersects two other keys and stores them in THIS key
     * 
     * @param \SiTEL\DataSources\Redis\aKeyBoss $intersec_this
     * @param \SiTEL\DataSources\Redis\aKeyBoss $intersec_with
     * @return int number of elements in current key
     */    
    public function sinterstore(\SiTEL\DataSources\Redis\aKeyBoss $intersec_this,\SiTEL\DataSources\Redis\aKeyBoss $intersec_with):int{
        return $this->r->sinterstore($intersec_this,$intersec_with);
    }
    
    /**
     * Union input keys and stores them in THIS key
     * 
     * @param array<int, mixed> $keys of \SiTEL\DataSources\Redis\aKeyBoss
     * @return int number of members in new key
     */
    public function sunionstore_array(array $keys):int{
        return call_user_func_array([$this->r,'sunionstore'],$keys);
    }
    
    /**
     * Union input keys and stores them in THIS key
     *
     * @param \SiTEL\DataSources\Redis\aKeyBoss $keys array of 
     * @return int number of members in new key
     */
    public function sunionstore(\SiTEL\DataSources\Redis\aKeyBoss ...$keys):int{
        return $this->sunionstore_array($keys);
    }
    
    /**
     * Subtracts two keys and stores them in THIS key
     * 
     * @param \SiTEL\DataSources\Redis\aKeyBoss $subtract_from_that
     * @param \SiTEL\DataSources\Redis\aKeyBoss $subtract_this
     * @return int number of members in THIS key
     */
    public function sdiffstore(\SiTEL\DataSources\Redis\aKeyBoss $subtract_from_that, \SiTEL\DataSources\Redis\aKeyBoss $subtract_this):int{
        return $this->r->sdiffstore($subtract_from_that,$subtract_this);
    }
    
    /**
     * Returns if member is a member of the set stored at key.
     * @param mixed $member
     * @return int 0 | 1
     */
    public function sismember($member):int{
        return $this->r->sismember($member);
    }
    
    /**
     * raw SET scanner
     * 
     * @param ?int $cursor should be mnanaged by Redis. See iterator function below to see usage
     * @param string|false $pattern
     * @return bool|array<int,mixed>
     */
    public function sscan(?int &$cursor,$pattern=false){
        return $this->r->sscan($cursor,$pattern);
    }
    
    /**
     * Scan plugged in here
     * 
     * {@inheritDoc}
     * @see \SiTEL\DataSources\Redis\iScannable::scan_me()
     * @param ?int $cursor should be mnanaged by Redis. See iterator function below to see usage
     * @param string|false $pattern
     * @return bool|array<int,mixed> 
     */
    public function scan_me(?int &$cursor,$pattern=false){
        return $this->sscan($cursor,$pattern);
    }
}
