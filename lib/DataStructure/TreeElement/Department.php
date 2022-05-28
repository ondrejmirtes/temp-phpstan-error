<?php
/**
 *  Creates department elements for the tree
 *  @author Admin
 *
 */
class DataStructure_TreeElement_Department extends DataStructure_TreeElement_Abstract{
	
	/**
	 *  Builds the department tree element
	 *  @param stdClass	$department
	 *  @param array	$selected_departments
	 *  @param array	$undetermined_departments
	 *  @param string	$parent
	 */
	public function __construct($department,$selected_departments,$undetermined_departments,$parent){
		$department_state	= new DataStructure_TreeElement_State($this->opened,$department,$selected_departments, $undetermined_departments);
		$this->state		= $department_state;
		$this->id			= DataStructure_Tree::TREE_KEY__DEPARTMENT . DataStructure_Tree::DELIMITER . $department->id;
		$this->text			= $department->name . '   (' . $department->code . ')';
		$this->parent		= $parent;
		$this->children		= true;
		$this->icon			= DataStructure_Tree::TREE_KEY__DEPARTMENT;
	}
}