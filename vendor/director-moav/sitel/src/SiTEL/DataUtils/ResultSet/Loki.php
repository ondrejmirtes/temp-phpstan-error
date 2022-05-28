<?php namespace SiTEL\DataUtils\ResultSet;
/**
 * A fake dataset storage unit.
 * Sometimes u want to use aeon loopers just to work, no need to store anything.
 * Pass them this
 * 
 * It will store the pager and the filter though
 * 
 * @author Itay Moav
 *
 */
class Loki implements i{
    private   	?\SiTEL\DataUtils\aPager $pager = null;
	private     \SiTEL\DataSources\Sql\aQueryFilter $QueryFilter;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::setFilter()
	 */
	public function setFilter(\SiTEL\DataSources\Sql\aQueryFilter $QueryFilter):\SiTEL\DataSources\Sql\aQueryFilter{
		$this->QueryFilter = $QueryFilter;
		return $this->QueryFilter;
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::getFilter()
	 */
	public function getFilter():\SiTEL\DataSources\Sql\aQueryFilter{
		return $this->QueryFilter;
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::setData()
	 */
	public function setData($v):void{
		//buhahahah I do nothing :-DDDDD
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::getData()
	 */
	public function getData():array{
		//buhahahah I do nothing :-DDDDD
		return [];
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::addLine()
	 */
	public function addLine($row):array{
		//buhahahah I do nothing :-DDDDD
		return $row;
	}
    /**
     * @param \SiTEL\DataUtils\aPager $Pager
     */
	public function setPager(\SiTEL\DataUtils\aPager $Pager):i{
		$this->pager = $Pager;
		return $this;
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::getPager()
	 */
	public function getPager():\SiTEL\DataUtils\aPager{
	    if(!$this->pager){
	        throw new \Exception('Getting pager without setting pager first.');
	    }
		return $this->pager;
	}
	
	//--------------- ITERATOR INTERFACE -----------------------------------
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::rewind()
	 * @return void
	 */
	public function rewind()
	{
		
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::current()
	 * @return mixed
	 */
	public function current()
	{
		return [];
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::key()
	 * @return mixed
	 */
	public function key()
	{
		return [];
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::next()
	 * @return void
	 */
	public function next():void
	{
		
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::valid()
	 * @return bool
	 */
	public function valid()
	{
		return false;
	}
}