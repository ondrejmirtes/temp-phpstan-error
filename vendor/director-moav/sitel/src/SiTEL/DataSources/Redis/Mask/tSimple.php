<?php namespace SiTEL\DataSources\Redis\Mask;

trait tSimple{
    /**
     * Restrict the key to be one type of Redis object
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    protected function get_redis_mask(\SiTEL\DataSources\Redis\Client $r):\SiTEL\DataSources\Redis\aClientMask{
        return new Simple($r);
    }
}

/**
 * Simple one value key space
 * 
 * @author itay
 *
 */
class Simple extends \SiTEL\DataSources\Redis\aClientMask{
    /**
     * @param bool $with_build
     * @return string
     */
    public function get(bool $with_build = true){
        return $this->r->get($with_build);
    }
    
    /**
     * 
     * @param mixed $value
     * @param int $ttl
     * @return string|NULL
     */
    public function set($value,int $ttl=0):?string{
        return $ttl?$this->r->set($value,$ttl):$this->r->set($value);
    }
    
    /**
     * 
     * @param mixed $value
     * @return string|NULL
     */
    public function getset($value):?string{
        return $this->r->getset($value);
    }
    
    /**
     * 
     * @param int $amount
     * @return int
     */
    public function incrby(int $amount):int{
        return $this->r->incrby($amount);
    }
    
    /**
     * Sets the variable IF not exists
     * @param mixed $value serializable
     * @return int
     */
    public function setnx($value):int{
        return $this->r->setnx($value);
    }
    
    /**
     * Sets value and expiration of key
     * 
     * @param int $seconds
     * @param mixed $value serializable
     */
    public function setex(int $seconds,$value):void{
        $this->r->psetex($seconds,$value);
    }

    /**
     * Sets value and expiration of key
     * 
     * @param int $milliseconds
     * @param mixed $value serializable
     */
    public function psetex(int $milliseconds,$value):void{
        $this->r->psetex($milliseconds,$value);
    }
    
    /**
     * @return int length of var
     */
    public function strlen():int{
        return $this->r->strlen();
    }
    
}
