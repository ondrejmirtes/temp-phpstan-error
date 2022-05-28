<?php namespace SiTEL\DataSources\Sql;

/**
 * TODO: this class should have one common ancsestor
 *
 * @author 	Itay Moav
 * @date	01-18-2011
 * @date    2020-05-27 make MSSQL version
 *
 * Centralized hub to do all Insert Delete Update (IDU) actions on a specific table
 */
abstract class MssqlTableHub{
    
        /**
         * 
         * @var string $connection_name
         */
        protected string $connection_name;
        
        /**
         * Databse name to manage IDU upon
         *
         * @var string $database_name
         */
        protected string $schema_name = '';
        
        /**
         * Table name to manage IDU upon
         *
         * @var string $table_name
         */
        protected string $table_name = '';
        
        /**
         * Determines if the class has a delta table to record changes
         *
         * Defaults to false
         * @var boolean
         */
        protected bool $has_delta = false;
        
        /**
         * Array of filters and dependencies to either fail or modify data before we insert/update
         * @var array
         */
        // protected array $data_clean_rules = []; TOBEDELETED2118
        
        /**
         * for the metadat fields
         * 
         * @var integer
         */
        static protected int $current_user = 0;
        
        /**
         * order by
         *
         * @var string
         */
        protected string $order_by = 'date_created';
        
        /**
         * Unique keys for on duplicate update
         * @var array<int, array<int, string>>
         */
        protected array $unique_keys = [];
        
        /**
         * Use date_created, date_modified, created_by, modified_by
         * @var boolean
         */
        protected bool $use_control_fields = true;
        
        /**
         * Sets the current user
         */
        static public function setCurrentUser(int $current_user):void{
            self::$current_user=$current_user;
        }
        
        /**
         * @param string $connection_name
         * @return \SiTEL\DataSources\Sql\MssqlTableHub
         */
        static public function getInstance(string $connection_name=''){
            return new static($connection_name);
        }
        
        /**
         * @param array<string,mixed> $where
         * @param array<string> $fields
         *
         * @return \stdClass or false if nothing found
         */
        static public function quickSelect(array $where=[],array $fields=['*']){
            return self::getInstance()->iQuickSelect($where,$fields);
        }
        
        /**
         *
         * @param string $table db.tbl or just tbl
         * @param array<string,mixed> $where
         * @param array<string> $fields
         * @return \stdClass|NULL
         */
        static public function quickSelectJoin(string $table,array $where=[],array $fields=['*']):?\stdClass{
            return self::getInstance()->iQuickSelectJoin($table,$where,$fields);
        }
        
        /**
         *  Returns an array of objects unless only a single field is selected.
         *  When a single field is selected an array is returned
         *
         *  To get [id => [full record] use $mode = PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC and make sure id is the first field
         *
         *  @return array
         */
        /**
         * 
         * @param array<string,mixed> $where
         * @param array<string> $fields
         * @param string $append_sql
         * @param int $mode
         * @return array<mixed>
         */
        static public function select(array $where=[],array $fields=['*'],$append_sql='',$mode = \PDO::FETCH_OBJ):array{
            return self::getInstance()->iSelect($where,$fields,$append_sql,$mode);
        }
        
        /**
         *  Returns an array of objects unless only a single field is selected.
         *  When a single field is selected an array is returned
         *
         *  To get [id => [full record] use $mode = PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC and make sure id is the first field
         *
         * @param string $table
         * @param array<string,mixed> $where
         * @param array<string> $fields
         * @param string $append_sql
         * @param int $mode
         * @param boolean $left_join
         * @return array<mixed>
         */
        static public function selectJoin(string $table,$where=[],array $fields=['*'],$append_sql='',$mode = \PDO::FETCH_OBJ,$left_join=false):array{
            return self::getInstance()->iSelectJoin($table,$where,$fields,$append_sql,$mode,$left_join);
        }
        
        /**
         * Shortcut to get key/pair result
         *
         * @param array<string,mixed> $where
         * @param array<string> $fields
         * @param string $append_sql
         * @return array<string,mixed>
         */
        static function selectKeyValue(array $where=[],array $fields=['*'],$append_sql=''):array{
            return static::select($where,$fields,$append_sql,\PDO::FETCH_KEY_PAIR);
        }
        
