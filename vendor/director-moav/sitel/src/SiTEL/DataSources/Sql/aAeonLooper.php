<?php namespace SiTEL\DataSources\Sql;

/**
 * @author 	Itay Moav
 * @date	07-15-2014
 * @date    07-18-2017 migrated to TalisMS
 *
 * Define common API and functionality for SQL originated datasets functionality
 */
abstract class aAeonLooper extends \SiTEL\DataUtils\aAeonLooper{
	
	const	PROCESS_TYPE_NONE		= 2,
			PROCESS_TYPE_PROCESS	= 3,
			PROCESS_TYPE_PAGED		= 5,
	
			ORDER_BY				= 'order_by',
			ORDER_BY_DIRECTION		= 'order_by_dir',
			ORDER_BY_ASC			= 'asc',
			ORDER_BY_DESC			= 'desc',
			
			PAGE					= 1,
			PAGE_SIZE				= 100,
			PAGE_SIZE_AUTOPAGING	= 400
	;
	
	/**
	 * FOR IMMEDIATE RUN!
	 *
	 * @param int $process_type PROCESS_TYPE_NONE = 2, PROCESS_TYPE_PROCESS	= 3, PROCESS_TYPE_PAGED = 5
	 * @param array<array> $params
	 * @param \SiTEL\DataUtils\ResultSet\i $Resultset
	 * @param int $page
	 * @param int $page_size
	 * @return \SiTEL\DataUtils\ResultSet\i
	 */
	static public function resultSet(int $process_type,array $params=[],\SiTEL\DataUtils\ResultSet\i $Resultset=null,int $page=self::PAGE,int $page_size=self::PAGE_SIZE):\SiTEL\DataUtils\ResultSet\i{
		return self::create($process_type,$params,$Resultset,$page,$page_size)->run()->getResultset();
	}
	
	/**
	 * Just create the object, u still need to RUN!
	 *
	 * @param int $process_type PROCESS_TYPE_NONE = 2, PROCESS_TYPE_PROCESS	= 3, PROCESS_TYPE_PAGED = 5
	 * @param array<string,mixed>|array[] $params
	 * @param \SiTEL\DataUtils\ResultSet\i $Resultset
	 * @param integer $page
	 * @param integer $page_size
	 * @return \SiTEL\DataSources\Sql\aAeonLooper
	 */
	static public function create(int $process_type,array $params=[],\SiTEL\DataUtils\ResultSet\i $Resultset=null,int $page=self::PAGE,int $page_size=self::PAGE_SIZE):aAeonLooper{
		return new static($process_type,$params,$Resultset,$page,$page_size);
	}
	
	/**
	 * Do before autopaging
	 * @param array<array> $params
	 * @return void
	 */
	static protected function preAutoPaging(array $params = []) {	}
	/**
	 * 
	 * @param array<string, mixed>|array[] $params
	 * @param \SiTEL\DataUtils\ResultSet\i $Resultset
	 * @param int $page_size
	 */
	static public function autoPagingData(array $params=[],\SiTEL\DataUtils\ResultSet\i $Resultset=null,$page_size=self::PAGE_SIZE_AUTOPAGING):void{
		if(!$Resultset) $Resultset = new \SiTEL\DataUtils\ResultSet\Loki;
		
		static::preAutoPaging($params);
		
		$page=1;
		$Reader = new static(self::PROCESS_TYPE_PAGED*self::PROCESS_TYPE_PROCESS,$params,$Resultset,$page,$page_size);
		$Resultset = $Reader->run()->getResultset();
		$num_of_pages = $Resultset->getPager()->getTotalPages();
		for($page=2; $page<=$num_of_pages;$page++){
			$Reader = new static(self::PROCESS_TYPE_PAGED*self::PROCESS_TYPE_PROCESS,$params,$Resultset,$page,$page_size);
			$Reader->run();
		}
	}
	
