<?php namespace SiTEL\DataSources\ActiveMQ;
trait tQueue{
    /**
     * 
     * @return string
     */
    protected function type():string{
        return Queue::QUEUE;
    }
}
