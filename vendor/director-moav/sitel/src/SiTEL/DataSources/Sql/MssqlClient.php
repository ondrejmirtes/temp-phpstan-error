<?php namespace SiTEL\DataSources\Sql;

/**
 * PDO MSSQL Wrapper
 *
 * EACH INSTANCE IS ONE CONNECTION
 *
 * @author Itay Moav
 * @date 2020-05-27
 */
class MssqlClient {
    
    /**
     * MySQL error codes
     */
    //const MSSQL_ERROR__ ....
    
    /**
     * In transaction flag
     * For nested flags this will increment 1,2,3...
     * 0 means no transaction
     *
     * @var int
     */
    private int $inTransaction = 0;
    
    /**
     * @var \ZimLogger\Streams\aLogStream
     */
    private \ZimLogger\Streams\aLogStream $Logger;
     
    /**
     * Native DB class.
     * Most likely PDO
     *
     * @var \PDO
     */
    private \PDO $NativeDB;
    
    /**
     * Last SQL which has been performed
     *
     * @var String
     */
    private string $lastSql = '';
    
    /**
     * Holds the last PDO Statment object
     *
     * @var \PDOStatement
     */
    private \PDOStatement $lastStatement;
    
    /**
     * Array of Parameters last used in the last SQL
     *
     * @var array<string, mixed>
     */
    private array $lastBindParams = [ ];
    
    /**
     * Number of the rows returned or affected
     *
     * @var Int
     */
    public int $numRows = 0;
    
    /**
     * Number of fields in returned rowset
     *
     * @var Int
     */
    public int $numFields = 0;
    
    /**
     * Holds the last inserted ID
     *
     * @var string
     */
    public string $lastInsertID;
    
    /**
     * Wether to execute the query or not.
     * Good to get back the SQL only, for Pagers, for example.
     * @var bool
     */
    private bool $noExecute = false;
    
    /**
     * Give a name to the connection so we can register/unregister in the factory
     * Helpfull for debugging all active connections
     * @var string
     */
    private string $connection_name;
    
    /**
     * last error code caught with no fail on error
     * When false, no error was caught
     *
     * @var integer
     */
    public int $lastErrorCode = 0;
    
