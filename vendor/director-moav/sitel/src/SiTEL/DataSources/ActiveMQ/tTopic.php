<?php namespace SiTEL\DataSources\ActiveMQ;
trait tTopic{
    protected function type(){
        return Queue::TOPIC;
    }
}