        /**
         * Shortcut to get key value pair result
         * @param string $table
         * @param array<string,mixed> $where
         * @param array<string> $fields
         * @param string $append_sql
         * @return array<mixed>
         */
        static public function selectJoinKeyPair(string $table,$where=[],array $fields=['*'],string $append_sql=''):array{
            return self::selectJoin($table,$where,$fields,$append_sql,\PDO::FETCH_KEY_PAIR);
        }
        
        /**
         * COUNT
         *
         * @param array<string,mixed> $where
         * @param string $field (default *)
         * @param boolean $distinct
         * @return integer number of records found.
         */
        static public function count(array $where=[],$field='*',$distinct=false):int{
            return self::getInstance()->iCount($where,$field,$distinct);
        }
        
        /**
         * @param string $table
         * @param array<string,mixed> $where
         * @param string $field ONE FIELD ONLY!
         * @param bool $distinct whether to add the DISTINCT keyword INSIDE the COUNT ... COUNT(DISTINCT ...)
         */
        static public function countJoin($table,array $where=[],$field='*',$distinct=false):int{
            $dis = $distinct?'DISTINCT ':'';
            $field = "COUNT({$dis}{$field})";
            return self::selectJoin($table,$where,[$field])[0];
        }
        
        /**
         * Ignore duplicates is false - so an excpetion is triggerd on duplicates
         *
         * @param array<string,mixed> $records
         * @return string
         */
        static public function createRecord(array $records):string{
            return self::getInstance()->insertData($records,false);
        }
        
        /**
         * Create many records
         *
         * @param array<int, array<string, mixed>> $records
	     * @return string
         */
        static public function createMultipleRecords(array $records):string{
            return self::getInstance()->insertMultipleData($records,false);
        }
        
        /**
         * Create many records, on update - Duplicate
         *
         * @param array<int, array<string, mixed>> $records
	     * @return string
         */
        static public function createUpdateMultipleRecords(array $records):string{
            return self::getInstance()->insertMultipleData($records,true);
        }
        
        /**
         * Create on duplicate Update one or many records
         *
         * @param array<string, mixed> $records
         * @return string
         */
        static public function createUpdateRecord(array $records):string{
            return self::getInstance()->insertData($records,true);
        }
        
        /**
        * @param array<string, mixed> $data
	    * @param array<string, mixed> $where
	    * @return int last inserted array
         */
        static public function updateRecord(array $data,array $where):int{
            return self::getInstance()->iUpdateRecord($data,$where)->numRows;
        }
        
        /**TOBEDELETED2118
         * Static call to update multiple row function
         * @param array $data
         * @return
         
        public static function updateMultipleRecords(array $data){
            return self::getInstance()->updateMultipleData($data);
        }
        */
        /**
         * @param array<string, mixed> $where
         * @return int last inserted array
         */
        static public function deleteRecord(array $where):int{
            return self::getInstance()->deleteData($where)->numRows;
        }
        
        /**
         * @param string $connection_name
         */
        final public function __construct(string $connection_name){
            if($connection_name) {
                $this->connection_name = $connection_name;
            }
        }
        
        /**
         * @return MssqlClient
         */
        protected function db():MssqlClient{
            return Factory::getConnectionMsSql($this->connection_name);
        }
        
        /**
         *  Apply filters and dependencies (validators)
         *  @param 	array<int, array<string, mixed>>|array<string, mixed>  $data
	     *  @return array<int, array<string, mixed>>|array<string, mixed>         
	     */
        protected function cleanData(array $data):array{
            return $data;
        }
        
        /**
    	 * Wrapper for INSERT queries. Default sets the DB obj to a WRITE one.
    	 * @param string $sql
    	 * @param array<string,mixed> $params
    	 * @return \SiTEL\DataSources\Sql\MssqlClient
    	 */
        protected function insert($sql,array $params=[]):MssqlClient{
            return $this->db()->insert($sql,$params);
        }//EOF insert
        
        /**
         * Prepare an INSERT statment from array of input
         *
         * Input is array where the keys are the field names in the DB
         * and the values are the values to insert.
         * ! DO NOT INCLUDE THE CONTROL FIELDS!
         * @param array<string,mixed> $data
    	 * @param bool $on_duplicate_update
    	 * @return string
         */
        public function insertData(array $data, bool $on_duplicate_update=false):string{
            $data=[$data];
            if(!$on_duplicate_update || ($on_duplicate_update == true && !$this->unique_keys)) {
                return $this->insertMultipleData($data,$on_duplicate_update);
            } else {
                return $this->insertUpdateMultipleDataRaw($data,$on_duplicate_update);
            }
        }
        
