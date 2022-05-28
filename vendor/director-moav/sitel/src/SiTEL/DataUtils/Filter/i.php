<?php namespace SiTEL\DataUtils\Filter;
/**
 * Implement this, and u can be a filter
 *
 * @author Itay
 */
interface i{
    /**
     * @param mixed $data
     * @return mixed
     */
	public function filter($data);
}
