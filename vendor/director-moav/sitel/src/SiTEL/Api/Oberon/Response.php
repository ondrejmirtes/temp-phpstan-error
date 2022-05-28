<?php namespace SiTEL\Api\Oberon;
/**
 *
 * @author itay
 *
 */
class Response{
    /**
     * 
     * @var ?\stdClass
     */
    public ?\stdClass $body;
    /**
     * 
     * @var array<string, mixed>
     */
    public array $headers;
    /**
     * 
     * @var int
     */
    public int $code;
    /**
     * 
     * @var string
     */
    public string $codePhrase;
    
    /**
     * @param \Psr\Http\Message\ResponseInterface $Response (\GuzzleHttp\Psr7\Response)
     */
    public function __construct(\Psr\Http\Message\ResponseInterface $Response){
        $this->parse($Response);
    }
    
    /**
     * 
     * @param \Psr\Http\Message\ResponseInterface $Response (\GuzzleHttp\Psr7\Response)
     * @return void
     */
    private function parse(\Psr\Http\Message\ResponseInterface $Response):void{
        $raw_body = $Response->getBody()->getContents();
        //dbgr('RAW BODY RETURNED',$raw_body);
        $body = null;
        if(trim($raw_body)){
            $body = json_decode($raw_body);
            if(! $body){//decode failed
                $body = null;
                \error('Failed decoding response ' . print_r($raw_body,true));

            }elseif(is_array($body)){
                $body = \SiTEL\array_to_object($body);
            }
        }

        $this->body = $body;
        $this->code = $Response->getStatusCode();
        $this->codePhrase = $Response->getReasonPhrase();
        $this->headers    = $Response->getHeaders();
    }
}
