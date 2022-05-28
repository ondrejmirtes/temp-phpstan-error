<?php namespace SiTEL\Api\Oberon;
/**
 * CLient to call Emerald
 * migrateed to the new better abstracted system that uses Guzzle
 * @date 20191119
 *
 * @author Itay Moav
 *
 */
class Emerald extends \SiTEL\Api\Oberon\ClientWrapper{
	
    /**
     * 
     * @var bool
     */
    private bool $use_demonhead_as_proxy = false;
    
    public function __construct(bool $use_demonhead_as_proxy = false){
        $this->forever_headers = [
            'User-Agent'    => 'LMS_Emerald',
            'Content-type'  => 'application/json'
        ];
        $this->use_demonhead_as_proxy = $use_demonhead_as_proxy;
        parent::__construct(app_env()['external sources']['emerald']['endpoint_url'],true,5, 10);
    }

    /**
     * To use the Demonhead wrapping
     * 
     * @param \SiTEL\Api\Oberon\Request $request
     * @return \SiTEL\Api\Oberon\Response
     */
    public function send(\SiTEL\Api\Oberon\Request $request):\SiTEL\Api\Oberon\Response{
        if(!$this->use_demonhead_as_proxy){
            return parent::send($request);
        }

        $c = new EmeraldViaDemonhead;
        $res = $c->sendToProxy($request);
        return $res;
    }
    
    /**
     * @param string $request_path
     * @param array<string,string|int> $resource_identifier
     * @param \stdClass $insert_data
     * @return \stdClass|NULL
     */
    public function write(string $request_path,array $resource_identifier,\stdClass $insert_data):?\stdClass{
        $url = "{$this->base_url}{$request_path}/create/" . $this->resource_identifier_to_url($resource_identifier);
        $request = new \SiTEL\Api\Oberon\Request($url, 'POST',[],$insert_data);
        return $this->send($request)->body;
    }
    
    /**
     * Handle updates - overwrites papa
     * @param string $request_path
     * @param array<string,string|int> $resource_identifier
     * @param \stdClass $update_data
     * @return \stdClass|NULL
     */
    public function update(string $request_path,array $resource_identifier,\stdClass $update_data):?\stdClass{
        $url = "{$this->base_url}{$request_path}/update/" . $this->resource_identifier_to_url($resource_identifier);
        $request = new \SiTEL\Api\Oberon\Request($url,'PUT',[],$update_data);
        return $this->send($request)->body;
    }
	
    
    /**
     * I use read instead of GET as PHP wont allow overloading methods
     * @param string $request_path
     * @param array<string, string|int> $resource_identifier
     * @return \stdClass|NULL
     */
	public function read(string $request_path, array $resource_identifier=[]):?\stdClass{
	    $url = "{$this->base_url}{$request_path}/read/" . $this->resource_identifier_to_url($resource_identifier);
	    $request = new \SiTEL\Api\Oberon\Request($url,'POST',[]);
	    return $this->send($request)->body;
	}
	
	/**
	 * Wrapper for generic REST delete for the Emerald API
	 * 
	 * @param string $request_path
	 * @param array<string, string|int> $resource_identifier
	 * @return \stdClass|NULL TODO decide what to do here.
	 */
	public function remove(string $request_path,array $resource_identifier):?\stdClass{
	    $url = "{$this->base_url}{$request_path}/delete/" . $this->resource_identifier_to_url($resource_identifier);
	    $request = new \SiTEL\Api\Oberon\Request($url,'DELETE',[]);
	    return $this->send($request)->body;
	}
	
	/**
	 * translate array to url string + add the caller id
	 * 
	 * @param array<string, int|string> $resource_identifier
	 * @return string
	 */
	private function resource_identifier_to_url(array $resource_identifier):string{
		$params = 'callerid/' . \User_Current::id();
		foreach($resource_identifier as $k=>$v){
			$params .="/{$k}/{$v}";
		}
		
		//add a guid call signature TODO might need to make this more generic
		$ispy=uniqid();
		$params .="/ispy/{$ispy}";
		
		return $params;
	}
}
