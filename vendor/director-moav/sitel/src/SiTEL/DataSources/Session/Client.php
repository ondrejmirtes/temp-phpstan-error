<?php namespace SiTEL\DataSources\Session;
/**
 * @author Itay Moav <itay.malimovka@gmail.com>
 * @Reviewer
 * @version 1
 * @license MIT - Opensource (File taken from PHPancake project)
 * @implements \Iterator<string,array>
 * 
 * 
 * 
 * Methods:
 * 
 * [static]
 * start		:		Starts the session mechanizem
 * close		:		Closes the session (and causing write)
 * regenerate	:		Regenrate the session
 * destroy		:		Destroys current session and regenrates a new session ID (and returns it)
 * 
 * [Instance]
 * constructor	:		Init a session class with a namespace, not touching the session itself
 * get			:		Get a session value from current namespace according to input key.
 * set			:		Creates a new entry in current namespace with key/value input
 * emptyAll		:		Erases current namespace
 * isEmpty		:		Checks if curent namespace has values or not
 * getAll		:		Get an array of all the values of the current namespace
 * setAll		:		Sets the values of current namespace with an array
 * 
 * [Iterator]	:		this class can be iterated over (iterates on the current namespace)
 */
class Client implements \Iterator{
	/**
	 * 
	 * @var bool $startFlag
	 */
    static private $startFlag=false;
    /**
     * 
     * @var string $appNamespace
     */
	static private $appNamespace='__LMS2';
	
	/**
	 * Expects a clear string as key. This function will hush it.
	 * 
	 * @param string $id
	 * @return string|false
	 */
	static public function setSessId($id){
	    return session_id($id);
	}
	
	/**
	 * 
	 * @return string|false
	 */
	static public function getSessId(){
	    return session_id();
	}
	
	static public function start():void{
		if(self::$startFlag) return;
		session_start();
		if(!isset($_SESSION[self::$appNamespace])){
			$_SESSION[self::$appNamespace]= null;
		}
		self::$startFlag=true;
	}
	
	static public function close():void{
		session_write_close();
		self::$startFlag=false;
	}
	
	static public function anihilate():void{
		$_SESSION[self::$appNamespace]= null;
		session_regenerate_id(true);
	}
	
	
	static public function regenrate():void{
		session_regenerate_id(true);
	}
	/**
	 * Both static and not static. If used in the static way, will kill ALL session data, otherwise, only the namesapce
	 * @return \SiTEL\DataSources\Session\Client
	 */
	public function destroy():\SiTEL\DataSources\Session\Client{
		\dbgn('DESTROYING SESSION: ' . self::$appNamespace . ' ---- ' . $this->nameSpace);
		unset($_SESSION[self::$appNamespace][$this->nameSpace]);
		return $this;
	}//EOF destroy
	/**
	 * 
	 * @var string $nameSpace
	 */
	private $nameSpace='';
	
	public function __construct(string $name_space){
		$this->nameSpace=$name_space;
	}
	
	/**
	 * @param string $index is the key name
	 * @param mixed $default is the default value to fetch back in case this key does not exists. Defaults to NULL
	 * @return mixed
	 */
	public function get(string $index,$default=null){
		$ret=(isset($_SESSION[self::$appNamespace][$this->nameSpace][$index]))?
			 ($_SESSION[self::$appNamespace][$this->nameSpace][$index]):
			 $default;
		return $ret;
	}
	/**
	 * Sets a value in the session
	 *
	 * @param string $index
	 * @param mixed $value
	 * 
	 * @return mixed $value
	 */
	public function set(string $index,$value){
		$_SESSION[self::$appNamespace][$this->nameSpace][$index]=$value;
		
		//return after filter applied
		return $value;
	}

	/**
	 * 
	 * @param string $index
	 * @return mixed
	 */
	public function emptyValue(string $index) {
		return $this->set($index,null);
	}

    /**
     * Clears all contents from current namespace
     *
     * @return \SiTEL\DataSources\Session\Client this instance for chaining and PONIES!!!
     */
	public function emptyAll():\SiTEL\DataSources\Session\Client{
    	if(isset($_SESSION[self::$appNamespace][$this->nameSpace])){
    		$_SESSION[self::$appNamespace][$this->nameSpace] = null;
    	}
    	return $this;
    }
	
	/**
     * Returns true if and only if storage is empty
     *
     * @return boolean true if empty | false if full
     */
    public function isEmpty():bool{
    	return !isset($_SESSION[self::$appNamespace][$this->nameSpace]);
    }

    /**
     * Returns the entire namespace
     *
     * @return array<string,array>
     */
    public function getAll():array{
		if(isset($_SESSION[self::$appNamespace][$this->nameSpace])){
    		return $_SESSION[self::$appNamespace][$this->nameSpace];
    	}
    	return [];
    }

    /**
     * Rewrite entire name space with input array
     *
     * @param  array<string,string> $contents
     * @return \SiTEL\DataSources\Session\Client
     */
    public function setAll(array $contents):\SiTEL\DataSources\Session\Client{
   		$_SESSION[self::$appNamespace][$this->nameSpace]=$contents;    		
    	return $this;
    }

    /* ------------------------------- interface implementation Iterator ------------------------------- */
    /**
     * @return mixed
     */
    public function current(){
    	return current($_SESSION[self::$appNamespace][$this->nameSpace]);
    }
    /**
     * @return void
     */
    public function next(){
    	next($_SESSION[self::$appNamespace][$this->nameSpace]);
    }
    /**
     * @return void
     */
    public function rewind(){
    	reset($_SESSION[self::$appNamespace][$this->nameSpace]);
    }
    
    /**
     * @return mixed
     */
    public function key() {
        return key($_SESSION[self::$appNamespace][$this->nameSpace]);
        
    }
    /**
     * @return bool
     */
    public function valid() {
        $var = (false !== $this->current());
        return $var;
    }
    
}//EOF CLASS