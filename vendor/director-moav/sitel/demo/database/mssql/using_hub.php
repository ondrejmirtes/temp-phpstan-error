<?php
require_once './bootstrap.php';
\SiTEL\DataSources\Sql\Factory::setConnectionMsSql('aws-itay-test-NOTACTIVE',[
        'server'    => 'diamond.cptyyf28ahy5.us-east-1.rds.amazonaws.com',
        'database'  => 'baba',
        'username'  => 'loki',
        'password'  => 'password1'
    ],
    \ZimLogger\MainZim::$CurrentLogger
);

dbgn("\n\nHUBBING\n\n");
class ShubiHub extends \SiTEL\DataSources\Sql\MssqlTableHub{
    protected string $database_name = '';
    protected string $table_name    = 'shubi';
}

$res=ShubiHub::select(['f2'=>3]);
dbgr('RESULT',$res);