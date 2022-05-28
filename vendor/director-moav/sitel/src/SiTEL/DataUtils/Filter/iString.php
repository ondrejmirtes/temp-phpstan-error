<?php namespace SiTEL\DataUtils\Filter;
/**
 * Implement this, and u can be a filter
 *
 * @author Itay
 */
interface iString{
    /**
     * 
     * @param mixed $data
     * @return string
     */
	public function filter($data):string;
}
