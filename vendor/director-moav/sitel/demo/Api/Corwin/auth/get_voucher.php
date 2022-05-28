<?php

require_once '../bootstrap.php';
    
$Client = new \SiTEL\API\CorwinClient($Logger);

echo "\n\n============================================== MAKING A CALL TO GET USER 2 VAUCHER         -> should Succeed ================================================\n";
$result1 = $Client->auth('BelongsToMedstar', ['username'=>'itay.moav@email.sitel.org','password'=>'password1']);
var_dump($result1);


echo "\n\n============================================== MAKING A CALL FOR USER 2 FEED WITHOUT THE VOUCHER -> should faile   ================================================\n";
$result2 = $Client->get('PercipioAttributes');
var_dump($result2);


echo "\n\n============================================== MAKING A CALL TO GET USER 2 FEED w\ VOUCHER -> should Succeed ================================================\n";
$result3 = $Client->setVoucher($result1->params->resource_voucher)
                  ->get('PercipioAttributes')
;
var_dump($result3);
                  