	/**
	 * As opposed to the query above, here the result set is constantly changing
	 * and getting smaller.
	 * Means the first page, technicaly is always the bnew page.
	 *
	 * @param array<string, mixed>| array[] $params
	 * @param integer $page_size
	 */
	static public function autoPagingManipulatedData(array $params=[],\SiTEL\DataUtils\ResultSet\i $Resultset=null,$page_size=self::PAGE_SIZE_AUTOPAGING):void{
		if(!$Resultset) $Resultset = new \SiTEL\DataUtils\ResultSet\Loki;
		
		static::preAutoPaging($params);
		$Reader = new static(self::PROCESS_TYPE_PAGED*self::PROCESS_TYPE_PROCESS,$params,$Resultset,1,$page_size);
		\dbgn('ITERATION:0');
		$Resultset = $Reader->run()->getResultset();
		$num_of_pages = ($Resultset->getPager()->getTotalPages() -1);//The first page is allready taken care of, and won't be found again.
		for($page=1; $page<=$num_of_pages;$page++){
			\dbgn("ITERATION:{$page} out of {$num_of_pages}");
			$Reader = new static(self::PROCESS_TYPE_PAGED*self::PROCESS_TYPE_PROCESS,$params,$Resultset,1,$page_size);
			$Reader->run();
		}
	}
	
	/**
	 * If the input where statment has no where in it, I'll add it with a mock condition (I assume it is followed by an AND)
	 *
	 * @param string $where
	 * @return string sql	WHERE statment
	 */
	static protected function putWhere($where):string{
		if(!$where){
			return ' WHERE 1=1 ';
		}
		return $where;
	}
	
	/**
	 * Generate group by string
	 * @param string[] $group_by
	 */
	static protected function putGroupBy(array $group_by):string{
		if(!empty($group_by)){
			$group_by = implode(',', $group_by);
			$group_by = ' GROUP BY '. $group_by;
			return $group_by;
		}
		
		return '';
	}
	
	/**
	 * The header class, if exists!
	 *
	 * @var 
	 */
	//protected $Header = null;  TOBEDELETED202144 Unused and undefined Header may add eventually?
	
	/**
	 * @var array<string, string> of parameter values to use in sql, this is for the prepared statments
	 */
	protected array $query_param_array = [];
	
	/**
	 * @var MySqlClient
	 */
	protected \SiTEL\DataSources\Sql\MySqlClient $DB;
	
	/**
	 * The name of the connection to use. This is the array index from the config file (usualy).
	 * For example the values can be: 'READ','WRITE','REPORT','BABA_GANUSH'
	 * 
	 * @var string 
	 */
	protected string $db_connection_name = '';
	
	/**
	 * Pager values
	 * @var int $page
	 */
	protected int $page;
	/**
	 * @var int $pageSize
	 */
	protected int $pageSize;

	/**
	 * Default value for the order by clause - overwritten by header
	 *
	 * @var string
	 */
	protected string $orderBy='';
	
	/**
	 * Order by direction - overwritten by header
	 *
	 * @var string
	 */
	protected string $orderByDirection=' ASC ';
	
	/**
	 * just for auto completion sake
	 *
	 * @var \SiTEL\DataUtils\ResultSet\i
	 */
	protected \SiTEL\DataUtils\ResultSet\i $Resultset;
	
	/**
	 * @var ?aQueryFilter $QueryFilter to generate the result set upon
	 */
	protected ?aQueryFilter $QueryFilter;
	
	/**
	 * Mode of processing
	 * 2 - PROCESS_TYPE_NONE
	 * 3 - PROCESS_TYPE_PROCESS
	 * 5 - PROCESS_TYPE_PAGED
	 *
	 * @var integer
	 */
	protected $process_mode = self::PROCESS_TYPE_NONE;
	
	/**
	 * While row type is the general value for all
	 * loopers, this is the value coming from PDO and whould be used 
	 * in the sql looper.
	 * If needs to be changed, all u need is changing
	 * the method setSqlRowType()
	 * 
	 * @var integer
	 */
	protected $sql_row_type;
	/**
	 * 
	 * @param int $process_type
	 * @param array<string|string>|array[] $user_params
	 * @param \SiTEL\DataUtils\ResultSet\i $Resultset
	 * @param int $page
	 * @param int $page_size
	 */
	public final function __construct($process_type,array $user_params=[],\SiTEL\DataUtils\ResultSet\i $Resultset=null,$page=self::PAGE,$page_size=self::PAGE_SIZE){
		parent::__construct($user_params);
		$this->setSqlRowType();
		$this->process_mode = $process_type;
		$this->setPaging($page, $page_size);
		$this->DB = Factory::getConnectionOrDefaultMySQL($this->db_connection_name);//maybe I should inject this...
		
		$this->preInit();
		$this->generateFilter()
			->set($Resultset)
			->setOrderBy()
			->postInit()
		;
	}
	
