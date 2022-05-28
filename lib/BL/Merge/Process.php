<?php
//TODO TOBEDELETED AND MERGED INTO USER MERGE CODE - REMOVE FROM HERE
class BL_Merge_Process {
    const
    STATUS_MERGED               = 'merged',
    STATUS_UNMERGED             = 'unmerged',
    
    PLACE_FINISHED              = 'finished',
    
    /*
     * Constants for redis status
     */
    REDIS_STATUS_INCOMPLETE     = 'incomplete',
    REDIS_STATUS_SUCCESS        = 'success',
    REDIS_STATUS_FAILURE        = 'failure'
        ;
        
        ///////////////////////// Operational Fields
        /**
         * @var $mergeId int
         */
        public   $mergeInstanceId;
        
        /**
         * @var $mergeId int
         */
        protected $mergeId;
        
        /**
         * @var $mergeIntoId int
         */
        protected $mergeIntoId;
        
        /**
         * @var $tables array
         */
        protected $tables = [];
        
        /**
         * @var $errMsg array
         */
        public $errMsg = [];
        
        protected $valid = true;
        
        /**
         *
         * @var BL_Hub_Abstract $mergeHub
         */
        protected $mergeHub;
        
        /**
         * @var $tableNames array
         */
        protected $tableNames = [];
        
        /***
         * Override validation - holly
         */
        protected $override_validation = false;
        
        /**
         * Check if override (for new UI) - holly
         */
        protected $check_override = false;
        
        
        /**
         * Redis client key
         * Redis expiration time ~ default 10 min
         * @var $redisClientKey
         * @var $redisExpirationTime
         */
        protected   $redisClientKey          = null,
                    $redisExpirationTime     = 600,
                    $async_status_display    = false
        ;
                    
        public function __construct($merge_id, $merge_into_id) {
            if ($merge_id == $merge_into_id) {
                $this->valid = false;
                $this->errMsg[] = 'Cannot merge a user to himself';
            }
            $this->setMerge_Hub();
            
            
            $this->mergeId = $merge_id;
            $this->mergeIntoId = $merge_into_id;
            rddb()->select('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED');
        }
        
        protected function setMerge_Hub(){
            $this->mergeHub = new $this->mergeHub();
            return $this;
        }
        
        /**
         * For UI only -- set merge/unmerge status to redis
         * @param boolean $flag
         */
        public function setAsyncStatusDisplay($async){
            $this->async_status_display = $async;
        }
        
