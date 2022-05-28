<?php namespace SiTEL\DataSources\Redis;
/**
 * Talis Wrapper for the Redis connection
 * 
 *
 * @method int hexists(string $field)
 * @method mixed hget(string $field)
 * @method array hgetall()
 * @method int hsetnx(string $field,$value)
 * @method int hset(string $field,$value)
 * @method array hkeys()
 * @method int hlen()
 * @method mixed lrange(int $start,int $stop)
 * @method int rpush($value)
 * @method ?string rpop()
 * @method ?string lpop()
 * @method int lpush($value)
 * @method int llen()
 * @method int scard()
 * @method array smembers()
 * @method int sinterstore(\SiTEL\DataSources\Redis\aKeyBoss $intersec_this,\SiTEL\DataSources\Redis\aKeyBoss $intersec_with)
 * @method int sdiffstore(\SiTEL\DataSources\Redis\aKeyBoss $subtract_from_that, \SiTEL\DataSources\Redis\aKeyBoss $subtract_this)
 * @method int sismember($member)
 * @method ?string set($value)
 * @method ?string getset($value)
 * @method int incrby(int $amount)
 * @method int setnx($value)
 * @method void psetex(int $milliseconds,$value)
 * @method int strlen()
 *
 *
 *
 * @method int hexists(string $field)
 * @method mixed hget(string $field)
 * @method array hgetall()
 * @method int hsetnx(string $field,$value)
 * @method int hset(string $field,$value)
 * @method array hkeys()
 * @method int hlen()
 * @method mixed lrange(int $start,int $stop)
 * @method int rpush($value)
 * @method ?string rpop()
 * @method ?string lpop()
 * @method int lpush($value)
 * @method int llen()
 * @method int scard()
 * @method array smembers()
 * @method int sinterstore(\SiTEL\DataSources\Redis\aKeyBoss $intersec_this,\SiTEL\DataSources\Redis\aKeyBoss $intersec_with)
 * @method int sdiffstore(\SiTEL\DataSources\Redis\aKeyBoss $subtract_from_that, \SiTEL\DataSources\Redis\aKeyBoss $subtract_this)
 * @method int sismember($member)
 * @method ?string set($value, int $ttl=0)
 * @method ?string getset($value)
 * @method int incrby(int $amount)
 * @method int setnx($value)
 * @method void psetex(int $milliseconds,$value)
 * @method int strlen()
 * @method array keys()
 * @method void expire(int $seconds)
 * @method void pexpire(int $milliseconds)
 * @method int del()
 * @method int ttl()
 * @method int pttl()
 * @method string type()
 * @method int exists()
 *
 * @author Itay Moav
 */
class Client{
    /**
     * @var \Redis
     */
    static private \Redis $MyRedis;
    
    /**
     * @var int the db name, defaults to 0.
     */
    static private $current_MyRedis_db = 0;
    
    /**
     * @param array<string,mixed> $config
     * @var aKeyBoss key
     * @param \ZimLogger\Streams\aLogStream $logger
     * @param iDataBuilder $DataBuilder
     *
     * @return \SiTEL\DataSources\Redis\Client with a specific key
     */
    static public function getInstance(array $config,aKeyBoss $key, \ZimLogger\Streams\aLogStream $logger,iDataBuilder $DataBuilder=null):\SiTEL\DataSources\Redis\Client{
        if(!isset(self::$MyRedis)){
            self::$MyRedis = new \Redis;
            if(isset($config['advanced'])){
                $cn = $config['advanced'];
                $logger->debug("=================== Redis CONNECT [{$cn[0]}] ===================\n");
                self::$MyRedis->connect(
                    $cn[0],
                    $cn[1],
                    $cn[2],
                    $cn[3],
                    $cn[4],
                    $cn[5],
                    $cn[6]
                    );
            } else {
                $logger->debug("=================== Redis CONNECT [{$config['host']}] ===================\n");
                self::$MyRedis->connect($config['host']);
            }
            self::$MyRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }
        return new \SiTEL\DataSources\Redis\Client($key,$logger,$DataBuilder);
    }
    
    /**
     * Close Redis server and empty Redis instance
     * @param \ZimLogger\Streams\aLogStream $logger
     */
    static public function close(\ZimLogger\Streams\aLogStream $logger):void{
        $logger->debug("=================== disconnecting from redis ===================\n");
        if (isset(self::$MyRedis)) {
            self::$MyRedis->close();
            //self::$MyRedis = null; TOBEDELETED202144  replace setting to null value with unset
            unset(self::$MyRedis);
        }
    }
    
