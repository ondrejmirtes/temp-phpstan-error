<?php

/**
 * This is for tables where the merge into course supercedes the merge course
 * 
 * @author matt
 *
 */
abstract class  BL_Merge_Table_Delete extends BL_Merge_Table {
	
	/**
	 * performs the merge action for this table
	 * 
	 */
	protected function processMerge() {
		//$this->DL->deleteData($this->table, $this->getWhereCondition($this->mergeId));
		$hub = new $this->prod_hub_class();
		$hub->deleteData($this->getWhereCondition($this->mergeId));
	}
	
	/**
	 * Restores the merged items from history
	 * this will add back the records that were deleted without overriding duplicate keys
	 */
	public function restore() {
		//$data = $this->DL->selectFields("{$this->historyDb}.{$this->table}", array('*'), 
			//array_merge(array('merge_id' => $this->mergeInstanceId), $this->getWhereCondition($this->mergeId)))->fetchAll();
		
		$data= (new $this->merge_hub_class())->select(array_merge(['merge_id' => $this->mergeInstanceId], $this->getWhereCondition($this->mergeId)),['*'],'',PDO::FETCH_ASSOC);
		
		if (!empty($data)) {
			foreach ($data as &$d) {
				unset($d['merge_id']);
			}
			//$this->DL->insertMultipleData($data, $this->table, false);
		(new $this->prod_hub_class())->insertMultipleData($data, false);
		}
	}
	
}