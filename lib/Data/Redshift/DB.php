<?php
/**
 * 
 * lms_data_wh : fact and dim tables around what people do in the LMS (course completions/ log in etc)
 * lms_data_mv: Materialized views built on top of lms_users_wh (important to know what is an MV vs Fact)
 * staging: anything used to prepare data for import/export
 * temp: I already know Stephen will need this to play
 * lms_usage_wh: facts and dim about lms usage (like which course is most search for, which user is doing most of what activity, which filters are most used etc)
 * 
 * @date 202204 to 202205
 * @author itay
 *
 */
class Data_Redshift_DB{
    
    /**
     * @var Data_Redshift_DB
     */
    static private ?Data_Redshift_DB $DB=null;
    
    /**
     * This is the connection. 
     * A resource, no data type
     * @var resource
     */
    private $NativeDB;
    /**
     * When escaping a user value, this is the index attached to the $
     * @var integer
     */
    private $param_index = 0;
    /**
     * Last SQL which has been performed
     *
     * @var String
     */
    private $last_sql = '';
    /**
     * Array of Parameters last used in the last SQL
     *
     * @var Array
     */
    private $last_bind_params = [];
    /**
     * response from db
     * @var resource
     */
    private $result;
    /**
     * 
     * @return Data_Redshift_DB
     */
    public static function getInstance():Data_Redshift_DB{
        if(!self::$DB){
            self::$DB = new Data_Redshift_DB(\app_env()['database']['reports-mayhem']);
        }
        return self::$DB;
    }

    /**
     * USed ONLY to retrieve the next $param_index available
     * @param string $name
     */
    public function __get($name){
        if($name === 'idx'){
            return ++$this->param_index;
        }
        throw new \Exception("Unknown variable name {$name}. Did u mean [idx]?");
    }
    
    /**
                'host' => 'localhost',
                'database' => 'mayhem',
                'schema'   => '.data',
                'username' => 'postgres',
                'password' => '',
                'port'     => 5432,
                'verbosity' => 2
                
     * @param array<string,string> $config
     */
    private function __construct(array $config){
        $connection_string = "host={$config['host']} dbname={$config['database']} port={$config['port']} connect_timeout=5 user={$config['username']} password={$config['password']}";
        $this->NativeDB = pg_connect($connection_string);
        if(!$this->NativeDB){
            throw new \Exception("Reports connection failed {$config['host']}:{$config['port']} {$config['database']}" . pg_last_error());
        }
    }
    
    /**
     * 
     * @param string $sql
     * @param array $params
     * @param bool $execute if u just want the query syntax
     * @return array of results
     */
    private function execute($sql, array $params,bool $execute):void{
        $this->last_sql = $sql;
        $this->last_bind_params=$params;
        dbgr('SQL ABOUT TO RUN',$this->last_sql);
        if($this->last_bind_params){
            dbgr('SQL PARAMS',$this->last_bind_params);
        }
        if(! $execute) return;
        
        //in transaction, direct all queries to the connection in transaction!
        $DB = $this->NativeDB;

        //error handling
        try{
            
            if($params){
                $this->result = pg_query_params($DB,$sql,$params);
                $this->param_index = 0;
            }else{
                $this->result = pg_query($DB,$sql);
            }
            
        }catch (\Error $e){
            fatal('Reports DB error ' . pg_last_error());
            throw $e;
        }
        
        if(!$this->result){
            throw new \Exception(pg_last_error());
        }
    }
    
    /**
     * 
     * @param string $sql
     * @param array $params
     * @param bool $execute
     * @return Data_Redshift_DB
     */
    public function select(string $sql, array $params=[],bool $execute=true):Data_Redshift_DB{
        $sql= str_replace(['environment.','environment_mv.'],['lms_data_wh.','lms_data_mv.'],$sql);
        $this->execute($sql,$params,$execute);
        return $this;
    }
    
    /**
     * @return array
     */
    public function fetchAll():array{
        return \pg_fetch_all($this->result);//,\PGSQL_NUM if I want array int
    }
    
    /**
     * @return array
     */
    public function fetch_row(){
        while($row=pg_fetch_assoc($this->result)){
            yield($row);
        }
    }
    
    /**
     *
     * @param int $page
     * @param int $size
     * @return string
     */
    public function getLimitSql(int $offset,int $how_many_records_to_fetch):string{
        return "\n LIMIT {$how_many_records_to_fetch} OFFSET {$offset}\n";
    }
    
    
    /**
     * Wrapper for escaped params
     * @param string $param_name -> ${idx}
     * @return string
     */
    public function get_escaped_param_placeholder(string $param_name):string{
        return '$'.$this->idx;
    }
}



