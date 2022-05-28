<?php namespace SiTEL\Api\Oberon;
/**
 * Proxy between External and Emerald.
 * Servs as client (sendToProxy method) and receive and proxy to Emerald (proxyToEmerald method)
 * Called inside the Emerald Client
 * 
 *
 * @author Itay Moav
 * @date 2020-12-10
 */
class EmeraldViaDemonhead{
    /**
     * Sends to DemonHead
     * TODO ************************************** this does not really need to be a CLient, It should use the DemonHead client when we create it ************************************
     * 
     * @param \SiTEL\Api\Oberon\Request $Request
     * @return \SiTEL\Api\Oberon\Response
     */
    public function sendToProxy(\SiTEL\Api\Oberon\Request $Request):\SiTEL\Api\Oberon\Response{
        $base_url = app_env()['external sources']['demonhead']['endpoint_url'];
        $url = "{$base_url}/emerald/proxy";
        $body = new \stdClass;
        $body->payload = \serialize($Request);
        $WrapperRequest = new \SiTEL\Api\Oberon\Request(
            $url, 
            'POST',
            [   'User-Agent'    => 'DemonHeadEmeraldProxy',
                'Content-type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(md5('babaganush-was-a-bad-boy') . ':' . md5('he-will-ssee-this-through'))
            ],
            $body);
        $C = new DemonHead;
        $WrappedResponse = $C->send($WrapperRequest);
        if(!isset($WrappedResponse->body->payload)){
            throw new \Exception('No payload received from Demon/Emerald');
        }
        return \unserialize($WrappedResponse->body->payload);
    }
}


//\GuzzleHttp\Exception\ConnectException