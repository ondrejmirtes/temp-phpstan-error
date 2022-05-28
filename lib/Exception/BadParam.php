<?php
class Exception_BadParam extends Exception{
	public $param_name = '';
	public function __construct($param_name){
		$this->param_name = $param_name;
		parent::__construct("Malformed Param [{$param_name}]");
	}
	
}
