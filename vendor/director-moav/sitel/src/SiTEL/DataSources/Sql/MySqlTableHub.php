<?php namespace SiTEL\DataSources\Sql;
/**
 * TODO: eventually the DL class and this class should have one common ancsestor
 *
 * @author 	Itay Moav
 * @date	01-18-2011
 * @date    2021-01-25 moved to sitelLib
 *
 * Centralized hub to do all Insert Delete Update (IDU) actions on a specific table
 */
abstract class MySqlTableHub{
	
    /**
     * @var string $DELTA_TYPE__EDIT
     * @var string $DELTA_TYPE__DELETE
     * @var string $DELTA_TYPE__CREATE
     */
	const
			DELTA_TYPE__EDIT	= 'edit',
			DELTA_TYPE__DELETE	= 'delete',
			DELTA_TYPE__CREATE	= 'create'
	;
	
	/**
	 * Databse name to manage IDU upon
	 *
	 * @var string database name
	 */
	protected string $database_name;
	
	/**
	 * Table name to manage IDU upon
	 *
	 * @var string table name
	 */
	protected string $table_name;
	
	/**
	 * Determines if the class has a delta table to record changes
	 *
	 * @var bool
	 */
	//SILENCED FOR NOW protected bool $has_delta = false;
	
	/**
	 * Array of filters and dependencies to either fail or modify data before we insert/update
	 * @var array<string, string>
	 */
	//SILENCED FOR NOW protected $data_clean_rules = [];
	
	/**
	 * @var MySqlClient
	 */
	protected MySqlClient $db_client;
	
    /**
     * 
     * @var integer
     */
	static protected int $current_user = 0;
        
	/**
	 * Sets the current user
	 */
	static public function setCurrentUser(int $current_user):void{
		self::$current_user=$current_user;
	}
	/**
	 * 
	 * @param string $connection_name leave empty to use default connection
	 * @return \SiTEL\DataSources\Sql\MySqlTableHub
	 */ 
	final static public function getInstance(string $connection_name=''):\SiTEL\DataSources\Sql\MySqlTableHub{
	    return new static($connection_name);
	}
	
	/**
	 * @param array<string, mixed> $where
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
	 * @param array<string, mixed> $where
	 * @param array<string> $fields
	 */
	static public function quickSelectJoin(string $table,array $where=[],array $fields=['*']):?\stdClass{
		return self::getInstance()->iQuickSelectJoin($table,$where,$fields);
	}
	
	/**
	 *  Returns an array of objects unless only a single field is selected.
	 *  When a single field is selected an array is returned
	 *
	 *  To get [id => [full record] use $mode = PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC and make sure id is the first field
	 * @param array<string, mixed> $where
	 * @param array<string> $fields
	 * @param string $append_sql
	 * @param int $mode
	 * @return array<mixed>
	 */
	static public function select(array $where=[],array $fields=['*'],$append_sql='',$mode = \PDO::FETCH_OBJ):array{
		return self::getInstance()->iSelect($where,$fields,$append_sql,$mode);
	}
	
	
	/**
	 * Returns an array of objects unless only a single field is selected.
	 * When a single field is selected an array is returned
	 * To get [id => [full record] use $mode = PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC and make sure id is the first field
	 * @param string $table
	 * @param array<string, mixed> $where
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
	 * @param array<string, mixed> $where
	 * @param array<string> $fields
	 * @param string $append_sql
	 * @return array<mixed>
	 */
	static function selectKeyValue(array $where=[],array $fields=['*'],$append_sql=''):array{
		return static::select($where,$fields,$append_sql,\PDO::FETCH_KEY_PAIR);
	}
	
	/**
	 * Shortcut to get key value pair result
	 * @param string $table
	 * @param array<string, mixed> $where
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
	 * @return int
	 */
	static public function countJoin(string $table,array $where=[],$field='*',$distinct=false):int{
		$dis = $distinct?'DISTINCT ':'';
		$field = "COUNT({$dis}{$field})";
		return self::selectJoin($table,$where,[$field])[0];
	}

