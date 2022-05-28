<?php namespace SiTEL\DataUtils\Filter;
/**
 * Formats h:i am/pm
 * @author naghmeh
 */
class TimeHIAP implements iString {
   /**
    * 
    * {@inheritDoc}
    * @see \SiTEL\DataUtils\Filter\i::filter()
    */
    public function filter($data):string {
        if(!$data){
            return '';
        }
        return date('h:i A', strtotime($data));
    }
}