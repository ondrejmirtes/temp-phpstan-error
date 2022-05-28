<?php namespace SiTEL\DataUtils\ResultSet;
/**
 * @author itay moav
 * 
 * @extends \Iterator<string,array>
 */
interface i extends \Iterator{
    /**
     * @param array<int,array> $v
     */
	public function setData(array $v):void;
	/**
	 * @return array<int,array>
	 */
	public function getData():array;
	/**
	 * @param array<string,string> $row
	 * @return array<string,string>
	 */
	public function addLine(array $row):array;
	/**
	 * @param \SiTEL\DataUtils\aPager $Pager
	 * @return $this<string,array>
	 */
	public function setPager(\SiTEL\DataUtils\aPager $Pager):\SiTEL\DataUtils\ResultSet\i;
	/**
	 * @return \SiTEL\DataUtils\aPager
	 */
	public function getPager():\SiTEL\DataUtils\aPager;
	/**
	 * @param \SiTEL\DataSources\Sql\aQueryFilter $QueryFilter
	 * @return \SiTEL\DataSources\Sql\aQueryFilter
	 */
	public function setFilter(\SiTEL\DataSources\Sql\aQueryFilter $QueryFilter):\SiTEL\DataSources\Sql\aQueryFilter;
	/**
	 * @return \SiTEL\DataSources\Sql\aQueryFilter
	 */
	public function getFilter():\SiTEL\DataSources\Sql\aQueryFilter;
	
	/**
	 * @return mixed
	 * {@inheritDoc}
	 * @see Iterator::current()
	 */
	public function current();
}
