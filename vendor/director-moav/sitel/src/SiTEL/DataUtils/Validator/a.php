<?php namespace SiTEL\DataUtils\Validator;
/**
 * I could not find an easy way to separate view (language) from server functionality = Sorry
 *
 * @author itaymoav
 */
abstract class a{
    /**
     * @var string
     */
	protected string $message = '';
	/**
	 * @var array<mixed>
	 */
    protected array	$params;
    
    /**
     * @param string $overwrite_message
     * @param array<mixed> $elm_specific_params
     */
	public function __construct(string $overwrite_message='',array $elm_specific_params = []){
		$this->message = $overwrite_message?:$this->message;
		$this->params  = $elm_specific_params;
	}
	
	/**
	 * @return string
	 */
	public function message():string{
		return $this->message;
	}
	
	/**
	 * @return array<mixed> params
	 */
	public function params():array{
		return $this->params;
	}
	
	/**
	 * @param mixed $value
	 * @return bool
	 */
	abstract public function validate($value):bool;
}
