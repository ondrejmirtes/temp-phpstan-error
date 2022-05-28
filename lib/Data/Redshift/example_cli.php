<?php 
require_once  '/lms2/production/anahita/bin/init/bootstrap.php';
\ZimLogger\MainZim::setGlobalLogger(
    'POSTGRETESTS_',
    \ZimLogger\Streams\Stdio::class,
    4,
    '/var/log/lms2/'
);
dbgn('STARTING');
$reports = Data_Redshift_DB::getInstance();
$reports->select('SELECT * FROM data.test1');
foreach($reports->fetch_row() as $i=>$row){
    dbgr($i,$row);
}