        /**
         * runs the merge process
         */
        public function merge() {
            
            if ($this->merge_validate()) {
                if($this->processMerge()){
                    
                    // set redis status to success for ui - holly
                    if($this->async_status_display) {
                        $this->setRedisMergeStatus(self::REDIS_STATUS_SUCCESS);
                    }
                    
                    \User\Account\Duplicate\Functions::mark_merged($this->mergeId, $this->mergeIntoId);
                    return true;
                }
                
                $this->setRedisMergeStatus(self::REDIS_STATUS_FAILURE);
                return false;
                
            }else {
                \User\Account\Duplicate\Functions::mark_blocked_suspected($this->mergeId, $this->mergeIntoId,$this->errMsg[0] );
            }
            
            // set redis status to failure for ui - holly
            if($this->async_status_display) {
                $this->setRedisMergeStatus(self::REDIS_STATUS_FAILURE);
            }
            return false;
        }
        
        
        /**
         * validates if this proccess has been done before or not
         * and
         * validates all tables for the merge and return collective result
         * @return boolean
         */
        protected function merge_validate() {
            $valid = $this->valid;
            $all_tables = true;
            $merge_obj = $this->mergeHub->quickSelect(['merge_item_id' => $this->mergeId, 'merge_into_id' => $this->mergeIntoId],
                ['id', 'place','status']);
            
            if (isset($merge_obj->id)) {
                $this->mergeInstanceId = $merge_obj->id;
                $all_tables = false;
            }
            foreach ($this->tableNames as $key => $name) {
                if (!$all_tables) {
                    $all_tables = true;
                }
                if ($all_tables) {
                    $this->tables[$key] = new $name($this->mergeId, $this->mergeIntoId);
                }
            }
            if ($this->mergeHub->count(['merge_item_id' => $this->mergeId,
                'merge_into_id' => $this->mergeIntoId,
                'status'        => self::STATUS_MERGED,
                'place'         => self::PLACE_FINISHED
            ])){
                $valid=false;
                $this->errMsg[] = "The merge process has already been completed for ids: {$this->mergeId}  & {$this->mergeIntoId}";
                return $valid;
            }
            if ($this->mergeHub->count(['merge_item_id' => $this->mergeIntoId,
                'merge_into_id' => $this->mergeId,
                'status'        => self::STATUS_MERGED,
                'place'         => self::PLACE_FINISHED
            ])){
                $valid=false;
                $this->errMsg[] = "The merge process has already been completed for ids: {$this->mergeIntoId}  & {$this->mergeId}";
                return $valid;
            }
            if (! \User\Account\Duplicate\Functions::valid_to_merge($this->mergeId, $this->mergeIntoId) ){
                
                $this->errMsg[] ='Accounts are blocked to merge.You cannot merge them.';
                return false;
            }
            
            if (!$this->override_validation) {// holly added override validation check
                
                if(IDUHub_Lms3users_OrganizationUserEnrollment::count(['rbac_user_id' => [$this->mergeId,$this->mergeIntoId],'feed_verified'=>1],' DISTINCT rbac_user_id ') > 1){
                    $valid=false;
                    $this->errMsg[] = "Both of the users are feed verified: {$this->mergeId}  & {$this->mergeIntoId}";
                    return $valid;
                }
                
                foreach ($this->tables as $table) {
                    /**
                     * @var User_Merge_Table_Abstract_Combine $table
                     */
                    if (!$table->validate()) {
                        $valid = false;
                        $this->errMsg[] = $table->getInvalidMessage();
                        $this->check_override = true; // holly: override check for UI
                    }
                }
            }
            return $valid;
            
        }
        /**
         * Set override for validating merge process
         * @param boolean $override
         */
        public function setOverrideValidation($override) {
            $this->override_validation = $override;
        }
        
        /**
         * validates if this proccess has been done before or not
         * @return boolean
         */
        protected function unmerge_validate() {
            
            $valid = true;
            if (!$this->mergeHub->count(['id'           => $this->mergeInstanceId,
                'place'        => self::PLACE_FINISHED,
                'status'       => self::STATUS_MERGED])){
                $valid=false;
                $this->errMsg[0] = "There is no Merge instance id [{$this->mergeInstanceId}] with place='finished' and status='merged'!";
                return $valid;
            }
            return $valid;
            
        }
        
        
        
        /**
         * runs the merge process after validation
         */
        protected function processMerge() {
            /*    $all_tables = true;
             $merge_obj = $this->mergeHub->quickSelect(['merge_item_id' => $this->mergeId, 'merge_into_id' => $this->mergeIntoId],
             ['id', 'place','status']);
             
             if (isset($merge_obj->id)) {
             $this->mergeInstanceId = $merge_obj->id;
             $all_tables = false;
             }
             */
            
            
            IDUHub_Lms2prod_LockQueue::enterQueue("merge {$this->mergeId} -> {$this->mergeIntoId}");
            IDUHub_Lms2prod_LockQueue::waitForQueue();
            
            try{
                
                if (!$this->mergeInstanceId){
                    $this->mergeInstanceId = $this->mergeHub->insertData(['merge_item_id' => $this->mergeId, 'merge_into_id' => $this->mergeIntoId,'place' => $this->tableNames[0]]);
                }
                
                foreach ($this->tables as $key => $table) {
                    echo".";
                    $this->mergeHub->updateData(['place' => $this->tableNames[$key],'status'=>self::STATUS_MERGED ],['id' => $this->mergeInstanceId]);
                    $table->process($this->mergeInstanceId);
                }
                $this->mergeHub->updateData(['place' =>self::PLACE_FINISHED],['id' => $this->mergeInstanceId]);
                
                
                
                IDUHub_Lms2prod_LockQueue::releaseQueue();
                
                
                
                return true;
            }catch(Exception $e){
                warning($e);
                $this->errMsg    = $e->getMessage();
                IDUHub_Lms2prod_LockQueue::releaseQueue();
                return false;
            }
            
        }

