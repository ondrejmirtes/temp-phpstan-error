<?php
/**
 *  Creates organization tree elements for trees
 *  @author Admin
 *
 */
class DataStructure_TreeElement_Organization extends DataStructure_TreeElement_Abstract{
	
	/**
	 *  Builds organization tree elements
	 *  @param stdClass	$organization
	 *  @param Array	$selected_organizations
	 *  @param Array	$undetermined_organizations
	 *  @param String	$parent
	 */
	public function __construct($organization,$selected_organizations,$undetermined_organizations,$parent = "#"){
		$organization_state	= new DataStructure_TreeElement_State($this->opened,$organization,$selected_organizations, $undetermined_organizations);
		$this->state		= $organization_state;
		$this->id			= DataStructure_Tree::TREE_KEY__ORGANIZATION . DataStructure_Tree::DELIMITER . $organization->id;
		$this->text			= $organization->name;
		$this->children		= true;
		$this->parent		= $parent;
		$this->icon			= DataStructure_Tree::TREE_KEY__ORGANIZATION;
	}
}