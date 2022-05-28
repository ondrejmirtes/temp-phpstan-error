<?php namespace SiTEL\DataSources\Session;
/**
 * Apply session handling to any class who wishes to
 *
 * @author itaymoav
 */
trait tHelper{
	/**
	 * @var Client
	 */
	protected $SessionCaching=null; //Session object for this model
	
	/**
	 * @param string $namespace
	 */
	protected function setSession(string $namespace):void{
		$this->SessionCaching=new Client($namespace);
		//  return $this;  TOBEDELETED202144  PHPStan trait issues with returning $this in traits.  Inconsistent return values.  Removing Chaining
	}
	
	/**
	 * destroys the session namespace
	 *
	 */
	protected function destroySession():void{
		$this->SessionCaching->destroy();
		//  return $this;  TOBEDELETED202144  PHPStan trait issues with returning $this in traits.  Inconsistent return values.  Removing Chaining
	}
	
	/**
	 * Sets all the session
	 * @param array<string,mixed> $value
	 *
	 * @return \SiTEL\DataSources\Session\Client
	 */
	protected function setSessionAllValue(array $value){
		return $this->SessionCaching->setAll($value);
	}
	
	/**
	 * set value in session with key
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function setSessionValue(string $key, $value):void{
		$this->SessionCaching->set($key, $value);
		//  return $this;  TOBEDELETED202144  PHPStan trait issues with returning $this in traits.  Inconsistent return values.  Removing Chaining
	}
	
	/**
	 * return the choosen value
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	protected function getSessionValue(string $key, $default=null){
		return $this->SessionCaching->get($key, $default);
	}
	
	/**
	 * get the namespace from session
	 *
	 * @return array<string,array>
	 */
	protected function getSessionAllValue():array{
		return $this->SessionCaching->getAll();
	}
}
