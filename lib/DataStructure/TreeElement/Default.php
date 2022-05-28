<?php
/**
 *  Creates default department tree element for tree
 *  @author Admin
 *
 */
class DataStructure_TreeElement_Default extends DataStructure_TreeElement_Abstract{

	/**
	 *  Builds default department tree element
	 *  @param stdClass	$default_department
	 *  @param Array	$selected_default
	 *  @param Array	$undetermined_default
	 *  @param String	$parent
	 */
	public function __construct($default_department,$selected_default,$undetermined_default,$parent){
		$default_state	= new DataStructure_TreeElement_State($this->opened,$default_department,$selected_default, $undetermined_default);
		$this->state	= $default_state;
		$this->id		= DataStructure_Tree::TREE_KEY__DEFAULT . DataStructure_Tree::DELIMITER . $default_department->id;
		$this->text		= 'Unassigned Users';
		$this->children	= true;
		$this->parent	= $parent;
		$this->icon		= DataStructure_Tree::TREE_KEY__DEPARTMENT;
	}
}