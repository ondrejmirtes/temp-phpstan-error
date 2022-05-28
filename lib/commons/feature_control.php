<?php namespace commons\featurecontrol;

function live_rollout(int $rbac_user_id): bool {
    \error('TOBEDELETED202206');
    return true;
}

function percipio(int $rbac_user_id): bool {
    $max_range   = 100;
    return  ( $rbac_user_id < $max_range);
}

//https://flyspray.samba42.club/flyspray/index.php?do=details&task_id=2511
// TODO TOBEDELETED202214 verify it's not used | FS#3083 - Remove Feature Flag | CloudCME SSO link on the LMS
function cloudcme(int $rbac_user_id): bool {
    \error('TOBEDELETED202214');
    if($rbac_user_id < 1000){
        return true;
    }
    $is_user_there = \IDUHub_Lms2groups_User::quickSelect(['student_rbac_user_id' => $rbac_user_id,'group_id' => 13620],['id']);
    if($is_user_there && isset($is_user_there->id)){
        return true;
    }
    return false;
}

//https://flyspray.samba42.club/flyspray/index.php?do=details&task_id=2512
function quizbuilder(int $rbac_user_id): bool {
    if($rbac_user_id < 10000){
        return true;
    }
    $is_user_there = \IDUHub_Lms2groups_User::quickSelect(['student_rbac_user_id' => $rbac_user_id,'group_id' => 13625],['id']);
    if($is_user_there && isset($is_user_there->id)){
        return true;
    }
    return false;
}

/**
 * owned by Itay, Derek and Holly
 * @param int $rbac_user_id
 * @return bool
 */
function mayhem_reports(int $rbac_user_id): bool {
    if(app_env()['env_type'] === 'development' || app_env()['env_type'] === 'STAGING'){
        return true;
    }
    if(in_array($rbac_user_id, [2,5,6,14,17,22,26,30,35,38,57,66,67,196538])){
        return true;
    }
    return false;
}
