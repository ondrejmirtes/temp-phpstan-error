<?php
require_once './bootstrap.php';
$client = new \SiTEL\DataSources\Sql\MssqlClient(
                            'test',[
                                    'server'    => 'emeraldsandbox.database.windows.net',
                                    'database'  => 'emerald_amwell',
                                    'username'  => 'emerald',
                                    'password'  => 'diamond.cptyyf28ahy5.us-east-1.rds.amazonaws.com'
                            ],
                            \ZimLogger\MainZim::$CurrentLogger
);

dbgn("\n\nSELECTING with params\n\n");
//$res = $client->select('select * from sys.databases WHERE name=:name',['name' => 'baba'])->fetchAll();
$res = $client->select('select * from sys.tables')->fetchAll();
dbgr('RESULT',$res);
dbgn('==========================================================');
//$res = $client->select('select * from itay_test1 WHERE id>:f',['f'=>0])->fetchAll();
$res = $client->select('exec sp_columns itay_test1')->fetchAll();
dbgr('RESULT',$res);
