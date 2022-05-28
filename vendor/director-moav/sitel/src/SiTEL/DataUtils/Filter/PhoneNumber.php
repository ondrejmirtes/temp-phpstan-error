<?php namespace SiTEL\DataUtils\Filter;
/**
 * Change any value to a numeric value, if it has one in it, or zero
 * @author Itay
 */
class PhoneNumber implements iString{
    /**
     * 
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\i::filter()
     */
    public function filter($data):string{
        return str_replace([' ','.','-'],'',$data);
    }
}