<?php namespace SiTEL\DataSources\Sql;

/**
 * Query Filter is a container for WHERE/JOIN/HAVING singel field objects
 * that create the relevant SQL, depends on the fields values.
 * The filter class, returned to the user, can also be used to
 * re-generate UI level filter values.
 * This class is used to tie View filter elements to Query filters
 *
 * @author 	Itay Moav
 * @date	22-07-2014
 * @date    18-07-2017 migrated to TalisMS
 */
abstract class aQueryFilter{
	/**
	 * Making sure indexes are same as elements names, will have to make sure about the local ids tooo
	 * @param \SiTEL\DataSources\Sql\QueryFilter\aField $filter_elements
	 * @return array<string,\SiTEL\DataSources\Sql\QueryFilter\aField>
	 */
    static public function arrayBuilder(\SiTEL\DataSources\Sql\QueryFilter\aField ...$filter_elements):array{
		$ret=[];
		foreach($filter_elements as $FilterElement){
		    /* @var $FilterElement \SiTEL\DataSources\Sql\QueryFilter\aField */
			$ret[$FilterElement->elementName]=$FilterElement;
		}
		return $ret;
	}
	
	/**
	 * Get back to you with the correct filter for your Looper class.
	 * If no specific filter exists it will return NULL
	 *
	 * @param \SiTEL\DataSources\Sql\aAeonLooper $owner, class owning the filter.
	 * @param array<string,string> $request_params
	 * @return ?aQueryFilter
	 */
	public static function factory(aAeonLooper $owner,array $request_params=[]):?\SiTEL\DataSources\Sql\aQueryFilter{
		$father_name = get_class($owner);
		
		//init filter
		//The reasoning is to achieve something similar to Assembly in C#
		$class_name=$father_name . 'Filter';//The filter class should be defined in the same file as the report class itself and should have the exact same name + Filter.
		
		if(class_exists($class_name,false)){
			\dbgn('in filter for ' . $father_name);
			$filter = new $class_name($father_name,$request_params);
			return $filter;
		}else{
			\dbgn('No filter supplied');
			return null;
		}
	}
	/**
	 * @var string owner of this filter (the DG that instantiated it)
	 */
	public string $owner='';

	/**
	 * @var array<string> $rawRequestParams
	 */
	public 	array $rawRequestParams;

	/**
	 * @var array<QueryFilter\aField> of filter elements accessed by element name which the filter element creates
	 */
	public array $filterElements=[];
	
	/**
	 * @param string $owner Looper name
	 * @param array<string,string> $request_params
	 */
	public function __construct(string $owner,array $request_params){
		$this->init();
		$this->owner=$owner;
		$this->rawRequestParams=$request_params;
		\dbgr('RAW PARAMS IN FILTER',$this->rawRequestParams);
		$this->constructElements();
	}//EOF constructor
	
	protected function init():void{
		
	}
	
	/**
	 * Constructs & Retrieves the Where and Join statment from the filters
	 * And the parameters of the query
	 *
	 * @param array<string,mixed> $param_array
	 * @param string $starlog_table
	 * @param ?array<string,mixed> $extra_params
	 * @return array<string,array<int, string>|string> of WHERE | JOIN
	 */
	public function getWhereJoin(array &$param_array,string $starlog_table='', ?array $extra_params=NULL):array{
		$join='';
		$group_by=[];
		$where=[];
		foreach ($this->filterElements as $MyFilterElement){
			/*@var $MyFilterElement QueryFilter\aField */
			if(!$MyFilterElement->isActivated()){
				continue;
			}
			$join.=$MyFilterElement->getSqlJoin($starlog_table,$extra_params);
			$w=$MyFilterElement->getSqlWhere($starlog_table,$extra_params);
			if($w){
				$where[]=$w;
			}
			if($MyFilterElement->getSqlGroupBy($starlog_table, $extra_params)){
				$group_by[]=$MyFilterElement->getSqlGroupBy($starlog_table, $extra_params);
			}
			$MyFilterElement->populateArray($param_array);
		}
		
		$where=implode(' AND ',$where);
		if($where){
			$where=' WHERE ' . $where;
		}
		
		return ['WHERE'=>$where,'JOIN'=>$join,'GROUPBY'=>$group_by];
	}

	/**
	 * Returns all the filters with values formated as a query string
	 * (this can be done inside getWhereJoin, but I'll leave it outside for better code maintainability)
	 *
	 * @return string
	 */
	public function getFiltersAsQueryString():string{
		$query_string='';
		foreach ($this->filterElements as $MyFilterElement){
			/*@var $MyFilterElement QueryFilter\aField */
			$query_string .= $MyFilterElement->getAsQueryString();
		}
		return $query_string;
	}
	
	
	/**
	 * Late additions of parameters to the filter
	 *
	 * @param string $param_key
	 * @param mixed $param_value
	 * 
	 * @return aQueryFilter
	 */
	public function addParam($param_key,$param_value):aQueryFilter{
		if(isset($this->filterElements[$param_key])){
			$this->filterElements[$param_key]->setRawValue($param_value);
		}
		return $this;
	}
	
	/**
	 * GETTTT
	 *
	 * @param ?string $param_key
	 */
	public function getParam($param_key):?string{
		return isset($this->filterElements[$param_key])?$this->filterElements[$param_key]->getRawValue():null;
	}
	
	/**
	 * Removes a filter element (not destroying it) from the filter
	 *
	 * @param string $param_key
	 * @return ?\SiTEL\DataSources\Sql\QueryFilter\aField
	 */
	public function removeFilter(string $param_key):?\SiTEL\DataSources\Sql\QueryFilter\aField{
		$ret = null;
		if(isset($this->filterElements[$param_key])){
			$ret = $this->filterElements[$param_key];
			unset($this->filterElements[$param_key]);
		}
		return $ret;
	}

	/**
	 * Construct the filter elements into the filterElements array.
	 * This has to be a one dim associative array.
	 * Use static public function arrayBuilder(...$filter_elements)
	 * 
	 * $this->filterElements = self::arrayBuilder(...)
	 * 
	 * @return \SiTEL\DataSources\Sql\aQueryFilter
	 */
	abstract protected function constructElements():\SiTEL\DataSources\Sql\aQueryFilter;
}
