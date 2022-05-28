<?php

require_once '../bootstrap.php';
    
$Client = new \SiTEL\API\CorwinClient($Logger);
$result = $Client->auth('BelongsToMedstar', ['username'=>'preston.beverly@email.sitel.org','password'=>'password1']);
var_dump($result);
