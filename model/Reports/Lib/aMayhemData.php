<?php namespace Reports\Lib;

/** 
 * 
 * @author itay
 * 
 * Class to encapsulate the main query/queries to fetch the data.
 */
abstract class aMayhemData
{
    /**
     * @var \Reports\Service\Ownership\Ownership
     */
    protected \Reports\Service\Ownership\Ownership $Ownership;

    /**
     * @var \Reports\Lib\aMayhemFilter
     */
    protected \Reports\Lib\aMayhemFilter $Filter;
    
    /**
     * @var \Reports\Lib\MayhemSortPager
     */
    protected \Reports\Lib\MayhemSortPager $SortPager;

    /**
     * 
     * @var array<int,array<string,mixed>> storage of the data fetched
     */
    protected array $dataset=[];
    
    /**
     * Summaries is the piece that goes on top of the report.
     * @var array<array<string,string>>
     */
    protected array $summaryReport=[];
    
    
    /**
     * Should the process() loop be activated for this report. Not implemented yet!
     * @var bool
     */
    protected bool $with_process = false;//TODO not used yet!
    /**
     * flag for the summary report this dataset has.
     * @var bool
     */
    protected bool $should_i_generate_summary;
    
    /**
     * @param \Reports\Service\Ownership\Ownership $Ownership
     * @param \Reports\Lib\aMayhemFilter $Filter
     * @param \Reports\Lib\MayhemSortPager $SortPager
     * @param string $should_i_generate_summary
     */
    public function __construct(\Reports\Service\Ownership\Ownership $Ownership,\Reports\Lib\aMayhemFilter $Filter,\Reports\Lib\MayhemSortPager $SortPager,?string $should_i_generate_summary)
    {
        $this->Ownership = $Ownership;
        $this->Filter    = $Filter;
        $this->SortPager = $SortPager;
        $this->should_i_generate_summary = is_string($should_i_generate_summary);
    }

    /**
     * Summaries is the piece that goes on top of the report.
     * Probably should be cached for each filter combo
     * Total Learners		Total Learner by organization		Total Learners by department		Total Learners by job function
     * 
     * OVERWRITE THIS TO GET A DIFFERENT SUMMARY REPORT
     */
    protected function generateSummaryReport():void{
        if($this->should_i_generate_summary){
            $summaries = new \Reports\Service\Summaries\OrgDepFunc($this->Ownership);
            $summaries->generateSummaryReport();
            $this->summaryReport = $summaries->summaryReport();
        }
    }
    
    /**
     * This method should generate string with the sql(s) of this report.
     *
     * @return string the main SQL to be used in chosen report.
     */
    abstract protected function query():string;
    
    /**
     * Adds filters, joins, env name and paging to the basic query
     * @return string
     */
    protected function construct_query():string{
        $sql = $this->query();
        //SILENCED maybe for good $sql = $this->SortPager->addCountQueryMySql($this->query());
        $sql .= $this->SortPager->getOrderByMySql();
        $sql .= $this->SortPager->getLimitMySql();
        return $sql;
    }
    
    /**
     * Runs the query and populates the dataset
     * @return \Reports\Lib\aMayhemData
     */
    public function run():\Reports\Lib\aMayhemData{
        $this->preCountSteps();
        $this->alternateCalculateCount();
        $sql = $this->construct_query();
        $this->dataset = \reports_mayhemdb()->select($sql,$this->Filter->get_sql_escaped_params())->fetchAll();
        //SILENCED maybe for good  $this->SortPager->runTotalCountQueryMySql($this->dataConnection);
        $this->generateSummaryReport();
        return $this;
    }
    
    /**
     * @return string
     */
    protected function getOwnershipSql():string{
        return $this->Ownership->as_sql();      
    }
    
    /**
     * Placeholder to make any preps needed. 
     * Example: temp tables needed for this report (consider making MVs)
     */
    protected function preCountSteps():void{
        
    }
    
    /**
     * A method to overwrite the tradinional SQL_CALC_FOUND_ROWS
     * Which is pretty stupid in some queries and not very efficient.
     */
    final protected function alternateCalculateCount():void{
        if(\Reports\FLAGS__PAGER_TOTAL_COUNT_NOT_CALCULATED === $this->SortPager->totalCount()){
            $this->SortPager->setTotalCount($this->calculateCountManually());
        }
    }
    
    /**
     * Overwrite this method to provide a report specific count
     * @return int
     */
    abstract protected function calculateCountManually():int;
    
    /**
     * @return array<int,array<string,mixed>>
     */
    public function data():array{
        return $this->dataset;
    }
    
    /**
     * @return array<array<string,string>>
     */
    public function summaries():array{
        return $this->summaryReport;
    }
}
