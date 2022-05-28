<?php namespace SiTEL\DataSources\Redis\Mask;

trait tList{
    /**
     * Restrict the key to be one type of Redis object
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    protected function get_redis_mask(\SiTEL\DataSources\Redis\Client $r):\SiTEL\DataSources\Redis\aClientMask{
        return new \SiTEL\DataSources\Redis\Mask\ListR($r);
    }
}

/**
 * (TODO This is partial functionality)
 * List data type
 * 
 * @author itay
 *
 */
class ListR extends \SiTEL\DataSources\Redis\aClientMask{
    
    /**
     * Returns the specified elements of the list stored at key. 
     * The offsets start and stop are zero-based indexes, with 0 being the first element of the list (the head of the list), 1 being the next element and so on.
     * These offsets can also be negative numbers indicating offsets starting at the end of the list. 
     * For example, -1 is the last element of the list, -2 the penultimate, and so on.
     * 
     * @param int $start
     * @param int $stop
     * @return mixed
     */
    public function lrange(int $start,int $stop){
        return $this->r->lrange($start,$stop);
    }
    
    /**
     * TODO in redis, this actuall supposed to get variable amount
     *      of input variables.
     * @param mixed $value
     * @return int
     */
    public function rpush($value):int{
        return $this->r->rpush($value);
    }
    
    /**
     * rpush an array
     * @param array<int,mixed> $value
     * @return int
     */
    public function rpush_array(array $value):int{
        return \call_user_func_array([$this->r,'rpush'],$value);
    }
    
    /**
     * Removes and returns the last element of the list stored at key.
     * 
     * @return ?string
     */
    public function rpop():?string{
        return $this->r->rpop();
    }
    
    /**
     * Removes and returns the first element of the list stored at key.
     * @return ?string
     */
    public function lpop():?string{
        return $this->r->lpop();
    }
    
    /**
     * Insert all the specified values at the head of the list stored at key. 
     * If key does not exist, it is created as empty list before performing the push operations. 
     * When key holds a value that is not a list, an error is returned.
     * It is possible to push multiple elements using a single command call just specifying multiple arguments at the end of the command. 
     * Elements are inserted one after the other to the head of the list, from the leftmost element to the rightmost element.
     * So for instance the command LPUSH mylist a b c will result into a list containing c as first element, b as second element and a as third element.
     * 
     * @param mixed $value
     * @return int
     */
    public function lpush($value):int{
        return $this->r->lpush($value);    
    }
    
    /**
     * lpush an array
     * @param array<int,mixed> $value
     * @return int
     */
    public function lpush_array(array $value):int{
        return \call_user_func_array([$this->r,'lpush'],$value);
    }
    
    /**
     * Returns the length of the list stored at key. 
     * If key does not exist, it is interpreted as an empty list and 0 is returned. 
     * An error is returned when the value stored at key is not a list.
     * @return int
     */
    public function llen():int{
        return $this->r->llen();
    }
    
    
}