    /**
     * Creating an instance
     * Although this is a type of sigleton, we are using a public modifier here, as we inherit the PDO class
     * which have a public constructor.
     * @param string $connection_name
     * @param array<string, mixed> $conf_data
     * @param \ZimLogger\Streams\aLogStream $Logger
     */ 
    public function __construct(string $connection_name, array $conf_data,\ZimLogger\Streams\aLogStream $Logger) {
        $this->Logger = $Logger;
        $this->connection_name = $connection_name;
        $dns = "sqlsrv:server = tcp:{$conf_data['server']},{$conf_data ['port']}; Database = {$conf_data ['database']}";
        $this->Logger->debug("Connecting to [{$dns}]");
        $this->NativeDB = new \PDO($dns,"{$conf_data ['username']}", "{$conf_data ['password']}");
        $this->NativeDB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     *
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function closeCursor() {
        $this->lastStatement->closeCursor();
        return $this;
    }
    
    /**
     * Executing the query.
     * 1. if fails in transaction - fails the entire thing and throws an exception
     * 2. If deadlocl/serialization - tries 10 times and then failes
     * 3. If duplicate entry - throw duplicate exception
     *
     * @param string $sql
     * @param array<string, mixed> $params
     * @throws \PDOException
     * @return void
     */
    private function execute(string $sql, array $params = []):void {
        $this->lastSql = $sql;
        $this->lastBindParams = $params;
        $this->Logger->debug($this->getDebugInfo());
        if ($this->noExecute)
            return;
            
            $DB = $this->NativeDB;
            
            // error handling
            try {
                
                if ($params) {
                    $this->lastStatement = $DB->prepare($sql);
                    $this->lastStatement->execute($params);
                    $error = $this->lastStatement->errorInfo() ;
                    
                    
                } else {
                    $query = $DB->query($sql);
                    if($query) $this->lastStatement = $query;
                    $error = $DB->errorInfo();
                }
                
                if($error[0] != '0000'){
                    $this->Logger->fatal("Query failed [{$sql}]",false);
                    $this->Logger->fatal($error,false);
                    throw new \Exception($error[2]);
                }
                
                $this->numFields = $this->lastStatement->columnCount(); 
                $this->numRows   = $this->lastStatement->rowCount();
                $this->Logger->debug("NUMFIELDS: [{$this->numFields}]\nNUMROWS: [{$this->numRows}]");
                
            } catch ( \PDOException $e ) {
                // The transaction was rolled back anyway, we need to stop!
                if ($this->inTransaction) {
                    throw $e;
                }
                
                // in some cases we automaticly try to re-submit the query, we give it just a few chance
                $code = $e->errorInfo[1];
                
                // handle each error specificaly
                switch ($code) {
                    /*TODO find replacerments
                    case (self::MYSQL_ERROR__LOCK_WAIT_TIMEOUT) : // SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction
                    case (self::MYSQL_ERROR__SERIALIZATION_FAILURE) : // SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction
                        sleep ( 10 );
                        $this->execute ( $sql, $params ); // RECURSSION!
                        break;
                        
                    case (self::MYSQL_ERROR__DUPLICATE_ENTRY) : // duplicate entries
                        throw new \Talis\Services\Sql\Exception\DuplicateEntry ( print_r ( $this->lastBindParams, true ) );
                        */
                    default :
                        throw $e;
                }
            }
    }
    
    /**
     * Entry point for select statments.
     * We have this spread of authorities for future use (like different server verifications)
     *
     * @param String $sql
     * @param array<string, mixed> $params
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function select(string $sql, array $params = []):\SiTEL\DataSources\Sql\MssqlClient{
        $this->execute($sql,$params);
        return $this;
    }
    
    /**
     * Insert a record
     *
     * @param String $sql
     * @param array<string, mixed> $params
     *        	(fieldanme=>value, fieldanme=>value, ...)
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function insert(string $sql, array $params = []):\SiTEL\DataSources\Sql\MssqlClient{
        $this->execute($sql,$params);
        $this->lastInsertID = $this->NativeDB->lastInsertId (); 
        return $this;
    }
    
    /**
     * Physically deletes a record or records from table
     *
     * @param String $sql
     * @param array<string, mixed> $params
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function delete(string $sql, array $params = []):\SiTEL\DataSources\Sql\MssqlClient{
        $this->execute($sql,$params);
        return $this;
    }
    
    /**
     * Updates a record
     *
     * @param String $sql
     * @param array<string, mixed> $bindparam
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function update(string $sql, array $bindparam = []):\SiTEL\DataSources\Sql\MssqlClient{
        $this->execute( $sql, $bindparam );
        return $this;
    }

    /**
     * Data definition - create|alter|drop
     * 
     * @param string $sql
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function ddl(string $sql):\SiTEL\DataSources\Sql\MssqlClient{
        $this->execute($sql);
        return $this;
    }
    
    /**
     * Returns the last statement Object
     *
     * @return \PDOStatement
     */
    private function getLastStatement():\PDOStatement{
        return $this->lastStatement;
    }
    
    /**
     * Returns the last SQL
     *
     * @return String
     */
    public function getLastSql():string {
        return $this->lastSql;
    }
    
    /**
     * Returns the last bind valye array
     *
     * @return array<string, mixed>
     */
    public function getLastbindParams():array {
        return $this->lastBindParams;
    }
    
    /**
     * Fetch the rowset based on the PDO Type (FETCH_ASSOC,...)
     *
     * @param integer $fetch_type
     * @return array<mixed, mixed>
     */
    public function fetchAll(int $fetch_type = \PDO::FETCH_ASSOC):array {
        $res = $this->lastStatement->fetchAll ( $fetch_type );
        return $res ?: [ ];
    }
    
    /**
     * Fetch the rowset based on the PDO Type (FETCH_OBJ)
     *
     * @return array<int, \stdClass>
     */
    public function fetchAllObj():array {
        $res =  $this->lastStatement->fetchAll ( \PDO::FETCH_OBJ ); 
        return $res ?: [ ];
    }
    
    /**
     * @param string $class_name
     * @param array<mixed, mixed> $ctor_args
     * @return array<int, mixed>
     */
    public function fetchAllUserObj(string $class_name, array $ctor_args = []):array {
       /** @phpstan-ignore-next-line Next line contains PHP8 phpstan issue changing before php 8 can result in errors with current version. */
        $res = $this->lastStatement->fetchAll ( \PDO::FETCH_CLASS, $class_name, $ctor_args ); 
        return $res ?: [ ];
    }
    
    /**
     * @param callable $func
     * @return array<mixed, mixed>
     */
    public function fetchAllUserFunc(callable $func):array {
        $res = $this->lastStatement->fetchAll ( \PDO::FETCH_FUNC, $func ); 
        return $res ?: [ ];
    }
    
    /**
     * returns the result index by the first selected field and an array of the
     * rest of the columns
     * @param callable $func
     * @return array<mixed, mixed>
     */
    public function fetchAllIndexed(callable $func):array { // THIS IS STILL THOUGHT UPON!
        $res = $this->lastStatement->fetchAll ( \PDO::FETCH_UNIQUE | \PDO::FETCH_FUNC, $func ); 
        return $res ?: [ ];
    }
    
    /**
     * Returns array structured [f1=>f2,f1=>f2,f1=>f2 ...
     * f1=>f2]
     *
     * @return array<string, mixed>
     */
    public function fetchAllPaired():array {
        $res = $this->lastStatement->fetchAll ( \PDO::FETCH_KEY_PAIR ); 
        return $res ?: [ ];
    }
    
    /**
     * Fetches one column as an array
     *
     * @param int $column
     *        	index in select list
     * @return array<int, int|string>
     */
    public function fetchAllColumn(int $column = 0):array {
        $res = $this->lastStatement->fetchAll ( \PDO::FETCH_COLUMN, $column ); 
        return $res ?: [ ];
    }
    
    /**
     * @param int $result_type
     * @return mixed
     */
    private function fetchRow(int $result_type) {
        return $this->lastStatement->fetch($result_type);
    }
    
    /**
     * @return array<int, mixed>
     */
    public function fetchNumericArray() :array {
        return $this->fetchRow(\PDO::FETCH_NUM);
    }
    
    /**
     * @return array<string, int|string>
     */
    public function fetchArray() :array {
        return $this->fetchRow(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @return \stdClass|null
     */
    public function fetchObj() :?\stdClass{
        return $this->fetchRow(\PDO::FETCH_OBJ) ?: null;
    }
    
    /**
     * Calls a sp
     * ATTENTION!!! I have no sanitation here!
     *
     * @param string $sp_name
     *
     * @return Client
     */
    /*TODO activate?
    public function call(string $sp_name):Client{
        $params = func_get_args ();
        unset ( $params [0] ); // this is the function name
        
        // convert params array into string to call sp function
        $sql_p = Shortcuts::generateInData ( $params );
        if (! $sql_p ['params']) { // for the IN statement we always get a value to prevent syntax error
            $sql_p ['str'] = '()';
        }
        $sql = "CALL {$sp_name}{$sql_p['str']}";
        return $this->select($sql,$sql_p['params']);
    }*/
    
    /**
     * If u use Omega, or wish to pass array of args instead of just args, choose this
     *
     * @param string $sp
     * @param array $args
     * @return MySqlClient
     */
    /*TODO activate?
    public function callArr($sp, array $args):MySqlClient{
        // convert params array into string to call sp function
        $sql_p = Shortcuts::generateInData ( $args );
        if (! $sql_p ['params']) { // for the IN statement we always get a value to prevent syntax error
            $sql_p ['str'] = '()';
        }
        $sql = "CALL {$sp}{$sql_p['str']}";
        return $this->select ( $sql, $sql_p ['params'] );
    }*/
    
    /**
     * Get the nested amount of transactions.
     * Can also determine if transaction is being used
     *
     * @return int
     */
    public function getTransaction():int {
        return $this->inTransaction;
    }
    
    /**
     * This function control the transaction flow & lock the auto commit.
     *
     * @throws \LogicException in case we are a read connection
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function beginTransaction():\SiTEL\DataSources\Sql\MssqlClient{
        $this->lastSql = 'BEGIN TRANSACTION';
        $this->lastBindParams = [ ];
        if (! $this->inTransaction) {
            $this->NativeDB->beginTransaction ();
        }
        $this->inTransaction ++;
        return $this;
    }
    
    /**
     * This function commit the transactions, reset the flag and returns
     * the true.
     * In case of error it rollbacks and returns false flag
     *
     * @throws \LogicException in case there is no transaction to close.
     * @return MssqlClient
     */
    public function endTransaction():\SiTEL\DataSources\Sql\MssqlClient{
        $this->lastSql = 'END TRANSACTION';
        $this->lastBindParams = [ ];
        
        switch ($this->inTransaction) {
            case 1 :
                $this->NativeDB->commit ();
                $this->inTransaction = 0;
                break;
                
            case 0 :
                throw new \LogicException ( 'Trying to close a closed transaction' );
                
            default :
                $this->inTransaction --;
                break;
        }
        
        return $this;
    }
    
    /**
     * This function rolls back the transactions, reset the flag and returns
     * the true.
     *
     * @return \SiTEL\DataSources\Sql\MssqlClient
     */
    public function rollbackTransaction():\SiTEL\DataSources\Sql\MssqlClient{
        $this->lastSql = 'ROLLBACK TRANSACTION';
        $this->lastBindParams = [];
        if ($this->inTransaction) {
            $this->NativeDB->rollBack ();
            $this->inTransaction = 0;
        } else {
            throw new \LogicException ( 'Trying to roleback a closed transaction' );
        }
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function inTransaction():bool {
        return $this->NativeDB->inTransaction ();
    }
    
    /**
     * Attempts to get Caller function.
     */
    private function getCaller():string {
        $bt = debug_backtrace ( BACKTRACE_MASK ); // @phpstan-ignore-line
        $stack = [ ];
        $i = 0;
        foreach ( $bt as $trace_line ) {
            if (! isset ( $trace_line ['file'] )) {
                $trace_line ['file'] = 'unknown, probably due to unittest reflection way';
            }
            if (! isset ( $trace_line ['line'] )) {
                $trace_line ['line'] = 'unknown, probably due to unittest reflection way';
            }
            
            if ($i > 4) {
                break;
            }
            $function = isset ( $trace_line ['function'] ) ? $trace_line ['function'] : '';
            // exclude some functions from debug trace
            if (in_array ( $function, array (
                'getCaller',
                'slog',
                'execute',
                'select',
                'update',
                'delete',
                'insert'
            ) )) {
                continue;
            }
            
            // unfold args
            $args = (isset ( $trace_line ['args'] ) && ! empty ( $trace_line ['args'] )) ? ' args: ' . print_r ( $trace_line ['args'], true ) : '';
            $stack [] = "{$trace_line['file']} ({$trace_line['line']}) function:{$function}{$args}";
            $i ++;
        }
        
        return implode ( PHP_EOL, $stack );
    }

    /**
     * Debug info for who ever wants it
     *
     * @return string
     */
    public function getDebugInfo():string{
        return "LAST SQL: \n{$this->lastSql}\nWith params:\n\n" . print_r($this->lastBindParams,true);
    }
}
