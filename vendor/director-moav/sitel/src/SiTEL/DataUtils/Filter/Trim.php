<?php namespace SiTEL\DataUtils\Filter;
/**
 * Trim an explicit string field
 * @author Itay
 */
class Trim implements iString{
    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\iString::filter()
     */
    public function filter($data):string{
        return trim($data);
    }
}
