<?php namespace SiTEL\DataUtils\ResultSet;
/**
 * The basic data set storage unit.
 * 
 * @author itaymoav
 *
 */
class Eve implements i{
	/**
	 * The actual collection of data
	 * @var array<int,array> $data
	 */
	protected	$data	= [];
	
	/**
	 * @var \SiTEL\DataSources\Sql\aQueryFilter $Queryfilter
	 */
	protected \SiTEL\DataSources\Sql\aQueryFilter $Queryfilter;
	
	/**
	 * @var ?\SiTEL\DataUtils\aPager 
	 */
	protected ?\SiTEL\DataUtils\aPager $pager = null;
	
	/**
	 * @var BL_Header_Abstract
	 */
	 //NOT IMPLEMENTED YET $header=null;
	
	 /**
	 * @var int
	 * Real page size
	 */
	protected int $count	= 0;
	/**
	 * What ever
	 * @var array<string,mixed> $additional_params
	 */
	protected array $additional_params = [];
	
	/**
	 * @param BL_Header_Abstract $header
	 */
	/**TODO
	public function setHeader(BL_Header_Abstract $header){
		$this->header = $header;
	}*/
	
	/**
	 * @return BL_Header_Abstract
	 */
	/**TODO
	public function getHeader(){
		return $this->header;
	}
	*/
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::setData()
	 * @param array<int,array> $v
	 */
	public function setData($v):void{
		$this->data = $v;
		$this->count = count($v);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::getData()
	 * @return array<int,array>
	 */
	public function getData():array{
		return $this->data;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::setFilter()
	 */
	public function setFilter( \SiTEL\DataSources\Sql\aQueryFilter $QueryFilter):\SiTEL\DataSources\Sql\aQueryFilter{
		$this->Queryfilter = $QueryFilter;
		return $QueryFilter;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::getFilter()
	 * @return \SiTEL\DataSources\Sql\aQueryFilter
	 */
	public function getFilter():\SiTEL\DataSources\Sql\aQueryFilter{
		return $this->Queryfilter;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\ResultSet\i::addLine()
	 * 
	 * @param array<string,string> $row
	 */
	public function addLine(array $row):array{
		$this->data[]= $row;
		$this->count++;
		return $row;
	}
	
	/**
	 * 
	 * @param string $k
	 * @param mixed $v
	 */
	public function setAdditionalParams(string $k,$v):void{
		$this->additional_params[$k] = $v;
	}
	
	
	/**
	 * 
	 * @param string $k
	 * @return mixed
	 */
	public function getAdditionalParams(string $k){
		return $this->additional_params[$k];
	}
	
	/**
	 * Getter for number of lines
	 *
	 * @return int
	 */
	public function c():int{
		return $this->count;
	}
	
	/**
	 * @param \SiTEL\DataUtils\aPager $Pager
	 * @return $this
	 */
	public function setPager(\SiTEL\DataUtils\aPager $Pager):i{
		$this->pager = $Pager;
		return $this;
	}
	
	/**
	 * @return \SiTEL\DataUtils\aPager $Pager
	 */
	public function getPager():\SiTEL\DataUtils\aPager{
	    if(!$this->pager){
	        // If no pager throw an error
	        throw new \Exception('Getting pager without setting pager first.');
	    }
		return $this->pager;
	}
	
	//--------------- ITERATOR INTERFACE -----------------------------------
	public function rewind():void
	{
		reset($this->data);
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::current()
	 * @return mixed
	 */
	public function current()
	{
		return current($this->data);
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::key()
	 * @return mixed
	 */
	public function key()
	{
		return key($this->data);
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Iterator::next()
	 * @return void
	 */
	public function next():void
	{
		next($this->data);
	}
	
	public function valid():bool
	{
		$key = key($this->data);
		$var = ($key !== NULL && $key !== FALSE);
		return $var;
	}
}
