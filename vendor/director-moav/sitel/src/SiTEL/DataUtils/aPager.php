<?php namespace SiTEL\DataUtils;
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
abstract class aPager{
	use \SiTEL\DataSources\Session\tHelper;

	/**
	 * @var int $pageSize
	 * @var int $current_page
	 * @var int $count
	 * @var int $currentPageTotal
	 * @var string $query
	 * @var string $key
	 */
	protected	int $pageSize;					//Page size to show.
	protected	int $current_page;				//current page requested.
	protected	int $count   = 0;            	//Number of rows expected from this query.
	protected   int $currentPageTotal;			//Total entries in this page.
	protected	string $query;					//The query I need to page.
	protected	string $key;					//The key in the cache for the sql.
    /**
     * @var array<mixed> $params
     */
	protected	$params;			//Params the query uses, we can have several similar base queries which differ only by the params (WHERE/filters etc)
				
				
	/**
	 * @return integer page size
	 */
	public function getPageSize():int{
		return $this->pageSize;	
	}
    
	/**
	 * @param int $page_size
	 */
	public function setPageSize(int $page_size):void{
		$this->pageSize=$page_size;
	}
	
	/**
	 * @param int $current_page
	 * @return aPager
	 */
	public function setCurrentPage(int $current_page):aPager{
		$this->current_page=($current_page-1)*1; //defaults to 0
		return $this;
	}

	/**
	 * Optional method to set manualy the count. Will save a query if there is pre knowledge of the count.
	 * Otherwise, will run a COUNT query once per new sql.
	 *
	 * @param integer $count
	 * @return aPager
	 */
	public function setCount(int $count):aPager{
		$this->count=$count*1; //*1 is to make it an int instead of a string
		$this->setSessionValue($this->key,$count);
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNextPageNumber():int {
		$c=$this->current_page;
		$s=$this->pageSize;
		$cn=$this->count;
		if((($c+1)*$s)>=$cn) {
			return 0;
		}else{
			return (++$c);
		}
	}//EOF getNextPageNumber

	/**
	 * @return int
	 */
	public function getBackPageNumber():int{
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
	 * @return int total entries
	 */
	public function getTotal():int{
		return $this->count*1;
	}//EOF getTotal
	
	/**
	 * returns number of records in this page
	 * @return int
	 */
	public function getCurrentPageTotal():int {
		return $this->currentPageTotal;
	}//EOF getTotalThisPage
	
	/**
	 * @return int
	 */
	public function getCurrentPage():int {
		return $this->current_page+1;
	}//EOF getCurrentPage

	/**
	 * @param string $query
	 * @param array<mixed> $params
	 *
	 * @return aPager
	 */
	protected function setQuery(string $query,array $params):aPager {
		$this->query=$query;
		$this->params=$params;
		return $this;
	}
	
	/**
	 * Create the key to get the count, if it  is stored
	 *
	 * @return aPager
	 */
	protected function createKey():aPager{
		$params=print_r($this->params,true);
		$this->key=md5($this->query.$params);
		return $this;
	}
	
	/**
	 * @return string count key for current SQL
	 */
	protected function getKey():string {
		return $this->key;
	}
	
	/**
	 * returns number of pages in query.
	 * 
	 * @return integer number of pages in query
	 */
	public function getTotalPages():int {
		$total=$this->count/$this->pageSize;
		if($total>((int)$total)){
			$total++;
		}
		return ((int)$total);
	}//EOF getTotalPages
	
	/**
	 * Main method of this class. It will check if a count exists, if not it will creat one, calculate the rullers
	 * update the query with the LIMIT clause, run the query and return a result set.
	 * @return mixed
	 */
	abstract public function getPage();
}//EOF CLASS
