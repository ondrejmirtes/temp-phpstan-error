<?php namespace SiTEL\DataUtils\Filter;
/**
 * Change any value to a numeric value, if it has one in it, or zero
 * @author Itay
 */
class ToNumeric implements i{
    /**
     * 
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\i::filter()
     */
    public function filter($data):int{
        return (int)$data;
    }
}