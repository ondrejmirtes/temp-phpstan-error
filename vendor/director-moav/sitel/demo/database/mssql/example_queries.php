<?php
require_once './bootstrap.php';

$config = [
    'server'        => 'emeraldsandbox.database.windows.net',
    'database'      => 'adventureworks',
    'port'          => 1433,
    'username'      => 'emerald',
    'password'      => 'diamond.cptyyf28ahy5.us-east-1.rds.amazonaws.com'
];

SiTEL\DataSources\Sql\Factory::setConnectionMsSql('test', $config, \ZimLogger\MainZim::$CurrentLogger);

dbgn("\n\nHUBBING\n\n");

/******* Generic table ******/

dbgn('GENERIC HUB');

class TestHub extends \SiTEL\DataSources\Sql\MssqlTableHub {
    protected string $schema_name       = 'dbo';
    protected string $table_name        = 'holly_test';
    protected string $connection_name   = 'test';
}

dbgr('CREATE', TestHub::createRecord(['baba' => 11]));

dbgr('CREATE UPDATE MULTIPLE', TestHub::createMultipleRecords([['baba' => 12], ['baba' => 13]]));

dbgr('CREATE UPDATE', TestHub::createUpdateRecord(['baba' => 20]));

dbgr('CREATE UPDATE MULTIPLE', TestHub::createUpdateMultipleRecords([['baba' => 20], ['baba' => 21]]));

dbgr('SELECT', TestHub::quickSelect(['baba' => 20]));

dbgr('SELECT ALL', TestHub::select(['baba' => 20]));

dbgr('UPDATE', TestHub::updateRecord(['created_by' => 2],['baba' => 11]));

dbgr('DELETE', TestHub::deleteRecord(['baba' => [20, 21]]));


/****** More complicated tables ********/ 

dbgn('MORE COMPLEX HUBS');

class OtherTestHub extends \SiTEL\DataSources\Sql\MssqlTableHub {
    protected string $schema_name       = 'dbo';
    protected string $table_name        = 'holly_test_2';
    protected string $connection_name   = 'test';
    protected array $unique_keys        = [['first_name', 'last_name']];
}

class MoreTestHub extends \SiTEL\DataSources\Sql\MssqlTableHub {
    protected string $schema_name       = 'dbo';
    protected string $table_name        = 'holly_test_3';
    protected string $connection_name   = 'test';
    protected array $unique_keys        = [['holly_test_2_id', 'tool']];
}


$insert_these_records = [
    [
        'first_name' => 'Isaac',
        'last_name' => 'Aisakku',
        'email' => 'forgemaster1@email.com',
        'dob' => '1111-11-11'
    ],
    [    
        'first_name' => 'Hector',
        'last_name' => 'Hekuta',
        'email' => 'forgemaster2@email.com',
        'dob' => '1111-11-11'
    ]
];

dbgr('CREATE', OtherTestHub::createMultipleRecords($insert_these_records));

$insert_update_these_records = [
    [
        'first_name' => 'Isaac',
        'last_name' => 'Aisakku',
        'email' => 'forgemaster1@email.com',
        'dob' => '1470-10-10'
    ],
    [
        'first_name' => 'Hector',
        'last_name' => 'Hekuta',
        'email' => 'forgemaster2@email.com',
        'dob' => '1465-11-11'
    ]
];

dbgr('CREATE UPDATE', OtherTestHub::createUpdateMultipleRecords($insert_update_these_records));

// Delete test records
dbgr('DELETE TESTS', OtherTestHub::deleteRecord(['first_name' => ['Isaac', 'Hector']]));


$create_update =     [
    'first_name' => 'Dracula',
    'last_name' => 'Tepes',
    'email' => 'fangs@email.com',
    'dob' => '1000-10-10'
];

dbgr('CREATE UPDATE', OtherTestHub::createUpdateRecord($create_update));

// Count
dbgr('COUNT', OtherTestHub::count(['first_name'=> 'Trevor']));

// Count distince
dbgr('COUNT DISTINCT', OtherTestHub::count([],'email', true));

$insert_other_records = [
    [
        'holly_test_2_id' => 1,
        'tool' => 'sword'
    ],
    [
        'holly_test_2_id' => 1,
        'tool' => 'morning_star'
    ],
    [
        'holly_test_2_id' => 2,
        'tool' => 'fire'
    ],
    [
        'holly_test_2_id' => 2,
        'tool' => 'ice'
    ]
];

dbgr('CREATE UPDATE MULTIPLE', MoreTestHub::createUpdateMultipleRecords($insert_other_records));

dbgr('SELECT JOIN', OtherTestHub::quickSelectJoin('holly_test_3', ['holly_test_2.id' => 1]));

dbgr('SELECT QUICK', MoreTestHub::quickSelect(['holly_test_2_id' => 1]));

dbgr('SELECT MULTIPLE', MoreTestHub::select(['holly_test_2_id' => 1], ['tool'],  '', \PDO::FETCH_ASSOC ));

dbgr('SELECT MULTIPLE AGAIN', MoreTestHub::select(['holly_test_2_id' => [1., 2]], ['holly_test_2_id', 'tool'],  '', \PDO::FETCH_ASSOC ));

dbgr('CREATING RECORD', MoreTestHub::createRecord(['holly_test_2_id' => 3, 'tool' => 'sword']));

dbgr('DELETE RECORD', MoreTestHub::deleteRecord(['holly_test_2_id' => 3]));

dbgr('UPDATE RECORD',  MoreTestHub::updateRecord(['tool' => 'vampire powers'],['holly_test_2_id' => 3]));

dbgr('UPDATE RECORD',  MoreTestHub::updateRecord(['tool' => 'shield'],['holly_test_2_id' => 3]));

/****** MISC *******/
$mssql = \SiTEL\DataSources\Sql\Factory::getConnectionMsSql('test');

$select_sql = "SELECT * FROM dbo.holly_test_2 WHERE last_name = 'Tepes' ";

dbgr('MANUAL SELECT SINGLE', $mssql->select($select_sql)->fetchArray());

dbgr('MANUAL SELECT ALL ARRAY', $mssql->select($select_sql)->fetchAll());

dbgr('MANUAL SELECT ALL OBJ', $mssql->select($select_sql)->fetchAllObj());

dbgr('MANUAL SELECT ALL COLUMN', $mssql->select($select_sql)->fetchAllColumn(2));

