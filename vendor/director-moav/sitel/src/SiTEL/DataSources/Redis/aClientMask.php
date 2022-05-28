<?php namespace SiTEL\DataSources\Redis;

abstract class aClientMask{
    /**
     * @var Client
     */
    protected $r;
    
    /**
     * @param Client $r
     */
    public function __construct(Client $r){
        $this->r = $r;
    }
    
    /**
     * Depending on the variable part of the key, suggested to use '*' to initialize a key
     * @return array<int,string>
     */
    public function keys():array{
        return $this->r->keys();
    }
    
    /**
     *
     * @param int $seconds
     */
    public function expire(int $seconds):void{
        $this->r->expire($seconds);
    }
    
    /**
     *
     * @param int $milliseconds
     */
    public function pexpire(int $milliseconds):void{
        $this->r->pexpire($milliseconds);
    }
    
    /**
     *
     * @return int
     */
    public function del():int{
        return $this->r->del();
    }
    
    /**
     * @return int TTL in seconds
     */
    public function ttl():int{
        return $this->r->ttl();
    }
    
    /**
     * @return int TTL in milliseconds,
     */
    public function pttl():int{
        return $this->r->pttl();
    }
    
    /**
     * Redis object type
     * @return string
     */
    public function type():string{
        return $this->r->type();
    }
    
    /**
     * @return int 1 key exists 0 does not
     */
    public function exists():int{
        return $this->r->exists();
    }
    
}
