<?php
/**
 *  Abstract for tree elements in the tree
 *  @author Admin
 *
 */
abstract class DataStructure_TreeElement_Abstract{
	
	/**
	 *  These must be public due to the serialization of the object
	 *  Private variables won't be found and it will use extra memory 
	 *  to use JsonSerializable.  Defeats the purpose of using as an object
	 *  if data is made into heavier associative array
	 */
	public	$state,
			$id,
			$text,
			$parent,
			$children,
			$icon
	;
	
	/**
	 *  Is the tree element open by default?
	 *  Most are not.
	 *  @var boolean
	 */
	protected	$opened	= false;
	
}