        /**
         * Create a multiple insert command.
         * TODO (need to think about this) Will perform an insert using a batch of no more than 50 records each time.
         * Will clean all incomming data.
         *
         * I expect the data to arrive as ['field_name']=>$value
         *
    	 * @param array<int, array<string, mixed>> $data
    	 * @param bool $on_duplicate_update
    	 * @return string
         */
        public function insertmultipledata(array $data,bool $on_duplicate_update=false):string{
            $datas = array_chunk($data, 4);
            $id = '';
            foreach ($datas as $d) {
                if (!$on_duplicate_update || ($on_duplicate_update == true && !$this->unique_keys)) {
                    $id = $this->insertMultipleDataRaw($d, $on_duplicate_update);
                } else { 
                    $id = $this->insertUpdateMultipleDataRaw($d, $on_duplicate_update);
                }
                
            }
            return $id;
        }
        
        /**
         * Insert, on duplicate update
         * NOTE: we can only do this one record at a time
         * @param array<int, array<string, mixed>> $data
         * @param boolean $on_duplicate_update
         * @return string
         */
        protected function insertUpdateMultipleDataRaw(array $data,bool $on_duplicate_update=false):string{
            $data           = $this->cleanData($data);
            $this->preInsertEvent($data);
            $fields         = array_keys(Shortcuts::cleanControlFields($data[0]));//get the field names for the insert
            $fields_str     = join(',',$fields);
            $modified_by    = self::$current_user;
            $id             = '';
            
            $insert_control_fields          = '';
            $insert_control_fields_values   = '';
            
            if($this->use_control_fields){
                $insert_control_fields          = ',date_created,created_by,modified_by';
                $insert_control_fields_values   = ",GETDATE(),{$modified_by},{$modified_by}";
            } 
            
            foreach($data as $k=>$cell){
                foreach($this->unique_keys as $unique_key) {
                    $sql="INSERT ({$fields_str}{$insert_control_fields})\nVALUES\n";
                    $key_fields = [];
                    $key_join = [];
                    $update_fields = [];
                    $params = [];
                    
                    $data[$k] = $this->cleanData(Shortcuts::cleanControlFields($cell));
                    $sql .= '(:' . join("{$k},:",$fields) . "{$k}{$insert_control_fields_values});";
                    
                    foreach($fields as $field){
                        
                        $current_param_index = ':'.$field.$k;
                        
                        if( isset($data[$k][$field]) ){
                            $current_value = $data[$k][$field];
                            if(is_object($current_value) && get_class($current_value) == 'Data_MySQL_UnmodifiedSql'){
                                $sql = str_replace($current_param_index,get_class($current_value),$sql);
                            }else{
                                $params[$current_param_index] = $current_value;
                            }
                            
                            if(in_array($field, $unique_key)) {
                                $key_param = "{$current_param_index}_key";
                                $key_fields[] = "{$key_param} AS $field";
                                $key_join[] = "target.{$field} = source.{$field}";
                                
                                $params[$key_param] = $current_value;
                            }
                            
                            $update_param = "{$current_param_index}_update";
                            $update_fields[] = "{$field} = {$update_param}";
                            $params[$update_param] = $current_value;
                            
                        }else{
                            $params[$current_param_index] = null;
                        }
                    }
                    
                    $key_fields_sql     = implode(',', $key_fields);
                    $unique_keys_sql    = implode(',', $unique_key);
                    $key_join_sql       = implode(' AND ', $key_join);
                    $update_fields_sql  = implode(',', $update_fields);
                    
                    $on_duplicate_sql = "
                    MERGE INTO {$this->schema_name}.{$this->table_name} WITH (HOLDLOCK) AS target
                    USING (SELECT {$key_fields_sql}) AS source ({$unique_keys_sql})
                    ON ({$key_join_sql})
                    WHEN MATCHED
                      THEN UPDATE
                          SET {$update_fields_sql}
                    WHEN NOT MATCHED
                      THEN ";
                    
                    $sql=$on_duplicate_sql.$sql;
                    $ret = $this->insert($sql,$params);
                    $id = $ret->lastInsertID;
                }
            }
            
            /*TOBEDELETED2118
             if($this->has_delta){
             $this->createInsertDeltaRecord(count($data));
             }
             */
            $this->postInsertEvent($data, $id);
            return $id;
        }
        
