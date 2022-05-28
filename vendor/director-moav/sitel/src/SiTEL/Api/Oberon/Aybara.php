<?php namespace SiTEL\Api\Oberon;
/**
 * Client Wrapper for Aybara
 * @author Itay Moav
 */
class Aybara extends \SiTEL\Api\Oberon\ClientWrapper{
    CONST
        URL__CORWIN_STATELESS_POST  = '/corwin/stateless/post',
        
        ACTION__ERRORPROXY          = 'ErrorProxy',
        ACTION__CLOUDCME            = 'CloudCMECompletions',
        ACTION__FEED_PUSHER         = 'FeedPusher'
    ;
    
    /**
     * Don't change this!
     * @var boolean
     */
    protected bool $use_aybara_as_proxy  = false;
    
    /**
     * @var float
     */
    protected float $connection_timeout  = 10;
    
    /**
     * Constructor
     * Set aybara base url
     */
    public function __construct(){
        parent::__construct(app_env()['external sources']['aybara']['endpoint_url']);
    }
    
    /**
     * Request for corwin stateless post
     * @param string $action
     * @param array<string, mixed> $data
     * @param string $method
     * @param array<string,string> $headers
     * @return \SiTEL\Api\Oberon\Response
     */
    public function send_corwin_stateless_action(string $action, array $data, string $method = \SiTEL\Api\Oberon\Request::METHOD__POST, array $headers = []) : \SiTEL\Api\Oberon\Response{
        $req = new \SiTEL\Api\Oberon\Request($this->base_url . self::URL__CORWIN_STATELESS_POST, $method, $headers);
        
        $body = new \stdClass;
        $body->action = $action;
        $body->params = $data;
        
        $req->body($body);
        return $this->send($req);
    }
    
    /**
     * @param string $type
     * @param array<mixed> $body
     * @return \SiTEL\Api\Oberon\Response
     */
    public function octoposim(string $type,array $body) : \SiTEL\Api\Oberon\Response{
        $json_body = json_encode($body);
        if(!$json_body){
            throw new \Exception('Failed json encoding body ' . print_r($body,true));
        }
        $enc_body = base64_encode($json_body);
        return $this->send_corwin_stateless_action('OctoposimEvent',['subject'=>$type,'body'=>$enc_body]);
    }
}
