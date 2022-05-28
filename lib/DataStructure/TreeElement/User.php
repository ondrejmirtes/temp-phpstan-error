<?php
/**
 *  Creates user tree elements for the tree
 *  @author Admin
 *
 */
class DataStructure_TreeElement_User extends DataStructure_TreeElement_Abstract{
	
	/**
	 *  The one situation in which the element defaults to open
	 *  Should have no child elements
	 *  @var boolean
	 */
	protected	$opened = false;
	
	/**
	 *  Builds the user tree element
	 *  @param stdClass	$user
	 *  @param array	$selected_users
	 *  @param String	$parent
	 */
	public function __construct($user,$selected_users,$parent){
		$user_state		= new DataStructure_TreeElement_State($this->opened,$user,$selected_users);
		$this->state	= $user_state;
		$this->id		= DataStructure_Tree::TREE_KEY__USER . DataStructure_Tree::DELIMITER . $user->id;
		$this->text		= $user->name;
		$this->parent	= $parent;
		$this->children	= false;
		$this->icon		= DataStructure_Tree::TREE_KEY__USER;
	}
	
}