        /**
         * Insert multiple rows
         * @param array<string,mixed> $data
         * @param boolean $on_duplicate_update
         * @return string
         */
        protected function insertMultipleDataRaw(array $data,bool $on_duplicate_update=false):string{
            $data = $this->cleanData($data);
            $this->preInsertEvent($data);
            $fields=array_keys(Shortcuts::cleanControlFields($data[0]));//get the field names for the insert
            $fields_str=join(',',$fields);
            $modified_by = self::$current_user;
            
            $insert_control_fields          = '';
            $insert_control_fields_values   = '';
            
            if($this->use_control_fields){
                $insert_control_fields          = ',date_created,created_by,modified_by';
                $insert_control_fields_values   = ",GETDATE(),{$modified_by},{$modified_by}";
            } 
            
            $sql="INSERT INTO {$this->schema_name}.{$this->table_name} ({$fields_str}{$insert_control_fields})\nVALUES\n";
            $params=[];
            
            foreach($data as $k=>$cell){
                $data[$k] = $this->cleanData(Shortcuts::cleanControlFields($cell));
                $sql .= '(:' . join("{$k},:",$fields) . "{$k}{$insert_control_fields_values}),\n";
                foreach($fields as $field){
                    $current_param_index = ':'.$field.$k;
                    
                    if( isset($data[$k][$field]) ){
                        $current_value = $data[$k][$field];
                        if(is_object($current_value) && get_class($current_value) == 'Data_MySQL_UnmodifiedSql'){
                            $sql = str_replace($current_param_index, get_class($current_value), $sql);
                        }else{
                            $params[$current_param_index] = $current_value;
                        }
                        
                    }else{
                        $params[$current_param_index] = null;
                    }
                }
            }
            
            $sql=substr($sql,0,-2);
            $ret = $this->insert($sql,$params);
            $id = $ret->lastInsertID;
            /*TOBEDELETED2118
            if($this->has_delta){
                $this->createInsertDeltaRecord(count($data));
            }
            */
            $this->postInsertEvent($data, $id);
            return $id;
        }
        
        /**
         * Wrapper for UPDATE queries. Default sets the DB obj to a WRITE one.
         *
         * @return MssqlClient
         */
        /**
         * 
         * @param string $sql
         * @param array<string,mixed> $params
         * @return MssqlClient
         */
        public function update($sql,array $params=[]):MssqlClient{
            return $this->db()->update($sql,$params);
        }//EOF update
        
        /**
         * Creates an update from an array of data.
         * It will explode each cell ofthe array into an AND condition. For more complex conditions,
         * Write your own SQL.
         * @param array<string, mixed> $values
	     * @param array<string, mixed> $where
	     * @param bool $clean_values
	     * @param bool $clean_where
         * @return MssqlClient
         */
        public function iUpdateRecord(array $values,array $where=[],bool $clean_values=true,bool $clean_where=true):MssqlClient{
            //get SET fields
            $params=[];
            $values = $this->cleanData($values);
            $set=Shortcuts::generateSetData($values,$params,$clean_values,self::$current_user, Shortcuts::DATE_NOW__MSSQL);
            //Clean the where array and add to the $params array and rebuild the $where array
            $where_sql = Shortcuts::generateWhereData($where,$params,$clean_where);
            //sql
            /*TOBEDELETED2118            
            if($this->has_delta){
                $this->createUpdateDeltaRecord($where);
            }*/
            $this->preUpdateEvent([$values,$where]);
            $sql="UPDATE
    		{$this->schema_name}.{$this->table_name}
    		SET
    		{$set}
    		WHERE
    		{$where_sql}";
    		$ret= $this->update($sql,$params);
    		$edited_rows = $this->db()->numRows;
    		$this->postUpdateEvent([$values,$where]);
    		$this->db()->numRows = $edited_rows;
    		
    		return $ret;
        }
        
