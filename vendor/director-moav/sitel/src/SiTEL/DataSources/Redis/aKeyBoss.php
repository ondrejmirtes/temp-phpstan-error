<?php namespace SiTEL\DataSources\Redis;
/**
 * This class is where u construct the Client specific for a key 
 * with all it's constraints.
 * 
 * @author itaymoav
 */
abstract class aKeyBoss{
    
    /**
     * @var ?\SiTEL\DataSources\Redis\aClientMask
     */
    protected ?\SiTEL\DataSources\Redis\aClientMask $KeyClient = null;
    
    /**
     * @var char key field separator
     */
    const  FIELD_SEPARATOR = ':';
    
    public function __construct(string $var_key = '',iDataBuilder $builder = null){
        $this->var_key = $var_key;
        $this->builder = $builder;
    }
    
    /**
     * The key of current redis element
     * @var string
     */
    protected string $var_key = '';
    
    /**
     * Object that populate the Redis key if no data found
     * 
     * @var ?iDataBuilder
     */
    protected ?iDataBuilder $builder = null;

    /**
     * Gets a Redis client wrapped in a Mask
     * 
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    public function get_client():\SiTEL\DataSources\Redis\aClientMask{
        if($this->KeyClient){
            return $this->KeyClient;
        }
        $r = Client::getInstance(['host'=>$this->host()],$this,$this->logger(),$this->builder);
        if($this->should_i_serilize()){
            $r->serialize();
        } else {
            $r->dontSerialize();
        }
        $this->KeyClient = $this->get_redis_mask($r);
        return $this->KeyClient;
    }
    
    /**
     * Returns the db number, defaults to 0
     * 
     * @return int
     */
    public function get_db():int{
        return 0;
    }
    
    /**
     * set to true will cause data sent to Redis to be serilized.
     * more expensive.
     * 
     * @return bool
     */
    protected function should_i_serilize():bool{
        return false;
    }
    
    /**
     * Returns the host IP, I would create another abstract on
     * top of this class to fill all the default values for 
     * all abstract methods.
     * 
     * @return string
     */
    abstract protected function host():string;
    
    /**
     * Returns a logger instance. 
     * I suggest you create another abstract on top of this class 
     * to serve all classes in your specific project/sub project.
     * 
     * @return \ZimLogger\Streams\aLogStream
     */
    abstract protected function logger():\ZimLogger\Streams\aLogStream;
    
    /**
     * Restrict the key to be one type of Redis object
     * @param \SiTEL\DataSources\Redis\Client $r
     * @return \SiTEL\DataSources\Redis\aClientMask
     */
    abstract protected function get_redis_mask(\SiTEL\DataSources\Redis\Client $r):\SiTEL\DataSources\Redis\aClientMask;
    
	/**
	 * The namespace part of the key
	 * @return string
	 */
	abstract public function name_space():string;
	
	/**
	 * the name of the entity this key is all about
	 * For example, user cache in launcher module. User is the entity, launcher is the namespace.
	 * @return string
	 */
	abstract public function entity_name():string;
	
	/**
	 * The variable value of this entity.
	 * Key can be empty, in case this is a one thing entity, like a hash.
	 *
	 * @return string
	 */
	public function var_key():string{
	    return $this->var_key;
	}
	
	/**
	 * Returns a debug blurb about this key purpose.
	 * This is also for ease of finding all Redis related objects
	 * @return string
	 */
	abstract public function debug_redis_description():string;
	
	/**
	 * the key of this elelemnt
	 * @return string
	 */
	public function __toString(){
	    return $this->name_space() . self::FIELD_SEPARATOR . $this->entity_name() . self::FIELD_SEPARATOR . $this->var_key();
	}
	
	/**
	 * alias for __toString()
	 * @return string
	 */
	public function key_as_string():string{
	    return $this->__toString();
	}
}