	/**
	 * Sets the sql queries return types
	 * 
	 * @return int
	 */
	protected function setSqlRowType():int{
		switch ($this->row_type){
			case self::ROW_TYPE__ARRAY:
				return $this->sql_row_type = \PDO::FETCH_BOTH;
				
			case self::ROW_TYPE__ASSOC:
				return $this->sql_row_type = \PDO::FETCH_ASSOC;
				
			case self::ROW_TYPE__STDCLAS:
				return $this->sql_row_type = \PDO::FETCH_CLASS;
				
			default:
				return $this->sql_row_type = \PDO::FETCH_BOTH;
		}
	}
	
	/**
	 * paging values
	 *
	 * @param integer $page
	 * @param integer $page_size
	 * @return aAeonLooper
	 */
	public function setPaging(int $page,int $page_size):aAeonLooper{
		$this->page     = $page;
		$this->pageSize = $page_size;
		return $this;
	}
	
	/**
	 * This method should generate string with the query of this dataset.
	 * @return mixed resource/query to loop around.
	 */
	abstract protected function query();
	
	/**
	 * The main entry point to the report generating algorithem
	 *
	 * @return aAeonLooper
	 */
	final public function run():aAeonLooper{
		$this->localGenerateResultset()
	 		 ->postProcess()
		;
		return $this;
	}
	
	/**
	 * Holds the specific structure of the filter for each data set
	 * This is the default behaviour - feel free to override it
	 *
	 * @return aAeonLooper
	 */
	public function generateFilter():aAeonLooper{
		$this->QueryFilter = aQueryFilter::factory($this, $this->user_params);
		return $this;
	}
	
	/**
	 * Sets this class data set, this is the default
	 * @return aAeonLooper
	 */
	protected function set(\SiTEL\DataUtils\ResultSet\i $Resultset=null):aAeonLooper{
		$this->Resultset = $Resultset?:new \SiTEL\DataUtils\ResultSet\Loki;
		if($this->QueryFilter) $this->Resultset->setFilter($this->QueryFilter);
		return $this;
	}
	
	/**
	 * @return \SiTEL\DataUtils\ResultSet\i
	 */
	public function getResultset():\SiTEL\DataUtils\ResultSet\i{
		return $this->Resultset;
	}
	
	/**
	 * set the order by field
	 *
	 * @return aAeonLooper
	 */
	public function setOrderBy():aAeonLooper{
		/* TODO DONOTDELETE!!! 
		 * for now shut down, this is a way to override default order by and get order by from UI.
		 * Will restore it when the need returns
		 *
		$order_by = $this->getParam(self::ORDER_BY);
		if($this->getHeader() && $order_by){
			$this->orderBy=$this->getHeader()->get_value($order_by);
			
			$order_by_dir = $this->getParam(self::ORDER_BY_DIRECTION);
			if($order_by_dir){
				$this->orderByDirection=($order_by_dir==self::ORDER_BY_DESC)?self::ORDER_BY_DESC:self::ORDER_BY_ASC;
			}
			
			$this->getHeader()->setOrderBy($order_by,$this->orderByDirection);
		}*/
		return $this;
	}
	
	/**
	 * Holds the specific structure of the filter for each report object
	 * @return aQueryFilter
	 */
	public function getFilter():aQueryFilter{
	    if(!$this->QueryFilter){
	        throw new \Exception('Getting Filter that has not been set.');
	    }
		return $this->QueryFilter;
	}
	
	/**
	 * the most commonly used way of localGenerateDataset
	 * Overwrite this if u wish to have different method
	 *
	 * @return aAeonLooper
	 */
	protected function localGenerateResultset():aAeonLooper{
		return $this->defaultGenerateResultset();
	}
	
