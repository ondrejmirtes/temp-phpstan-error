<?php
/**
 *  Creates education group tree elements for tree
 *  @author Admin
 *
 */
class DataStructure_TreeElement_EducationGroup extends DataStructure_TreeElement_Abstract{
	
	/**
	 *  Builds education group tree elements
	 *  @param stdClass	$group
	 *  @param array	$selected_groups
	 *  @param array	$undetermined_groups
	 *  @param string	$parent
	 */
	public function __construct($group,$selected_groups,$undetermined_groups,$parent = "#"){
		$group_state	= new DataStructure_TreeElement_State($this->opened,$group,$selected_groups, $undetermined_groups);
		$this->state	= $group_state;
		$this->id		= DataStructure_Tree::TREE_KEY__EDUGRP . DataStructure_Tree::DELIMITER . $group->id;
		$this->text		= $group->title;
		$this->children	= true;
		$this->parent	= $parent;
		$this->icon		= DataStructure_Tree::TREE_KEY__EDUGRP;
	}
}