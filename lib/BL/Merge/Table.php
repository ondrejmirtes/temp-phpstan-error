<?php

/**
 * Abstract for each table involved in a merge process
 * 
 * @author matt
 *
 */
abstract class BL_Merge_Table {
	
	///////////////////////// Operational Fields
	/**
	 * @var $mergeId int
	 */
	protected $mergeInstanceId;
	
	/**
	 * @var $mergeId int
	 */
	protected $mergeId;
	
	/**
	 * @var $mergeIntoId int
	 */
	protected $mergeIntoId;
	
	/**
	 * @var BL_Hub_Abstract $prod_hub_class
	 */
	protected $prod_hub_class;
	
	/**
	 * @var BL_Hub_Abstract $merge_hub_class
	 */
	protected $merge_hub_class;
	
	
	/**
	 * 
	 * @var BL_Hub_Abstract $merge_hub_class
	 * merge table's hub in merge database
	 */
	protected $mergeHub;
	
	///////////////////////// Data Fields
	/**
	 * the db to store history for the process
	 * 
	 * @var $historyDb string
	 */
	//protected $historyDb;
	
	/**
	 * the table name in the db
	 * 
	 * @var $table string
	 */
	protected $table;
	
	/**
	 * The id field being changed during merge
	 * 
	 * @var $idField string
	 */
	protected $idField;
	
	/**
	 * @var $invalidMessage string
	 */
	protected $invalidMessage = '';
	
	
	
	public function __construct($merge_id, $merge_into_id) {
		$this->mergeId = $merge_id;
		$this->mergeIntoId = $merge_into_id;
		
		//$this->historyDb = $history_db;
	}
	
	/**
	 * Validates that the two items can be merged
	 * 
	 * @return boolean
	 */
	public function validate() {
		return true;
	}
	
	/**
	 * gets the message for a validation failure
	 * 
	 * @return string
	 */
	public function getInvalidMessage() {
		return $this->invalidMessage;
	}
	
	/**
	 * performs the merge action for this table
	 * including keeping a history.
	 * 
	 */
	public function process($merge_inst_id) {
		$this->mergeInstanceId = $merge_inst_id;
		$this->recordHistory();
		$this->processMerge();
	}
	
	/**
	 * keeps a history of premerge data
	 */
	protected function recordHistory() {
		//$this->DL->createHistoryRecorForMerge($this->table, $this->getWhereCondition($this->mergeId), $this->historyDb, $this->mergeInstanceId);
	    $hub = new $this->merge_hub_class();
		$hub->createHistoryRecordForMerge($this->table,$this->getWhereCondition($this->mergeId), $this->mergeInstanceId);
		//$this->DL->createHistoryRecordForMerge($this->table, $this->getWhereCondition($this->mergeIntoId), $this->historyDb, $this->mergeInstanceId);
		$hub->createHistoryRecordForMerge($this->table,$this->getWhereCondition($this->mergeIntoId), $this->mergeInstanceId);
	}
	
	/**
	 * can be overriden
	 */
	protected function getWhereCondition($id) {
		return array($this->idField => $id);
	}
	
	/**
	 * performs the merge action for this table
	 */
	protected abstract function processMerge();
	
	public function setRestoreId($merge_id) {
		//Deal with different DBs here
		$this->mergeInstanceId = $merge_id;
		
		//$merge = $this->DL->selectFields("{$this->historyDb}.merge", array('merge_item_id', 'merge_into_id'), array('id' => $merge_id))->fetchObj();
		$merge = (new $this->mergeHub())->quickSelect(['id' => $merge_id],
		                                         ['merge_item_id', 'merge_into_id']);
		
		$this->mergeId = $merge->merge_item_id;
		$this->mergeIntoId = $merge->merge_into_id;
	}
	
	/**
	 * Restores the merged items from history
	 */
	public abstract function restore();
	
}