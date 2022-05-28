<?php
/**
 * @author Itay Moav <2008>
 * @license MIT - Opensource (File taken from PHPancake project)
 * 
 * Will be used to define the pager interface 
 * 
 *
 * 
 * Methods:
 * 
 * getPageSize			:		Returns the page size
 * 
 * getCurrentPage		:		Returns the current page number that was fetched.
 * 
 * getCurrentPageTotal	:		Returns number of records in this page.
 * 
 * getTotal				:		Returns total number of records in this search.
 * 
 * getTotalPages		:		Returns the total number of pages in the system.
 * 
 * getPage				:		Returns the dataset of data
 * 
 * setCurrentPage		:		Sets the pager to the correct page to fetch.
 * 
 * setCountSql			:		Sets the counting mechanizem to a user supplied SQL, to be used if none simple SQL
 * 								are used, or in some cases of optimization
 */
abstract class Data_APager{
    use \SiTEL\DataSources\Session\tHelper;

	protected	$pageSize,					//Page size to show.
				$current_page,				//current page requested.
				$count			= false,	//Number of rows expected from this query.
				$currentPageTotal,			//Total entries in this page.
				$query,						//The query I need to page.
				$key,						//The key in the cache for the sql.
				$params
				
	;
				
	/**
	 * @return integer page size
	 */
	public function getPageSize(){
		return $this->pageSize;	
	}

	public function setPageSize($page_size){
		
		if($page_size <= 0){
			throw new Exception_BadParam('page_size:' . $page_size);
		}
		
		$this->pageSize=$page_size*1;
	}
	
	public function setCurrentPage($current_page){
		
		if($current_page <= 0){
			throw new Exception_BadParam('page:'.$current_page);
		}
		
		$this->current_page=($current_page-1)*1; //defaults to 0
		return $this;
	}

	/**
	 * Optional method to set manualy the count. Will save a query if there is pre knowledge of the count.
	 * Otherwise, will run a COUNT query once per new sql.
	 *
	 * @param integer $count
	 * @return Data_APager
	 */
	public function setCount($count){
		$this->count=$count*1; //*1 is to make it an int instead of a string
		$this->setSessionValue($this->key,$count);
		return $this;
	}

	public function getNextPageNumber() {
		$c=$this->current_page;
		$s=$this->pageSize;
		$cn=$this->count;
		if((($c+1)*$s)>=$cn) {
			return 0;
		}else{
			return (++$c);
		}
	}//EOF getNextPageNumber
	
	public function getBackPageNumber() {
		$c=$this->current_page;
		$s=$this->pageSize;
		$cn=$this->count;
	
		if($c<=0){
			return ((int)($cn/$s));
		}else{
			return (--$c);
		}
	}//EOF getBackPageNumber
	
	/**
	 * returns total entries in the query (without a limit)
	 * @return integer total entries
	 */
	public function getTotal() {
		return $this->count*1;
	}//EOF getTotal
	
	/**
	 * returns number of records in this page
	 */
	public function getCurrentPageTotal() {
		return $this->currentPageTotal;
	}//EOF getTotalThisPage
	
	public function getCurrentPage() {
		return $this->current_page+1;
	}//EOF getCurrentPage

	/**
	 * @param string $query
	 * @param array $params
	 *
	 * @return Data_APager
	 */
	protected function setQuery($query,array $params) {
		$this->query=$query;
		$this->params=$params;
		return $this;
	}
	
	/**
	 * Create the key to get the count, if it  is stored
	 *
	 * @return Data_APager
	 */
	protected function createKey(){
		$params=print_r($this->params,true);
		$this->key=md5($this->query.$params);
		return $this;
	}
	
	/**
	 * @return string count key for current SQL
	 */
	protected function getKey() {
		return $this->key;
	}
	
	/**
	 * returns number of pages in query.
	 * 
	 * @return integer number of pages in query
	 */
	public function getTotalPages() {
		$total=$this->count/$this->pageSize;
		if($total>((int)$total)){
			$total++;
		}
		return ((int)$total);
	}//EOF getTotalPages
	
	/**
	 * Main method of this class. It will check if a count exists, if not it will creat one, calculate the rullers
	 * update the query with the LIMIT clause, run the query and return a result set.
	 *
	 * @return array
	 */
	abstract public function getPage();
}//EOF CLASS
