<?php namespace SiTEL\Api\Oberon;
/**
 * CLient to call DemonHead
 * 
 *
 * @author Itay Moav
 * @date 2020-12-09
 */
class DemonHead extends \SiTEL\Api\Oberon\ClientWrapper{
	
    
    public function __construct(bool $should_retry=true,int $no_of_retries=5,int $wait_for_in_seconds=10){
        $this->forever_headers = [
            'User-Agent'    => 'DemonHead',
            'Content-type'  => 'application/json'
        ];
        parent::__construct(app_env()['external sources']['demonhead']['endpoint_url'],$should_retry,$no_of_retries,$wait_for_in_seconds);
    }
    
    /**
     * @param string $request_path
     * @param array<string, string> $resource_identifier
     * @param \stdClass $insert_data
     * @return \stdClass|NULL
     */
    public function write(string $request_path,array $resource_identifier,\stdClass $insert_data):?\stdClass{
        $url = "{$this->base_url}{$request_path}" . $this->resource_identifier_to_url($resource_identifier);
        $request = new \SiTEL\Api\Oberon\Request($url,\SiTEL\Api\Oberon\Request::METHOD__POST,$this->forever_headers,$insert_data);
        return $this->send($request)->body;
    }

    /**
     * Handle updates - overwrites papa
     * @param string $request_path
     * @param array<string, string> $resource_identifier
     * @param \stdClass $update_data
     * @return \stdClass|NULL
     */
    public function update(string $request_path,array $resource_identifier,\stdClass $update_data):?\stdClass{
        $url = "{$this->base_url}{$request_path}" . $this->resource_identifier_to_url($resource_identifier);
        $request = new \SiTEL\Api\Oberon\Request($url,\SiTEL\Api\Oberon\Request::METHOD__PUT,$this->forever_headers,$update_data);
        return $this->send($request)->body;
    }
	
    
    /**
     * I use read instead of GET as PHP wont allow overloading methods
     * @param string $request_path
     * @param array<string, string> $resource_identifier
     * @return \stdClass|NULL
     */
	public function read(string $request_path, array $resource_identifier=[]):?\stdClass{
	    $url = "{$this->base_url}{$request_path}" . $this->resource_identifier_to_url($resource_identifier);
	    $request = new \SiTEL\Api\Oberon\Request($url,\SiTEL\Api\Oberon\Request::METHOD__GET,$this->forever_headers);
	    return $this->send($request)->body;
	}
	
	/**
	 * Wrapper for generic REST delete for the Emerald API
	 * 
	 * @param string $request_path
	 * @param array<string, string> $resource_identifier
	 * @return \stdClass|NULL TODO decide what to do here.
	 */
	public function remove(string $request_path,array $resource_identifier):?\stdClass{
	    $url = "{$this->base_url}{$request_path}" . $this->resource_identifier_to_url($resource_identifier);
	    $request = new \SiTEL\Api\Oberon\Request($url,\SiTEL\Api\Oberon\Request::METHOD__DELETE,$this->forever_headers);
	    return $this->send($request)->body;
	}
	
	/**
	 * translate array to url string + add the caller id
	 * 
	 * @param array<string, string> $resource_identifier
	 * @return string
	 */
	private function resource_identifier_to_url(array $resource_identifier):string{
		$params = 'callerid/' . \User_Current::id();
		foreach($resource_identifier as $k=>$v){
			$params .="/{$k}/{$v}";
		}
		return $params;
	}
}
