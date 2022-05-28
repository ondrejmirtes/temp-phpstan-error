<?php
/**
 * Return responses as JSon
 * 
 * This is a more free form response type 
 * than the Ajax response type
 */
class Response_Json extends Response_Abstract{
    

    const	RESPONSE_SUCCESS	=	'success',	//to manage BL return statuses, as opposed to sofware http response codes.
            RESPONSE_FAILURE	=	'failure',
            HTTP_CODE__NOT_FOUND_ERROR          = ' 404 Not Found Error',
            HTTP_CODE__INTERNAL_SERVER_ERROR    = ' 500 Internal Server Error'
    ;
        
	protected $headers		 = ['Content-Type: application/json; charset=utf-8'];
	/**
	 * @var stdClass
	 */
	private   $response_data    = null,
	          $response_status  = '', // Either  RESPONSE_SUCCESS or RESPONSE_FAILURE
	          $response_comment = ''
    ;
	
    public function setData(stdClass $Res):Response_Json{
		$this->response_data = $Res;
		$this->response_data->response_status  = $this->response_status;
		$this->response_data->response_comment = $this->response_comment;
		return $this;
	}
	
	/**
	 * Status to appear on the response
	 * @param string $response_status
	 * @param string $response_comment
	 * @return Response_Json
	 */
	public function setResponseStatus(string $response_status,string $response_comment=''):Response_Json{
	    switch($response_status){
	        case self::RESPONSE_SUCCESS:
	            $this->response_status = self::RESPONSE_SUCCESS;
	            break;
	            
	        case self::RESPONSE_FAILURE:
	            $this->response_status = self::RESPONSE_FAILURE;
	            break;
	            
	        case self::HTTP_CODE__NOT_FOUND_ERROR:
	            $this->response_status = self::RESPONSE_FAILURE;
	            $this->headers[]   = $_SERVER['SERVER_PROTOCOL'] . self::HTTP_CODE__NOT_FOUND_ERROR;
	            break;
	            
	        default:
	            $this->response_status = '';
	            warning("Illegal response status supplied [{$response_status}], It is being ignored!");
	            break;
	    }
	    
	    $this->response_comment = $response_comment;
	    return $this;	    
	}
	
	/**
	 * Echo headers
	 * Echo layouts
	 * Echo view file itself
	 */
	public function render():Response_Json{
	    //sometimes all we need back is the status
	    if(!$this->response_data){
	        $this->setData(new stdClass);
	    }
	    
		//headers
		foreach($this->headers as $header) header($header);
		
		//body
	    echo json_encode($this->response_data);
		return $this;
	}
}

