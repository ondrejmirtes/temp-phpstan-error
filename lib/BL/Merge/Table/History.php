<?php
/**
 * This is for history tables
 * 
 * @author matt
 *
 */
abstract class BL_Merge_Table_History extends BL_Merge_Table {
	
	/**
	 * performs the merge action for this table
	 * 
	 */
	protected function processMerge() {

	}
	
	/**
	 * Restores the merged items from history
	 * this will add back the records that were deleted by overriding duplicate keys
	 * It is assumed that the duplicate keys will be the combined records from the merge
	 */
	public function restore() {

	}
	
}