<?php
class Exception_MissingParam extends Exception{
	public $param_name = '';
	public function __construct($param_name){
		$this->param_name = $param_name;
		parent::__construct("Missing [{$param_name}]");
	}
	
}