        /**
         *runs the unmerge process after validation
         */
        protected function processUnmerge() {
            foreach ($this->tableNames as $key => $name) {
                $this->tables[$key] = new $name( $this->mergeId,  $this->mergeIntoId);
            }
            IDUHub_Lms2prod_LockQueue::enterQueue("unmerge {$this->mergeId} and {$this->mergeIntoId}");
            IDUHub_Lms2prod_LockQueue::waitForQueue();
            rwdb()->beginTransaction();
            $this->mergeHub->updateData(['place' => '','status'=>self::STATUS_UNMERGED],['id' => $this->mergeInstanceId]);
            
            try{
                // reversed to avoid foreign key issues
                foreach (array_reverse($this->tables) as $key => $table) {
                    echo".";
                    /* TODO If unmerged will crash in the middle a lot in prod and in qa, we will need to implement save place for unmerge too.
                     $this->mergeHub->updateData(['place' => $this->tableNames[$key],
                     'status'=>self::STATUS_UNMERGED],
                     ['id' => $this->mergeInstanceId]);
                     */
                    
                    $table->setRestoreId($this->mergeInstanceId);
                    $table->restore();
                }
                
                
                $this->mergeHub->updateData(['place' =>self::PLACE_FINISHED],['id' => $this->mergeInstanceId]);
                rwdb()->endTransaction();
                IDUHub_Lms2prod_LockQueue::releaseQueue();
                return true;
                
            }catch(Exception $e){
                warning($e);
                
                rwdb()->rollbackTransaction();
                IDUHub_Lms2prod_LockQueue::releaseQueue();
                return false;
            }
            
        }
        
        /**
         * runs the merge process after validation
         * @var $table BL_Merge_Table
         */
        public function restore($merge_inst_id) {
            
            $this->mergeInstanceId = $merge_inst_id;
            if ($this->unmerge_validate()) {
                if($this->processUnmerge()){
                    // set redis status to success for ui - holly
                    if ($this->async_status_display) {
                        $this->setRedisUnmergeStatus(self::REDIS_STATUS_SUCCESS);
                    }
                    //Mark them as Blocked_Merging in duplicates
                    \User\Account\Duplicate\Functions::mark_blocked_merge($this->mergeId,$this->mergeIntoId);
                    return true;
                }
                
                $this->setRedisUnmergeStatus(self::REDIS_STATUS_FAILURE);
                return false;
            }
            
            // set redis status to failure for ui - holly
            if ($this->async_status_display) {
                $this->setRedisUnmergeStatus(self::REDIS_STATUS_FAILURE);
            }
            return false;
        }
        
        /**
         * Set redis client to keep track of merge/unmerge status
         * @author holly
         */
        protected function setRedisMergeStatus($status) {
            $redisClient   = (new $this->redisClientKey($this->mergeIntoId))->status_merge_check($this->mergeId);
            
            // success
            if($status == self::REDIS_STATUS_SUCCESS) {
                $redisClient->setex($this->redisExpirationTime, ['status' => self::REDIS_STATUS_SUCCESS]);
                
                // failure
            } else if ($status == self::REDIS_STATUS_FAILURE) {
                $redisClient->setex($this->redisExpirationTime,
                    [
                        'status'            => self::REDIS_STATUS_FAILURE,
                        'error_message'     => $this->errMsg,
                        'check_override'    => $this->check_override // for user merge ui
                    ]);
            }
        }
        
        /**
         * Set redis client to keep track of merge/unmerge status
         * @author holly
         */
        protected function setRedisUnmergeStatus($status) {
            $redisClient   = (new $this->redisClientKey($this->mergeIntoId))->status_unmerge_check($this->mergeId);
            
            // success
            if($status == self::REDIS_STATUS_SUCCESS) {
                $redisClient->setex($this->redisExpirationTime, ['status'      => self::REDIS_STATUS_SUCCESS]);
                
                // failure
            } else if ($status == self::REDIS_STATUS_FAILURE) {
                $redisClient->setex($this->redisExpirationTime,
                    [
                        'status'            => self::REDIS_STATUS_FAILURE,
                        'error_message'     => $this->errMsg
                    ]);
            }
            
        }
}
