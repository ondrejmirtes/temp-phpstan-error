<?php
/**
 * This is for tables where the data is combined
 * 
 * @author matt
 *
 */
abstract class BL_Merge_Table_Combine extends BL_Merge_Table {
	
    protected $merge_database;
	/**
	 * performs the merge action for this table
	 * 
	 */
	protected function processMerge() {
	    $hub = new $this->prod_hub_class();
	    $sql = "UPDATE IGNORE {$this->table}
				SET
					{$this->idField}= {$this->mergeIntoId}
				WHERE
					{$this->idField} = $this->mergeId";
		$hub->update($sql);
		$hub->deleteData([$this->idField => $this->mergeId]);

		
		//$this->DL->updateData(' IGNORE '.$this->table, array($this->idField => $this->mergeIntoId), $this->getWhereCondition($this->mergeId));
		//$this->DL->deleteData(str_replace('.', '`.`', $this->table), $this->getWhereCondition($this->mergeId));
		
		
	}
	
	/**
	 * Restores the merged items from history
	 * this will add back the records that were deleted by overriding duplicate keys
	 * It is assumed that the duplicate keys will be the combined records from the merge
	 */
	public function restore() {
		//Deal with different DBs here
		
//		$data = $this->DL->selectFields("{$this->historyDb}.{$this->table}", array('*'), 
//			array_merge(array('merge_id' => $this->mergeInstanceId), $this->getWhereCondition($this->mergeId)))->fetchAll();
		$data = (new $this->merge_hub_class())->select(array_merge(['merge_id' => $this->mergeInstanceId], $this->getWhereCondition($this->mergeId)),['*'],'',PDO::FETCH_ASSOC);
		
		
		if (!empty($data)) {
			foreach ($data as &$d) {
				unset($d['merge_id']);
			}
			(new $this->prod_hub_class())->insertMultipleData($data, true);
			//$this->DL->insertMultipleData($data, $this->table, true);
			
		}
	}
	/**
	 * Deletes the records that are archived from lms2archive DB
	 * if the record belongs to primary account, it should be insert into DB before deleting from lms2archive 
	 */
	public function unarchive(){
	    
	    $param =[];
        // fetch all archived records benlog to primary
	    $sql = "SELECT A.* 
	            FROM 
	                   {$this->merge_database}.{$this->table} A
	            JOIN 
	                   lms2archive.{$this->table} B
	               ON
	                   A.id = B.id
	            WHERE
	                   A.merge_id       = {$this->mergeInstanceId}
	               AND
	                   A.{$this->idField} = {$this->mergeIntoId}    
	           ";

	    $data=  rddb()->select($sql)->fetchAll(PDO::FETCH_ASSOC);

	    if (!empty($data)){// if I archive any record belonged to primary, I have to revert it first
	        foreach ($data as &$d) {
	            unset($d['merge_id']);
	        }
	        (new $this->prod_hub_class())->insertMultipleData($data, true);
	    }
	    
	    //records with secondary rbac_user_id
	    $ids = (new $this->merge_hub_class())->select(['merge_id' => $this->mergeInstanceId],
	                                                       ['id'],'',PDO::FETCH_ASSOC);
	    if (!empty($ids)){
	        $where=Data_MySQL_Shortcuts::generateWhereData(['id' =>$ids],$param);
    	    $sql = "delete from lms2archive.{$this->table} where {$where} ";
     	    rwdb()->delete($sql,$param);
	    }
	     
	}
	
}