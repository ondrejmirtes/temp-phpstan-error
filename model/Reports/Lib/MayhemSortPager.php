<?php namespace Reports\Lib;

/**
 * 
 * @author itay
 *
 */
class MayhemSortPager{

    /**
     * 
     * @var \Reports\Lib\MayhemSchema
     */
    private \Reports\Lib\MayhemSchema $Schema;
    
    /**
     * @var string
     */
    private string $sort_by;
    
    /**
     * 
     * @var string
     */
    private string $sort_dir;
    
    /**
     * Total number of records current report has (not page size, total!)
     * 
     * @var int
     */
    private int $total_count;
    
    /**
     * @var int
     */
    private int $current_page;
    
    /**
     * @param \Talis\Message\Request $Request
     * @param \Reports\Lib\MayhemSchema $Schema
     */
    public function __construct(\Talis\Message\Request $Request,\Reports\Lib\MayhemSchema $Schema)
    {
        $this->Schema  = $Schema;
        
        //SORT
        $this->sort_by  = $this->Schema->default_sort_by();
        $this->sort_dir = $this->Schema->default_sort_dir();
        
        $sort_by = $Request->get_param_null(\Reports\API_KEYWORD__SORT_BY);
        if($sort_by){//check the value is legit, from schema
            foreach($this->Schema->fields() as $field_sql_safe){
                if($field_sql_safe->id === $sort_by && $field_sql_safe->sortable == 1){
                    $this->sort_by = $field_sql_safe->id;
                    if($Request->get_param_null(\Reports\API_KEYWORD__SORT_DIRECTION) === \Reports\API_KEYWORD__SORT_DIRECTION__DESC){
                        $this->sort_dir = \Reports\API_KEYWORD__SORT_DIRECTION__DESC;
                    } else {
                        $this->sort_dir = \Reports\API_KEYWORD__SORT_DIRECTION__ASC;
                    }
                    break; //found a matching field
                }
            }
        }
        
        //PAGER
        $this->total_count  = intval($Request->get_param_default(\Reports\API_KEYWORD__PAGER_TOTAL_COUNT, strval(\Reports\FLAGS__PAGER_TOTAL_COUNT_NOT_CALCULATED))) * 1;
        $this->current_page = intval($Request->get_param_default(\Reports\API_KEYWORD__PAGER_CURRENT_PAGE,'1')) -1;
        if($this->current_page < 0){//below 0 will trigger a sql error
            $this->current_page = 0;
        }
    }
    
    /**
     * Looks in the request for sort fields, makes sure they are legit 
     * if none, returnes the default from the schema
     * 
     * @return string
     */
    public function getOrderByMySql():string{
        return " ORDER BY {$this->sort_by} {$this->sort_dir} ";
    }
    
    /**
     * LIMIT 100,1000
     * 
     * @return string
     */
    public function getLimitMySql():string{
        $offset = $this->current_page * \Reports\DATA_BATCH_SIZE;
        return \reports_mayhemdb()->getLimitSql($offset,\Reports\DATA_BATCH_SIZE);
        //LIMIT {$offset}, {$batch_size}"; or LIMIT {$batch_size} OFFSET {$offset}
    }
    
    /**
     * IF there is no total count, modifies the query /captures it etc for calculating total possible records
     * 
     * @param string $query
     * @return string
     */
    public function addCountQueryMySqlSILENCEDMybeForGood(string $query):string{
        if($this->total_count === \Reports\FLAGS__PAGER_TOTAL_COUNT_NOT_CALCULATED){
            //TODO !!! $query = preg_replace('"SELECT "','SELECT SQL_CALC_FOUND_ROWS ',$query, 1);
        }
        return $query;
    }

    /**
     * @DEPRECATED I want to write count queries manually for each report
     * Figures out the total count - 
     * @param unknown $DbClient
     */
    /*
    public function runTotalCountQueryMySqlSILENCEDMybeForGood($DbClient):void{
        if($this->total_count === \Reports\FLAGS__PAGER_TOTAL_COUNT_NOT_CALCULATED){
            $this->total_count = $DbClient->select('SELECT FOUND_ROWS() AS total')->fetchObj()->total;
        }
    }*/
    
    /**
     * @return int
     */
    public function totalCount():int{
        return $this->total_count;
    }

    /**
     * @param int $count
     */
    public function setTotalCount(int $count):void{
        $this->total_count = $count;
    }
    
    /**
     * @return \stdClass
     */
    public function getHeaderResponse():\stdClass{
        $header = new \stdClass;
        $header->sort_by  = $this->sort_by;
        $header->sort_dir = $this->sort_dir;
        $header->total_count = $this->total_count;
        $header->batch_size = \Reports\DATA_BATCH_SIZE;
        $header->page_size  = \Reports\DATA_DEFAULT_PAGE_SIZE;
        $header->page       = $this->current_page;
        return $header;
    }
    
}
