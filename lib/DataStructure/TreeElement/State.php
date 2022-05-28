<?php
/**
 *  Creates the state portion of the tree 
 *  @author Admin
 */
class DataStructure_TreeElement_State {
	
	/**
	 *  These must be public due to the serialization of the object
	 *  Private variables won't be found and it will use extra memory 
	 *  to use JsonSerializable.  Defeats the purpose of using as an object
	 *  if data is made into heavier associative array
	 */
	public	$opened,
			$selected,
			$undetermined
	;
	
	/**
	 *  Build state portion of tree elements
	 *  @param boolean	$opened
	 *  @param stdClass	$element
	 *  @param array	$selected_elements
	 *  @param array	$undetermined_elements
	 */
	public function __construct($opened, $element, $selected_elements, $undetermined_elements = []){
		$this->opened		= $opened;
		$this->selected		= in_array($element->id, $selected_elements); 
		$this->undetermined	= in_array($element->id, $undetermined_elements);
	}
}