	/**
	 * A deault way to generate a data set.
	 * Will decide which method to run to get data
	 * @return aAeonLooper
	 */
	protected function defaultGenerateResultset():aAeonLooper{
		$name = 'GenerateResultset';
		if($this->process_mode%self::PROCESS_TYPE_PROCESS == 0){
			$name = 'processed' . $name;
		}
		
		if($this->process_mode%self::PROCESS_TYPE_PAGED == 0){
			$name = 'paged' . $name;
		}
		$query = $this->query() . $this->getOrderBySql();
		return $this->$name($query);
	}
	
	/**
	 * Straight forward data retrieval. 
	 * Unless it needs filtering + ordering, it should really be inside
	 * a SP
	 *
	 * @param string $sql
	 * @return aAeonLooper
	 */
	protected function generateResultset(string $sql):aAeonLooper{
		$this->Resultset->setData($this->DB->select($sql,$this->query_param_array)->fetchAll($this->sql_row_type));
		return $this;
	}
	
	/**
	 * Returns a paged dataset by the page params
	 *
	 * @param string $sql
	 * @return aAeonLooper
	 */
	protected function pagedGenerateResultset(string $sql):aAeonLooper{
	    if(!$this->QueryFilter){
	        throw new \Exception('Using Query Filter that has not been set.');
	    }
		$this->QueryFilter->getWhereJoin($this->query_param_array);// Why is this here?! IT IS HERE PROBABLY TO GENERATE THE Pager params only, should be fixed
		$Pager = new Pager($sql,$this->query_param_array,$this->DB,$this->pageSize);
		$Pager->setCurrentPage($this->page);
		$this->Resultset->setPager($Pager);
		$this->Resultset->setData($Pager->getPage($this->sql_row_type));
		return $this;
	}
	
	/**
	 * Both paging the dataset
	 * and running the process method on top of it
	 *
	 * @param string $sql
	 * @return aAeonLooper
	 */
	protected function processedGenerateResultset(string $sql):aAeonLooper{
		$Result = $this->DB->select($sql,$this->query_param_array)->fetchAll($this->sql_row_type);
		foreach($Result as $row){
			$this->row=&$row;
			$this->process();
			$this->Resultset->addLine($row);
		}
		
		return $this;
	}
	
	/**
	 * @param string $sql
	 * @return aAeonLooper
	 */
	protected function pagedprocessedGenerateResultset(string $sql):aAeonLooper{
	    $Pager = new Pager($sql,$this->query_param_array,$this->DB,$this->pageSize);
		$Pager->setCurrentPage($this->page);
		$this->Resultset->setPager($Pager);
		$Result = $Pager->getPage($this->sql_row_type);
		foreach($Result as $row){
			$this->row=&$row;
			$this->process();
			$this->Resultset->addLine($row);
		}
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	protected function getOrderBySql(){
		if($this->orderBy){
			return " ORDER BY {$this->orderBy} {$this->orderByDirection}";
		}
		return '';
	}
	
	/**
	 * Get the WHERE and JOIN statment is a default way from the filters
	 * @param string $starlog_table
	 * @param ?array<string,string> $extra_params
	 * @return array<string, array<int, string>|string>
	 */
	protected function getWhereJoin(string $starlog_table='', $extra_params = null):array{
		$this->query_param_array=[];//we might get usage of those params more then once in a report, so I need to clean those as not all are used on the same part
		$where_join = $this->QueryFilter ? $this->QueryFilter->getWhereJoin($this->query_param_array,$starlog_table, $extra_params) : ['WHERE'=>'','JOIN'=>'','GROUPBY'=>''];
		QueryFilter\aField::resetAllreadyJoinedTables();
		return $where_join;
	}
	
	/**
	 * Return just the where condition, with no WHERE
	 * @param string $starlog_table
	 * @param ?array<string,string> $extra_params
	 * @return array<int, string>|string
	 */
	protected function justGetWhere(string $starlog_table='',$extra_params = null){
		$w_j = $this->getWhereJoin($starlog_table, $extra_params);
		/** @phpstan-ignore-next-line Ignore until custom class made on Sunday evening or Monday */
		return (strlen(trim($w_j['WHERE']))>5) ? $w_j['WHERE'] : '';
	}
}//EOF CLASS


