<?php namespace TheKof;
abstract class Model_a{

	/**
	 * When querying a collection (as opposed to one item by id) the result returns
	 * the minimum needed fields.
	 * To get the full item info, another request has to be done (with the full info url).
	 * @var bool
	 */
	protected $is_fully_loaded = false;
	
	/**
	 * The original data object from SM
	 * @var \stdClass
	 */
	protected $item_data;
	
	/**
	 * @param \stdClass $single_item Pre loaded data. If u create a new object, this can be null.
	 */
	public function __construct(\stdClass $single_item = null){
		$this->item_data = $single_item??new \stdClass;
		$this->set_if_fully_loaded();
	}
	
	/**
	 * Will fully load the item from SM and replace the existing item_data with the 
	 * return result
	 * 
	 * @return Model_a
	 */
	public function fully_load():Model_a{
		if(!$this->is_fully_loaded){
			$this->item_data = $this->get_client()->get_one()->item_data;
			$this->set_if_fully_loaded();
		}
		return $this;
	}
	
	/**
	 * CAREFULL - THIS IS read/write access, objects are transfered by REF.
	 * 
	 * @return \stdClass
	 */
	public function get_raw_data():\stdClass{
		return $this->item_data;
	}
	
	/**
	 * When sending a model to a Client to create/update on SM
	 * The response is the updated data. I will refresh the Model
	 * with the new data.
	 * 
	 * @param \stdClass $raw_data
	 * @return Model_a
	 */
	public function change_state(\stdClass $raw_data):Model_a{
		$this->item_data       = $raw_data;
		$this->is_fully_loaded = false;
		$this->set_if_fully_loaded();
		return $this;
	}
	
	/**
	 * updates the element in survey monkey
	 * 
	 * @param \stdClass $sub_structure is the part you want to modify. The members of this element correspond to the memebers in the raw_data object
	 * @return Model_a this
	 */
	public function patch(\stdClass $sub_structure):Model_a{
	    $this->get_client()->patch($this,$sub_structure);
	    return $this;
	}

	/**
	 * get method for item id
	 * 
	 * @return integer
	 */
	public function id():int{
		return $this->item_data->id*1;
	}
	
	/**
	 * Sets the $is_fully_loaded flag according to the info found in item_data
	 */
	abstract protected function set_if_fully_loaded();
}