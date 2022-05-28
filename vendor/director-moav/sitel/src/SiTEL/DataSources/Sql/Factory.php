<?php namespace SiTEL\DataSources\Sql;

/**
 * Factory class to get a SQL client
 *
 * @author Itay Moav
 */
class Factory {
	const CONNECTION_NAME__READ  = 'mysql_read', 
		  CONNECTION_NAME__WRITE = 'mysql_write'
	;
	
	/**
	 *
	 * @var array<string,mixed> of registered (created with factory) connections.
	 */
	private static $registered_connections = [ ];

	/**
	 * @param string $connection_name
	 * @return bool
	 */
	static public function checkConnectionExists(string $connection_name):bool{
        return isset(self::$registered_connections[$connection_name]);	    
	}
	
	/**
	 * @param string $connection_name
	 * @param array<string, string> $config
	 * @param \ZimLogger\Streams\aLogStream $Logger
	 * @return \SiTEL\DataSources\Sql\MssqlClient
	 */
	static public function setConnectionMsSql(string $connection_name,array $config,\ZimLogger\Streams\aLogStream $Logger):\SiTEL\DataSources\Sql\MssqlClient{
	    return self::$registered_connections[$connection_name] = new \SiTEL\DataSources\Sql\MssqlClient($connection_name,$config,$Logger);
	}

	/**
	 * @param string $connection_name
	 * @return \SiTEL\DataSources\Sql\MssqlClient
	 */
	static public function getConnectionMsSql(string $connection_name):\SiTEL\DataSources\Sql\MssqlClient{
	    return self::$registered_connections[$connection_name];
	}
	
	/**
	 * @param string $connection_name
	 * @param array<string,mixed> $config
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	static public function getConnectionMySQL(string $connection_name, array $config = []) {
		if (isset ( self::$registered_connections [$connection_name] ))
			return self::$registered_connections [$connection_name];
		
		if ($config == [ ])
			throw new \LogicException ( 'You must pass a config array to get a connection' );
		
		return self::$registered_connections [$connection_name] = new MySqlClient ( $connection_name, $config );
	}
	
	/**
	 * @throws \LogicException
	 * @return MySqlClient
	 */
	static public function getDefaultConnectionMySql():MySqlClient{
		if(!self::$registered_connections){
			throw new \LogicException ('You must initilize one connection to use this method');
		}
		return reset(self::$registered_connections);
	}

	/**
	 * Check if a valid name was passed, if not will return the default connection
	 * 
	 * @param string $connection_name
	 * @param array<string,mixed> $config
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	static public function getConnectionOrDefaultMySQL(string $connection_name='', array $config = []):MySqlClient{
		if($connection_name){
			return self::getConnectionMySQL($connection_name,$config);
		}
		return self::getDefaultConnectionMySql();
	}
	
	/**
	 *
	 * @param string $connection_name        	
	 */
	static public function unregister(string $connection_name):void {
		unset ( self::$registered_connections [$connection_name] );
	}
	
	/**
	 *
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	static public function getReadConn() {
		return self::getConnectionMySQL ( self::CONNECTION_NAME__READ, app_env () ['database'] ['mysql_slave'] );
	}
	
	/**
	 *
	 * @return \SiTEL\DataSources\Sql\MySqlClient
	 */
	static public function getWriteConn() {
		return self::getConnectionMySQL ( self::CONNECTION_NAME__WRITE, app_env () ['database'] ['mysql_master'] );
	}
	
	/**
	 * 
	 * @return array<string, mixed>
	 */
	static public function getDebugInfo():array{
		$ret = [];
		foreach(self::$registered_connections as $name=>$connection){
			$ret[$name] = $connection->getDebugInfo();
		}
		return $ret;
	}
}
	
