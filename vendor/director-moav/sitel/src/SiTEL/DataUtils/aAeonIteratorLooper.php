<?php namespace SiTEL\DataUtils;
/**
 * Looper for iterators.
 * 
 * @author Itay Moav
 */
abstract class aAeonIteratorLooper extends aAeonLooper{

	/**
	 * @var \Iterator<mixed>
	 */
    protected \Iterator $iterator;
	
	/**
	 * Number of headers
	 * @var int
	 */
	protected int $num_headers = 0;
	
	/**
	 * 
	 * @var array<\SiTEL\DataUtils\Filter\i>
	 */
	protected array $record_header_level_filters = [];
	
	/**
	 * @var array<array<\SiTEL\DataUtils\Filter\i>>
	 */
	protected array $field_header_level_filters = [];
	
	/**
	 * @var array<\SiTEL\DataUtils\Validator\a>
	 */
	protected array $record_header_level_validators = [];
	
	/**
	 * @var array<array<\SiTEL\DataUtils\Validator\a>>
	 */
	protected array $field_header_level_validators = [];
	
	/**
	 * @param \Iterator<mixed> $iterator
	 * @param array<mixed> $user_params
	 */
	public function __construct(\Iterator $iterator,array $user_params=[]){
		$this->iterator = $iterator;
		parent::__construct($user_params);
		$this->preInit();
		$this->load_filters();
		$this->load_validators();
		$this->load_header_filters();
		$this->load_header_validators();
		$this->postInit();
	}
	
	/**
	 * Entry point for iteration
	 * @return \SiTEL\DataUtils\aAeonIteratorLooper
	 */
	public function run():\SiTEL\DataUtils\aAeonIteratorLooper{
		$this->runHeaders();
		while($this->iterator->valid()) {
			$this->row = $this->iterator->current();
			//skip if certain rules apply
			if(!$this->skip()){
				
				if($this->validate()){ //this one calls the apply_filters to save a loop
					$this->process();
				}else{
					$this->handle_errors();
				}
				
			}
			$this->iterator->next();
		}
		
		//  Post Processing
		$this->postProcess();
		$this->closeResources();
		return $this;//for chaining and PONNIES!
	}
	/**
	 * Run process for headers
	 */
	protected function runHeaders():void{
		$this->iterator->rewind();
		for ($i = 0; $i < $this->num_headers; $i++) {
			$this->row = $this->iterator->current();
			
			if($this->validate_header()){ //this one calls the apply_filter_header to save a loop
				$this->processHeader();
			}else{
				$this->handle_errors();
			}
			$this->iterator->next();
		}
	}

	/**
	 * This has to be implemented to do something.
	 * It supposed to check current entry and based on some rules 
	 * either process it or skip it.
	 *
	 * @return bool
	 */
	protected function skip():bool{
		return false;
	}
	
	
	/**
	 * house of actual logic for headers
	 */
	protected function processHeader():void {  }
	
	/**
	 * Instantiate the filters into the record or fields (header level)
	 * Demo in comments. Do not delete!
	 *
	 * @return aAeonIteratorLooper
	 */
	protected function load_header_filters():aAeonIteratorLooper{
		/* DO NOT DELETE THIS COMMENT
		
		$this->record_header_level_filters = [new \SiTEL\DataUtils\Filter\Trim];
		$this->field_header_level_filters = [
		User_Upload_GuestParser::PARSED_PLACE__FIRST_NAME  => [new Form_Filter_Name, new Some_Other_Filter implementing the Form_Filter_i interface],
		User_Upload_GuestParser::PARSED_PLACE__MIDDLE_NAME => [new Form_Filter_Name],
		User_Upload_GuestParser::PARSED_PLACE__LAST_NAME   => [new Form_Filter_Name]
		];
		*/
		
		return $this;
	}
	
	/**
	 * Instantiate the filters into the record or fields (header level)
	 * Demo in comments. Do not delete!
	 *
	 * @return aAeonIteratorLooper
	 */
	protected function load_header_validators():aAeonIteratorLooper{
		/* DO NOT DELETE THIS COMMENT
		
		$this->record_header_level_validators = [new Form_Validator_stringLength(false,['min'=>0,'max'=>255])];
		$this->field_header_level_validators = [
		User_Upload_GuestParser::PARSED_PLACE__EMAIL       => [new Form_Validator_notEmpty,new Form_Validator_emailAddress],
		User_Upload_GuestParser::PARSED_PLACE__FIRST_NAME  => [new Form_Validator_notEmpty],
		User_Upload_GuestParser::PARSED_PLACE__LAST_NAME   => [new Form_Validator_notEmpty]
		];
		*/
		
		return $this;
	}
	
	/**
	 * Validate the header
	 * @return boolean
	 */
	final protected function validate_header():bool {
		//first make sure we even have a record
		if(!isset($this->row[0])) return false;
		
		foreach($this->row as $place => $field) {
			$this->apply_filter_header($place);
			
			// record level validation
			foreach($this->record_header_level_validators as $RecLvlValidator){
				if(!$RecLvlValidator->validate($field)){
					return false;
				}
			}
			
			// field level validation
			if(isset($this->field_header_level_validators[$place])){
				foreach($this->field_header_level_validators[$place] as $FLvlValidator){
					if(!$FLvlValidator->validate($field)){
						return false;
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Apply filters to header
	 * @param int|string $current_index
	 * @return aAeonIteratorLooper
	 */
	final protected function apply_filter_header($current_index):aAeonIteratorLooper{
		// record level filter
		if($this->record_header_level_filters){
			foreach($this->record_header_level_filters as $RecLvlFilter){
				$this->row[$current_index] = $RecLvlFilter->filter($this->row[$current_index]);
			}
		}
		
		// field level filter
		if(isset($this->field_header_level_filters[$current_index])){
			foreach($this->field_header_level_filters[$current_index] as $FLvlFilter){
				$this->row[$current_index]=$FLvlFilter->filter($this->row[$current_index]);
			}
		}
		return $this;
	}
	
	/**
	 * Close any resources
	 * @return aAeonIteratorLooper
	 */
	protected function closeResources():aAeonIteratorLooper{
		return $this;
	}
}