    /**
     * Ignore duplicates is false - so an excpetion is triggerd on duplicates
     * 
     * @param array<string, mixed> $records
     * @param bool $ignore_duplicates
     * @return string
     */
	static public function createRecord(array $records,bool $ignore_duplicates=false):string{
		return self::getInstance()->insertData($records,false,$ignore_duplicates);
	}
	
	/**
	 * Create many records
	 * @param array<int, array<string, mixed>> $records
	 * @param boolean $ignore
	 * @return string
	 */
	static public function createMultipleRecords(array $records,bool $ignore=false):string{
		return self::getInstance()->insertMultipleData($records,false,$ignore);
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
	 * @param integer $id main entity id (like course, user, org)
	 * @param array<string, mixed> $records
	 * @return string last inserted id
	 */
	static public function createUpdateRecord($id,array $records):string{
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
	
	/**
	 * Static call to update multiple row function
	 * @param array<int, array<string, mixed>> $data
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	/*TOBEDELETED2106 
	public static function updateMultipleRecords(array $data):\SiTEL\DataSources\Sql\MySqlClient{
		return self::getInstance()->updateMultipleData($data);
	}
	*/
	/**
	 * 
	 * @param array<string, mixed> $where
	 * @return int
	 */
	static public function deleteRecord(array $where):int{
		return self::getInstance()->deleteData($where)->numRows;
	}
	
	/**
	 * 
	 * @param string $connection_name leave empty for default connection
	 */
	final public function __construct(string $connection_name=''){
	    $this->db_client = $connection_name ? Factory::getConnectionMySQL($connection_name) :
		                                      Factory::getDefaultConnectionMySql();
	}
	/**
	 * @return MySqlClient
	 */
	protected function db():MySqlClient{
		return $this->db_client;
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
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	protected function insert($sql,array $params=[]):\SiTEL\DataSources\Sql\MySqlClient{
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
	 * @param bool $ignore_duplicates
	 * @return string
	 */
	public function insertData(array $data, bool $on_duplicate_update=false,bool $ignore_duplicates=false):string{
		$data=[$data];
		return $this->insertMultipleData($data,$on_duplicate_update,$ignore_duplicates);
	}
	
	/**
	 * Create a multiple insert command.
	 * TODO (need to think about this) Will perform an insert using a batch of no more than 50 records each time.
	 * Will clean all incomming data.
	 *
	 * I expect the data to arrive as ['field_name']=>$value
	 * @param array<int, array<string, mixed>> $data
	 * @param boolean $on_duplicate_update
	 * @param boolean $ignore_duplicates
	 * @return string
	 */
	public function insertMultipleData(array $data,bool $on_duplicate_update=false,bool $ignore_duplicates=false):string{
		$datas = array_chunk($data, 4);
		$id = '';
		foreach ($datas as $d) {
		    $id = $this->insertMultipleDataRaw($d, $on_duplicate_update,$ignore_duplicates);
		}
		return $id;
	}
	/**
	 * 
	 * @param array<int, array<string, mixed>> $data
	 * @param boolean $on_duplicate_update
	 * @param boolean $ignore_duplicates
	 * @return string
	 */
	protected function insertMultipleDataRaw(array $data,$on_duplicate_update=false,$ignore_duplicates=false):string{
	    $put_ignore = $ignore_duplicates?' IGNORE ':'';
		$data = $this->cleanData($data);
		$this->preInsertEvent($data);
		$fields=array_keys(Shortcuts::cleanControlFields($data[0]));//get the field names for the insert
		$fields_str=join('`,`',$fields);
		$modified_by = self::$current_user;
		$sql="INSERT {$put_ignore} INTO {$this->database_name}.{$this->table_name} (`{$fields_str}`,date_created,created_by,modified_by)\nVALUES\n";
		$params=array();
		
		foreach($data as $k=>$cell){
			$data[$k] = $this->cleanData(Shortcuts::cleanControlFields($cell));
			$sql .= '(:' . join("{$k},:",$fields) . "{$k},NOW(),{$modified_by},{$modified_by}),\n";
			foreach($fields as $field){
				$current_param_index = ':'.$field.$k;
				if( isset($data[$k][$field]) ){
					$params[$current_param_index] = $data[$k][$field];
				}else{
					$params[$current_param_index] = null;
				}
			}
		}
		
		$sql=substr($sql,0,-2);
		
		//on duplicate
		$sql=$this->onDuplicateSql($sql,$on_duplicate_update,$fields,$modified_by);
		$ret = $this->insert($sql,$params);
		$id = $ret->lastInsertID;
		/* createInsertDeltaRecord is silenced
		if($this->has_delta){
			$this->createInsertDeltaRecord(count($data));
		}*/
		$this->postInsertEvent($data, $id);
		return $id;
	}
	
	/**
	 * Generates the ON DUPLICATE part of the INSERT statment
	 * @param string $sql
	 * @param bool $on_duplicate_update
	 * @param array<int, int|string> $fields
	 * @param int $modified_by
	 * @return string
	 */
	protected function onDuplicateSql(string $sql,bool $on_duplicate_update,array $fields,int $modified_by){
		if($on_duplicate_update){
			$sql.=' ON DUPLICATE KEY UPDATE ';
			foreach($fields as $field){
				$sql.=" `{$field}`=VALUES(`{$field}`),";
			}
			$sql.="modified_by={$modified_by}";
		}
		return $sql;
	}
	
	/* silenced until replaced. This is a ledger of changes to a record.
	protected function createInsertDeltaRecord(int $n_of_records_just_created){
		$type        = self::DELTA_TYPE__CREATE;
		$explanation = Action_LogMsgs::getActionLogMsg()?:
		                 "History record created without reason or type for table {$this->databaseName}.{$this->tableName}. U need to setup message for this action.";
		$user        = \User_Current::pupetMasterId();
		$sql = "
			INSERT INTO {this.database_name}_delta.{$this->table_name}
			SELECT *, CURRENT_TIMESTAMP,'{$type}','{$explanation}',{$user}
			FROM {this.database_name}.{$this->table_name}
			ORDER BY date_created DESC
			LIMIT {$n_of_records_just_created}";//TODO make the order by the key, which means I should define it in the hub too.
		try{
			$this->insert($sql);
		} catch(Exception $e){
			throw new FailedDeltaRecordCreation("Failed creating INSERT delta records for {$this->databaseName}.{$this->tableName}");
		}
	}
	
	protected function createUpdateDeltaRecord(array $where){
		$type        = self::DELTA_TYPE__EDIT;
		$explanation = Action_LogMsgs::getActionLogMsg()?:
		"History record created without reason or type for table {$this->databaseName}.{$this->tableName}. U need to setup message for this action.";
		$user        = \User_Current::pupetMasterId();
		$params      = [];
		$where_sql   = Shortcuts::generateWhereData($where,$params,true);
		$sql = "
		INSERT INTO {this.database_name}_delta.{$this->table_name}
		SELECT *, CURRENT_TIMESTAMP,'{$type}','{$explanation}',{$user}
		FROM {this.database_name}.{$this->table_name}
		WHERE {$where_sql}
		";
		try{
			$this->insert($sql,$params);
		} catch(\Exception $e){
			throw new FailedDeltaRecordCreation("Failed creating UPDATE delta records for {$this->databaseName}.{$this->tableName}");
		}
	}*/

	/**
	 * Wrapper for UPDATE queries. Default sets the DB obj to a WRITE one.
	 * @param string $sql
	 * @param array<string,mixed> $params
	 * @return MySqlClient
	 */
	public function update($sql,array $params=[]):MySqlClient{
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
	 * @return MySqlClient
	 */
	public function iUpdateRecord(array $values,array $where=[],bool $clean_values=true,bool $clean_where=true):MySqlClient{
		//get SET fields
		$params=[];
		$values = $this->cleanData($values);
		$set=Shortcuts::generateSetData($values,$params,$clean_values,self::$current_user);
		//Clean the where array and add to the $params array and rebuild the $where array
		$where_sql = Shortcuts::generateWhereData($where,$params,$clean_where);
		//sql
		/* createUpdateDeltaRecord is silenced
		if($this->has_delta){
			$this->createUpdateDeltaRecord($where);
		}*/
		$this->preUpdateEvent([$values,$where]);
		$sql="UPDATE
		{$this->database_name}.{$this->table_name}
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
	 * @return MySqlClient
	 */
	public function delete(string $sql,array $params=[]):MySqlClient{
		return $this->db()->delete($sql,$params);
	}//EOF delete
	
	/**
	 * Shortcut for simple DELETE queries.
	 *
	 * @param array<string, mixed> $where array of where clauses, ONLY ANDs
	 * @param boolean $clean_where wether to clean the where params or not.
	 *
	 * @return MySqlClient
	 */
	public function deleteData(array $where,bool $clean_where=true):MySqlClient{
		$params=[];
		$sql_where=Shortcuts::generateWhereData($where,$params,$clean_where);
		if($sql_where) $sql="DELETE FROM {$this->database_name}.{$this->table_name} WHERE {$sql_where}";
		else throw new \Exception('NO TRUNCATE IS ALLOWED - use where'); //$sql="TRUNCATE `{$this->tableName}`";
		
		$this->preDeleteEvent($where);
		$ret = $this->delete($sql,$params);
		$this->postDeleteEvent($where);
		return $ret;
	}
	


	/**
	 *
	 * @param string $command INSERT | UPDATE | DELETE
	 * @param array<string, mixed> $where
	 * @return string json encoded Events queue message {source:db,table:table,event_type: 'event_type', params:{field:value,field:value}}
	 */
	protected function sendEventMessage($command,$where){
	    /*Silenced until a replacement is done
		$msg = DataPusher_ActiveMQ_Publisher_Events::construct_message($this->database_name,$this->table_name,$command,$where);
		return DataPusher_ActiveMQ_Publisher_Events::get_client()->publish($msg);
		*/
	    return '';
	}
	
	/**
	 * Event called before insert
	 * @param array<int, array<string, mixed>>|array<string, mixed> $param
	 * @return MySqlTableHub
	 */
	protected function preInsertEvent(array $param){
		return $this;
	}
	
	/**
	 * Event called before deletion.
	 * @param array<string, mixed> $param
	 * @return MySqlTableHub
	 */
	protected function preDeleteEvent(array $param){
		return $this;
	}
	
	/**
	 *  Event called before update
	 *  @param array<string,mixed>|array[] $param
	 *  @return MySqlTableHub
	 */
	protected function preUpdateEvent(array $param=[]){
		return $this;
	}
	
	/**
	 *  Event called before a mass update
	 *  @param array<int, array<string, mixed>> $param
	 *  @return MySqlTableHub
	 */
	protected function preMultipleUpdateEvent(array $param=[]){
		return $this;
	}
	
	/**
	 * Event called after insert
	 * @param array<string, mixed>|array<int, array<string, mixed>> $params
	 * @param string $last_insert_id
	 * @return MySqlTableHub
	 */
	protected function postInsertEvent(array $params, string $last_insert_id){
		return $this;
	}
	
	/**
	 *  Event called after delete
	 *  @param array<string, mixed> $params
	 *  @return MySqlTableHub
	 */
	protected function postDeleteEvent(array $params){
		return $this;
	}
	
	/**
	 *  Event called after update
	 *  @param array<int, array<string, mixed>>|array<string, mixed> $params
	 *  @return MySqlTableHub
	 */
	protected function postUpdateEvent(array $params){
		return $this;
	}
	
	/**
	 *  Event called after mass update
	 *  @param array<int, array<string, mixed>> $params
	 *  @return MySqlTableHub
	 */
	protected function postMultipleUpdateEvent(array $params){
		return $this;
	}

	/**
	 * Return a stdObj with all the fields values in the table for the specified id/where statment
	 *
	 * @param array<string,mixed> $where not mandatory
	 * @param array<string> $fields not mandatory 9will fetch all fields
	 *
	 * @return mixed
	 */
	public function iQuickSelect(array $where=[],array $fields=['*']){
		return $this->selectFields($fields,$where,true,'LIMIT 1')->fetchObj();
	}
	
	/**
	 *
	 * @param String $table db.tbl
	 * @param array<string, string> $where
	 * @param string[] $fields
	 * @return ?\stdClass
	 */
	public function iQuickSelectJoin(string $table,array $where=[],array $fields=['*']):?\stdClass{
		$join = "JOIN {$table} ON {$this->table_name}.id = {$table}.{$this->table_name}_id";
		return $this->selectFields($fields,$where,true,'LIMIT 1',[],$join)->fetchObj();
	}
	
	/**
	 * @param array<string,mixed> $where not mandatory
	 * @param array<string> $fields not mandatory will fetch all fields
	 * @param int $mode
	 * @return array<mixed>
	 */
	public function iSelect(array $where=[],array $fields=['*'],string $append_sql='',int $mode = \PDO::FETCH_OBJ):array{
		$res = $this->selectFields($fields,$where,true,$append_sql);
		
		if(count($fields) == 1 && $fields[0] != '*'){
			return $res->fetchAllColumn();
		}
		return $res->fetchAll($mode);
	}
	
	/**
	 * Return a stdObj with all the fields values in the table for the specified id/where statment
	 * @param string $table
	 * @param array<string, mixed> $where not mandatory
	 * @param array<string> $fields not mandatory 9will fetch all fields
	 * @param string $append_sql
	 * @param int $mode
	 * @param bool $left_join
	 * @return array<mixed>
	 */
	public function iSelectJoin($table,array $where=[],array $fields=['*'], $append_sql='',$mode = \PDO::FETCH_OBJ,bool $left_join = false):array{
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
	 * @return integer number of records found.
	 */
	public function iCount(array $where=array(),$field='*',$distinct=false):int{
		return $this->selectCount($where,$field,$distinct);
	}
	
	/**
	 *  Functions moved from old DL to remove the need for DL
	 *  Selects a set of fields based on where array
	 *  @param array<string>  $fields			Fields to be selected from query
	 *  @param array<string,mixed>  $where		    Where portion of select query
	 *  @param bool   $clean_where		Boolean determining if where cleaning is needed
	 *  @param string $concat_sql		String to be appended to the end of the query (Grouping and Limits)
	 *  @param array<string> $concat_params	Array of params that require concatination
	 *  @param string $join_stmt
	 *  @return MySqlClient
	 */
	protected function selectFields( array $fields,array $where=[], bool $clean_where=true, string $concat_sql='',array $concat_params=[],string $join_stmt=''):MySqlClient
	{
		$fields=join(',',$fields);
		$params=[];
		$where= Shortcuts::generateWhereData($where,$params,$clean_where);
		$params=array_merge($params,$concat_params);
		$where = $where ? ' WHERE ' . $where : '';
		$sql="SELECT {$fields} FROM {$this->database_name}.{$this->table_name} {$join_stmt} {$where} {$concat_sql}";
		return $this->db()->select($sql,$params);
	}
	
	/**
	 *  Moved from DL
	 *  Performs a count.  Originally contained $table param.  Not need in Hub though.
	 *  @param array<string,mixed>	$whereData
	 *  @param string	$field
	 *  @param bool 	$distinct
	 *  @return int
	 */
	protected function selectCount(array $whereData,$field='*',bool $distinct=false):int {
		$distinct=$distinct?' DISTINCT ':'';
		$params=array();
		$where = Shortcuts::generateWhereData($whereData,$params,true);
		if($where)$where=' WHERE '.$where;
		else $where='';
		$sql = "
		SELECT
		COUNT({$distinct}{$field}) AS c
		FROM
		{$this->database_name}.{$this->table_name}
		{$where}";
		$res = $this->db()->select($sql, $params)->fetchAllObj();
		return $res[0]->c;
	}
	
	/**
	 *  Get the value of hasDelta variable
	 */
	/**
	 * 
	 * @return bool
	 */
	/* SILENCED FOR NOW
	static public function getHasDelta(){
	    return self::getInstance('')->has_delta;
	}*/
	
}//EOF CLASS


class InconsistentDataException extends \Exception{
	
}

class NoDataException extends \Exception{
	
}

class FailedDeltaRecordCreation extends \Exception {}
