<?php
/**
 * 
 * @author itay
 *
 */
class Data_Redshift_MyStagingDB{
    
    /**
     * @var array
     */
    private array $results = [];
    
    /**
     * @param string $sql
     * @param array $params
     * @return Data_Redshift_MyStagingDB
     */
    public function select(string $sql,$params=[]){
        $sql= str_replace(['environment.','environment_mv.'],['lms4_reports_staging.','lms4_reports_mv.'],$sql);
        $this->results = \rddb()->select($sql,$params)->fetchAll();
        return $this;
    }

    public function fetchAll():array{
        return $this->results;
    }
    
    /**
     * @return array
     */
    public function fetch_row():array{
       return $this->results;
    }
    
    /**
     * 
     * @param int $page
     * @param int $size
     * @return string
     */
    public function getLimitSql(int $offset,int $how_many_records_to_fetch):string{
        return "\n LIMIT {$offset}, {$how_many_records_to_fetch}\n";
    }
    
    /**
     * Wrapper for escaped params 
     * @param string $param_name -> :param_name
     * @return string
     */
    public function get_escaped_param_placeholder(string $param_name):string{
        return ":{$param_name}";
    }
}