        /**
         * Wrapper for DELETE queries. Default sets the DB obj to a WRITE one.
         * @param string $sql
         * @param array<string, mixed> $params
         * @return MssqlClient
         */
        public function delete(string $sql,array $params=[]):MssqlClient{
            return $this->db()->delete($sql,$params);
        }//EOF delete
        
        /**
         * Shortcut for simple DELETE queries.
         *
         * @param array<string, mixed> $where array of where clauses, ONLY ANDs
         * @param boolean $clean_where wether to clean the where params or not.
         *
         * @return MssqlClient
         */
        public function deleteData(array $where,bool $clean_where=true):MssqlClient{
            $params=[];
            $sql_where=Shortcuts::generateWhereData($where,$params,$clean_where);
            if($sql_where) $sql="DELETE FROM {$this->schema_name}.{$this->table_name} WHERE {$sql_where}";
            else throw new \Exception('NO TRUNCATE IS ALLOWED - use where'); //$sql="TRUNCATE `{$this->tableName}`";
            
            $this->preDeleteEvent($where);
            $ret = $this->delete($sql,$params);
            $this->postDeleteEvent($where);
            return $ret;
        }
        
        /**
         *
         * @param string $command INSERT | UPDATE | DELETE
         * @param array<string,mixed> $where
         */
        protected function sendEventMessage(string $command,array $where) : void {
            /*Silenced until a replacement is done
             $msg = DataPusher_ActiveMQ_Publisher_Events::construct_message($this->database_name,$this->table_name,$command,$where);
             return DataPusher_ActiveMQ_Publisher_Events::get_client()->publish($msg);
             */
        }
        
        /**
         * Event called before insert
         * @param array<int, array<string, mixed>>|array<string, mixed> $params
         * @return MssqlTableHub
         */
        protected function preInsertEvent(array $params):MssqlTableHub{
            return $this;
        }
        
        /**
         * Event called before deletion.
         * @param array<int, array<string, mixed>>|array<string, mixed> $params
         * @return MssqlTableHub
         */
        protected function preDeleteEvent(array $params):MssqlTableHub{
            return $this;
        }
        
        /**
         *  Event called before update
         *  @param array<int, array<string, mixed>>|array<string, mixed> $params
         *  @return MssqlTableHub
         */
        protected function preUpdateEvent(array $params=[]):MssqlTableHub{
            return $this;
        }
        
        /**
         *  Event called before a mass update
         *  @param array<int, array<string, mixed>>|array<string, mixed> $params
         *  @return MssqlTableHub
         */
        protected function preMultipleUpdateEvent(array $params=[]):MssqlTableHub{
            return $this;
        }
        
        /**
         * Event called after insert
         * @param array<int, array<string, mixed>>|array<string, mixed> $params
         * @param string $last_insert_id
         * @return MssqlTableHub
         */
        protected function postInsertEvent(array $params, string $last_insert_id):MssqlTableHub{
            return $this;
        }
        
        /**
         *  Event called after delete
         *  @param array<int, array<string, mixed>>|array<string, mixed> $params
         *  @return MssqlTableHub
         */
        protected function postDeleteEvent(array $params):MssqlTableHub{
            return $this;
        }
        
        /**
         *  Event called after update
         *  @param array<int, array<string, mixed>>|array<string, mixed> $params
         *  @return MssqlTableHub
         */
        protected function postUpdateEvent(array $params):MssqlTableHub{
            return $this;
        }
        
        /**
         *  Event called after mass update
         *  @param array<int, array<string, mixed>>|array<string, mixed> $params
         *  @return MssqlTableHub
         */
        protected function postMultipleUpdateEvent(array $params):MssqlTableHub{
            return $this;
        }
        
        /**
         * Return a stdObj with all the fields values in the table for the specified id/where statment
         *
         * @param array<string, mixed> $where not mandatory
         * @param array<string> $fields not mandatory 9will fetch all fields
         * @return mixed
         */
        public function iQuickSelect(array $where=[],array $fields=['*']){
            return $this->selectFields($fields,$where,true,"ORDER BY {$this->order_by} OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY")->fetchObj();
        }
        
        /**
         *
         * @param string $table db.tbl
         * @param array<string, mixed> $where
         * @param array<string> $fields
         * @return ?\stdClass
         */
        public function iQuickSelectJoin(string $table,array $where=[],array $fields=['*']):?\stdClass{
            $join = "JOIN {$table} ON {$this->table_name}.id = {$table}.{$this->table_name}_id";
            return $this->selectFields($fields,$where,true,"ORDER BY {$this->table_name}.{$this->order_by} OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY",[],$join)->fetchObj();
        }
        
