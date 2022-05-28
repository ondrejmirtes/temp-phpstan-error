<?php
require_once '../bootstrap.php';

$Client = new \SiTEL\API\CorwinClient($Logger);
$result = $Client->get('Ping');
var_dump($result);

