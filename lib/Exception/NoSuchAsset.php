<?php
class Exception_NoSuchAsset extends Exception{
	public function __construct($asset_id){
		$msg = "An unknown asset ID was requested [{$asset_id}].";
		parent::__construct($msg,ERRORCODE::UNKNOWN_ASSET);
	}
}
