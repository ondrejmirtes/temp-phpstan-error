<?php
require_once '../bootstrap.php';

echo "\n\n==================================ENROLL TO CONTENT WITH NO AUTH - SHOULD FAIL ==================================\n";

$Client = new \SiTEL\API\CorwinClient($Logger);

$result = $Client->call('Curriculum_EnrollToContent', [
    'content_id' => 10266
]);
var_dump($result);

echo "\n\n================================== AUTH  ==================================\n";

$Client->setCallType('auth');

$result = $Client->call('NoOrg',[
    'username' => 'itay.moav@email.sitel.org',
    'password' => 'password1'
]);
var_dump($result);

echo "\n\n================================== ENROLL WITH AUTH - UNEXISTING CONTENT - SHOULD FAIL==================================\n";

$Client->setCallType('post');
$Client->setAuthCode($result->authKey);
$result = $Client->call('Curriculum_EnrollToContent',[
    'content_id' => 100000
]);
var_dump($result);

echo "\n\n================================== ENROLL WITH AUTH - USER HAS EXISTING UNCOMPLETED ENROLLMENT  - SHOULD RETURN EXISTING ENROLLMENT ID, AND EXISTING_UNCOMPLETED_ENROLLMENT_FLAG==================================\n";

$result = $Client->call('Curriculum_EnrollToContent',[
    'content_id' => 10266
]);
var_dump($result);

echo "\n\n================================== ENROLL WITH AUTH - USER DONT HAVE PREVIOUS ENROLLMENT    - SHOULD RETURN NEW ENROLLMENT ID AND NEW_ENROLLMENT_FLAG==================================\n";

$result = $Client->call('Curriculum_EnrollToContent',[
    'content_id' => 12149
]);
var_dump($result);