    /**
     *
     * @param aKeyBoss $key
     * @param \ZimLogger\Streams\aLogStream $logger TODO until we migrate all to Talis, we need to leave this typeless
     * @param iDataBuilder $DataBuilder
     */
    private function __construct(aKeyBoss $key,\ZimLogger\Streams\aLogStream $logger,iDataBuilder $DataBuilder=null){
        $this->key         = $key;
        $this->logger      = $logger;
        $this->DataBuilder = $DataBuilder;
    }
    
    /**
     * @var aKeyBoss the key this class will work on.
     */
    private $key,
    

    /**
     * @var ?iDataBuilder
     */
    $DataBuilder = NULL,
    
    /**
     * @var \ZimLogger\Streams\aLogStream
     */
    $logger
    ;

    
    /**
     * Makes sure we run the Redis command on the right DB.
     */
    protected function call_db_init():void{
        $this->logger->debug("=================== Redis SUNSET ===================\n");
        if(self::$current_MyRedis_db != $this->key->get_db()){
            dbgn('SELECT (new db) [' . $this->key->get_db() . ']');
            $res = self::$MyRedis->select($this->key->get_db());
            dbgn($res);
            self::$current_MyRedis_db = $this->key->get_db();
        }
    }
    
    /**
     * Wrapper for the redis class
     * To get dbg and error recovery
     *
     * @param string $method_name Redis class method name
     * @param array<string,string> $arguments
     * @return mixed
     */
    public function __call(string $method_name , array $arguments=[]){
        
        $this->call_db_init();
        
        $arguments = array_merge([$this->key->key_as_string()],$arguments);
        $this->logger->debug("===== Redis: {$method_name}\n" . print_r($arguments,true));
        $r = call_user_func_array([self::$MyRedis, $method_name], $arguments);
        $this->logger->debug("===== Redis: RESULTS FROM MY REDIS\n" . print_r($r,true));
        return $r;
    }
    
    /**
     * hardcore get
     * @return mixed $r
     */
    public function get(bool $with_build = true){
        $this->call_db_init();
        $this->logger->debug("===== Redis: GET\n");
        $r = self::$MyRedis->get($this->key->key_as_string());
        $this->logger->debug("===== Redis: RESULTS FROM MY REDIS\n" . print_r($r,true));
        
        if(!$r && $this->DataBuilder && $with_build){
            $this->logger->debug("===== Redis: Building data\n");
            $r = $this->DataBuilder->build();
            if($r) {
                $this->DataBuilder->ttl()?$this->set($r,$this->DataBuilder->ttl()):$this->set($r);
            }
        }
        return $r;
    }
    
    /**
     * The KEYS command, it is generic. Ovverides the __call
     *
     * @return array<int,string>
     */
    public function keys():array{
        $this->call_db_init();
        $pattern = $this->key->key_as_string();
        $this->logger->debug("===== Redis: KEYS {$pattern}\n");
        return self::$MyRedis->keys($pattern);
    }
    
    /**
     * IF data has to be serilized (primitives should not be, this take memeory and time)
     */
    public function serialize():void{
        self::$MyRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
    }
    
    /**
     * Prevent key data serialization
     */
    public function dontSerialize():void{
        self::$MyRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
    }
    
    /**
     * setOption wrapper
     *
     * @param int $key use \Redis constants
     * @param int $value  use \Redis constants
     */
    public function setOption(int $key, int $value):void{
        self::$MyRedis->setOption($key,$value);
    }
    
    /**
     * MUST STAY HERE, due to by ref not passing to __call properly
     *
     * Read Redis.io and redisphp to understand how pattern works
     *
     * @param integer $cursor (resource)
     * @param string|false $pattern
     *
     * @return array<int,string>|bool
     */
    public function sscan(&$cursor,$pattern=false){
        $this->call_db_init();
        
        $this->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);//do not bring empty results
        dbgr('Redis: SSCAN',['cursor' => $cursor,'pattern' => $pattern]);
        if($pattern !== false){
            return self::$MyRedis->sscan($this->key->key_as_string(),$cursor,$pattern);
        }else{
            return self::$MyRedis->sscan($this->key->key_as_string(),$cursor);
        }
    }
}