        /**
         * Return a stdObj with all the fields values in the table for the specified id/where statment
         *
         * @param array<string, mixed> $where not mandatory
         * @param array<string> $fields not mandatory will fetch all fields
         * @param string $append_sql
         * @param int $mode
         * @return array<mixed>
         */
        public function iSelect(array $where=[],array $fields=['*'],$append_sql='',$mode = \PDO::FETCH_OBJ):array{
            $res = $this->selectFields($fields,$where,true,$append_sql);
            
            if(count($fields) == 1 && $fields[0] != '*'){
                return $res->fetchAllColumn();
            }
            return $res->fetchAll($mode);
        }
        
        /**
         * Return a stdObj with all the fields values in the table for the specified id/where statment
         * @param string $table
         * @param array<string,mixed> $where not mandatory
         * @param array<string> $fields not mandatory 9will fetch all fields
         * @param string $append_sql
         * @param int $mode
         * @return array<mixed>
         */
        public function iSelectJoin(string $table,array $where=[],array $fields=['*'],string $append_sql='',$mode = \PDO::FETCH_OBJ,bool $left_join = false):array{
            $join = ($left_join?'LEFT ':'') . "JOIN {$table} ON {$this->table_name}.id = {$table}.{$this->table_name}_id";
            $res = $this->selectFields($fields,$where,true, $append_sql,[],$join);
            
            if(count($fields) == 1 && $fields[0] != '*'){
                return $res->fetchAllColumn();
            }
            return $res->fetchAll($mode);
        }
        
        /**
         * COUNT
         *
         * @param array<string, mixed> $where
    	 * @param string $field
    	 * @param boolean $distinct
    	 * @return int number of records found.
         */
        public function iCount(array $where=array(),string $field='*',bool $distinct=false):int{
            return $this->selectCount($where,$field,$distinct);
        }
        
        /**
         * Quick method to get list of enum values for one field
         *
         * @param string $field_name
         *
         * @return array of enum values
        //Todo -> implement getEnumValues
        public function iGetEnumValues($field_name):array{
            return $this->db()->getEnumValues($this->database_name . '.' . $this->table_name, $field_name);
        }
        */
        
        /**
         *  Functions moved from old DL to remove the need for DL
         *  Selects a set of fields based on where array
         *  @param array<string>  $fields			Fields to be selected from query
         *  @param array<string,mixed>  $where		    Where portion of select query
         *  @param bool   $clean_where		Boolean determining if where cleaning is needed
         *  @param string $concat_sql		String to be appended to the end of the query (Grouping and Limits)
         *  @param array<string, mixed> $concat_params	Array of params that require concatination
         *  @param string $join_stmt
         *  @return MssqlClient
         */
        protected function selectFields( array $fields,array $where=[], bool $clean_where=true, string $concat_sql='',array $concat_params=[],string $join_stmt=''):MssqlClient
        {
            $fields=join(',',$fields);
            $params=[];
            $where= Shortcuts::generateWhereData($where,$params,$clean_where);
            $params=array_merge($params,$concat_params);
            $where = $where ? ' WHERE ' . $where : '';
            $sql="SELECT {$fields} FROM {$this->schema_name}.{$this->table_name} {$join_stmt} {$where} {$concat_sql}";
            return $this->db()->select($sql,$params);
        }
        
        /**
         *  Moved from DL
         *  Performs a count.  Originally contained $table param.  Not need in Hub though.
         *  @param array<string, mixed>	$whereData
         *  @param string	$field
         *  @param bool 	$distinct
         *  @return int
         */
        protected function selectCount(array $whereData,string $field='*',bool $distinct=false):int {
            $distinct=$distinct?' DISTINCT ':'';
            $params=array();
            $where = Shortcuts::generateWhereData($whereData,$params,true);
            if($where)$where=' WHERE '.$where;
            else $where='';
            $sql = "
		SELECT
		COUNT({$distinct}{$field}) AS c
		FROM
		{$this->schema_name}.{$this->table_name}
		{$where}";
		$res = $this->db()->select($sql, $params)->fetchAllObj();
		return $res[0]->c;
        }
}//EOF CLASS
