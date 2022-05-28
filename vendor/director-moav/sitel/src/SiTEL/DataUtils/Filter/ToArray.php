<?php namespace SiTEL\DataUtils\Filter;
/**
 * Cast the value to array
 * @author prestton
 */
class ToArray implements i{
    /**
     * @return array<int, mixed>
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\i::filter()
     */
    public function filter($data):array{
        return (array)$data;
    }
}