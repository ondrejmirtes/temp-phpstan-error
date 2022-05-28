<?php namespace SiTEL\DataSources\Sql\QueryFilter;
/**
 * Same as the abstract, just removed excess code we no longer need
 *
 * @author 	Itay Moav
 * @date	23-07-2014
 * @date    19-07-2017 migrated to TalisMS
 */
abstract class aField{
    /**
     * 
     * @var array<string,string> $allreadyJoinedTables
     */
	static protected array $allreadyJoinedTables=array();
	/**
	 * 
	 * @var string $join_type
	 */
	static protected string $join_type = 'join';
	/**
	 * 
	 * @var string
	 */
	private const EMPTY_FILTER_ELEMENT = 'EMPTYFILTERELEMENT';
	
	static public function resetAllreadyJoinedTables():void{
		self::$allreadyJoinedTables=array();
	}
	
	/**
	 * I use this to cache the values I get in getSqlWhere() for the mysql binding
	 * @var array<string,mixed> $params
	 */
	protected array $params=[];
	
	/**
	 * @var string Owner class name of the filter
	 */
	protected string $owner='';
	
	/**
	 * Needs to be overidden in each concrete usage
	 * @var string $elementName key in the input array of values.
	 */
	public string $elementName='';
	/**
	 * @var mixed $rawValue the raw value from the input array to the constructor.
	 */
	protected $rawValue;
	
	/**
	 * 
	 * @var array<mixed> $otherRawValue of all the other filter elements values
	 */
	protected array $otherRawValue;

	/**
	 * @var \SiTEL\DataSources\Sql\aQueryFilter
	 */
	public \SiTEL\DataSources\Sql\aQueryFilter $ParentFilter;
	
	//----------------------------------------------------------------- methods -----------------------------------------------------
	
	/**
	 * Stores the relevant data piece to this element from an array of input data (like a POST array)
	 */
	public function __construct(\SiTEL\DataSources\Sql\aQueryFilter $Filter){
		$this->owner         = $Filter->owner;
		$this->rawValue      = $Filter->rawRequestParams[$this->elementName]??null;
		$this->otherRawValue = $Filter->rawRequestParams;
		$Filter->filterElements[$this->elementName]=$this;
		$this->ParentFilter  = $Filter;
	}//EOF constructor
	
    /**
     * @param string $starlog_table
     * @param array<string, mixed> $extra_params
     * @return string relevant sql `where` statment.
     */
	public function getSqlWhere($starlog_table='', $extra_params = null):string{
		if(!$this->rawValue) return '';
		$join_method='where' . $starlog_table;
		
		if(method_exists($this,$join_method)){
			return $this->{$join_method}($starlog_table, $extra_params);
		}
		$join_method='where' . $this->owner;
		
		if(method_exists($this,$join_method)){
			return $this->{$join_method}($starlog_table, $extra_params);
		}
		return $this->whereDefault($starlog_table);
	}
	
	/**
	 * populate the params array (by ref) with :param]=value
	 * @param array<string,mixed> $params
	 */
	public function populateArray(array &$params):void{
		$params=array_merge($params,$this->params);
	}
	
	/**
     * @return string
	 */
	public function getOwner():string{
		return $this->owner;
	}
	
	
	/**
	 * The default where statment to call if no report specific exists
	 * @param string $starlog_table
	 * @return string
	 */
	abstract protected function whereDefault($starlog_table):string;
	
	/**
	 * This is the only part that is different between reports.
	 * How should I handle this,array or strategy or Lambda...?
	 * @param string $join
	 * @return string relevant sql `join` statment.
	 */
	protected function joinDefault($join=''):string{
		return $join;
	}
	
	/**
	 * @param string $starlog_table
	 * @param array<string, mixed> $extra_params
	 * @return string relevant sql `join` statment.
	 */
	public function getSqlJoin($starlog_table='', $extra_params=null):string{
		$join_method='join' . $starlog_table;
		if(method_exists($this,$join_method)){
			return $this->{$join_method}();
		}
		$join_method='join' . $this->owner;
		if(method_exists($this,$join_method)){
			return $this->{$join_method}($extra_params);
		}
		return $this->joinDefault();
	}
	
	/**
	 * Method to register all ready joined tables
	 * so I won't join the same table twice. Still, it is
	 * very easy to by pass in the individual join methods of the filters.
	 * @param string $table_name
	 * @param string $join
	 * @param string $join_type
	 * @return string
	 */
	static protected function getJoinIfNotRegistered($table_name,$join, $join_type = 'join'):string{
		if(isset(self::$allreadyJoinedTables[$table_name])){
			return '';
		}
		
		self::setJoinType($join_type);
		self::$allreadyJoinedTables[$table_name]='';
		return $join;
	}
	/**
     * @return string
	 */
	static protected function getJoinType():string{
		return self::$join_type;
	}
	/**
     * @param string $join_type
	 */
	static protected function setJoinType($join_type = 'join'):void{
		self::$join_type = $join_type;
	}
	
	/**
	 * @param string $starlog_table
	 * @param array<string, mixed> $extra_params
	 * @return string relevant sql `group by` statment.
	 */
	public function getSqlGroupBy($starlog_table='', $extra_params=null):string{
		$group_by_method='groupBy' . $starlog_table;
		if(method_exists($this,$group_by_method)){
			return $this->{$group_by_method}();
		}
		$group_by_method='groupBy' . $this->owner;
		if(method_exists($this,$group_by_method)){
			return $this->{$group_by_method}($extra_params);
		}
		return $this->groupByDefault();
	}
	
	/**
	 * Default Group By overwritten in child classes.  Allows for variable Group By
	 * @param string $group_by
	 * @return string
	 */
	protected function groupByDefault($group_by=''):string{
		return $group_by;
	}
	
	/**
     * @return string
	 */
	public function getAsQueryString():string{
		if(is_array($this->rawValue)){
			$ret='';
			foreach($this->rawValue as $k=>$v){
				$ret .= "&{$this->elementName}[{$k}]={$v}";
			}
			return $ret;
		}
		if($this->rawValue){
			return '&' . $this->elementName . '=' . $this->rawValue;
		}
		return '';
	}
	
	/**
	 * @return bool if to activate this filter element or not
	 */
	public function isActivated():bool{
		return boolval($this->rawValue);
	}
	/**
     * @return mixed
	 */
	public function getRawValue(){
		return $this->rawValue;
	}

	/**
	 * 
	 * @param mixed $param_value
	 * @return \SiTEL\DataSources\Sql\QueryFilter\aField
	 */
	public function setRawValue($param_value): \SiTEL\DataSources\Sql\QueryFilter\aField{
		$this->rawValue = $param_value;
		return $this;
	}
	/**
	 * 
	 * @return array<mixed>
	 */
	public function getOtherRawValue():array{
		return $this->otherRawValue;
	}
}
