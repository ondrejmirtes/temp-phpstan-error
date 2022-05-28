<?php
class Exception_UnknownUser extends Exception{
	public function __construct($username,$overwrite_err_code = ERRORCODE::UNKNOWN_USER){
		$msg = "An unknown user requested [{$username}].";
		parent::__construct($msg,$overwrite_err_code);
	}
}
