<?php namespace SiTEL\DataSources\Redis\Mask;

trait tHash{
    /**
     * Restrict the key to be one type of Redis object
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    protected function get_redis_mask(\SiTEL\DataSources\Redis\Client $r):\SiTEL\DataSources\Redis\aClientMask{
        return new \SiTEL\DataSources\Redis\Mask\Hash($r);
    }
}

/**
 * (TODO This is partial functionality)
 * Hash data type
 * 
 * @author itay
 *
 */
class Hash extends \SiTEL\DataSources\Redis\aClientMask{
    
    /**
     * Removes the specified fields from the hash stored at key. 
     * Specified fields that do not exist within this hash are ignored. 
     * If key does not exist, it is treated as an empty hash and this command returns 0.
     * 
     * @param string|array<string,string> ...$field
     * @return int
     */
    public function hdel(...$field):int{
        if(is_array($field[0])){
            $field = $field[0];
        }
        return \call_user_func_array([$this->r,'hdel'],$field);
    }
    
    /**
     * Returns if field is an existing field in the hash stored at key.
     * 
     * @param string $field
     * @return int
     */
    public function hexists(string $field):int{
        return $this->r->hexists($field);
    }
    
    /**
     * 
     * @param string $field
     * @return mixed
     */
    public function hget(string $field){
        return $this->r->hget($field);
    }
    
    /**
     * 
     * @return array<string,string>
     */
    public function hgetall():array{
        return $this->r->hgetall();
    }

    /**
     * Sets field in the hash stored at key to value, only if field does not yet exist. 
     * If key does not exist, a new key holding a hash is created. 
     * If field already exists, this operation has no effect.
     * 
     * @param string $field
     * @param mixed $value
     * @return int 0 or 1
     */
    public function hsetnx(string $field,$value):int{
        return $this->r->hsetnx($field,$value);
    }
    
    /**
     * 
     * @return array<string,string>
     */
    public function hkeys():array{
        return $this->r->hkeys();
    }
    
    /**
     * 
     * @param string $field
     * @param mixed $value
     * @return int
     */
    public function hset(string $field,$value):int{
        return $this->r->hset($field,$value);
    }
    
    /**
     * 
     * @return int
     */
    public function hlen():int{
        return $this->r->hlen();
    }
}
