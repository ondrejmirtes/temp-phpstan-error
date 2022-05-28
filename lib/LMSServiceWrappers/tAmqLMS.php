<?php
/**
 * For subscribers and publishers that connect to the LMS activeMQ on web3
 * @author itay
 *
 */
trait LMSServiceWrappers_tAmqLMS{
    static public function get_wrapped_client():\SiTEL\DataSources\ActiveMQ\Queue{
        
        $env = app_env()['database']['activeMQ'];
        $options = ['host'      => $env['host'],
                    'port'      => $env['port'],
                    'logger'    => \ZimLogger\MainZim::$CurrentLogger
        ];
        dbgr('CONNECTION OPTIONS',$options);
        return self::get_client($options);
    }
    
    /**
     * sugar
     * @return \SiTEL\DataSources\ActiveMQ\Publisher
     */
    static public function get_wrapped_publisher():\SiTEL\DataSources\ActiveMQ\Publisher{
        return self::get_wrapped_client();
    }
    
    /**
     * sugar
     * @return \SiTEL\DataSources\ActiveMQ\Publisher
     */
    static public function get_wrapped_subscriber():\SiTEL\DataSources\ActiveMQ\Subscriber{
        return self::get_wrapped_client();
    }
}

