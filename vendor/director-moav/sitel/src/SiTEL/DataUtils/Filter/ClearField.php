<?php namespace SiTEL\DataUtils\Filter;
/**
 *
 * Replace data to empty string
 * @author Itay
 */
class ClearField implements iString{
    /**
     * 
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\iString::filter()
     */
    public function filter($data):string{
        return '';
    }
}