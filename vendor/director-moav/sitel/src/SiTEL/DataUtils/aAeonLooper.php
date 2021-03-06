<?php namespace SiTEL\DataUtils;

/**
 * Abstract class to contain dataset validators, filters and hooks
 * to be used by loopers of various types.
 * 
 * @author itay revised by holly
 * @date new versioned Jul 17 2017
 */
abstract class aAeonLooper{
	const 		ROW_TYPE__STDCLAS	= 'stdclass',
				ROW_TYPE__ASSOC		= 'assoc',
				ROW_TYPE__ARRAY		= 'array'
	;
	
	/**
	 * 
	 * @var array<mixed> $user_params
	 */
    protected   $user_params					= []; //usually the filter params are transfered that way
   /**
    * 
    * @var array<mixed> $original_user_params
    */
    protected			$original_user_params 			= []; //a copy of the initial value of the user_params before modifications
    /**
     * 
     * @var array<string|int, string> $row
     */
    protected			$row;
    /**
     * 
     * @var string $row_type see consts above
     */
    protected			$row_type						= self::ROW_TYPE__ARRAY;
                
    /**
     * Filters/validators for records (exclude headers)
     */
    /**
     * 
     * @var array<\SiTEL\DataUtils\Filter\i> $record_level_filters
     */
    protected             $record_level_filters           = [];
    /**
     * 
     * @var array<string, array<\SiTEL\DataUtils\Filter\i>> $field_level_filters
     */
    protected            $field_level_filters            = [];
    /**
     * 
     * @var array<\SiTEL\DataUtils\Validator\a> $record_level_validators
     */
    protected            $record_level_validators        = [];
    
    /**
     * 
     * @var array<string, array<\SiTEL\DataUtils\Validator\a>> $field_level_validators
     */
    protected            $field_level_validators         = [];
                
    /**
     * 
     * @var string $last_error_message
     */
    protected            $last_error_message             = '';

    /**
     * This three methods are being set in the base constructor.
     * Use those for setting/reading/deleting row fields
     * Add more methods if necessary
     * 
     * @var callable $_setRowField
     */
    protected $_setRowField		= null;
    /**
     * 
     * @var callable $_getRowField
     */
    protected		  $_getRowField		= null;
    /**
     * 
     * @var callable $_butcher
     */
    protected		  $_butcher			= null;
    
	/**
	 * @param array<mixed> $user_params
	 */
	protected function __construct(array $user_params=[]){
		$this->user_params = $this->original_user_params = $user_params;
		$this->setGetterSetters();
	}
	
	/**
	 * Sets how to access the current $row
	 * 
	 * @throws \LogicException
	 */
	protected function setGetterSetters():void{
		switch($this->row_type){
			case self::ROW_TYPE__ARRAY:
			case self::ROW_TYPE__ASSOC:
				$this->_setRowField = function($index,$value){
					$this->row[$index] = $value;
					return $this->row[$index];
				};
				
				$this->_getRowField = function($index){
					return $this->row[$index];
				};
				
				$this->_butcher    = function($index){
					unset($this->row[$index]);
				};
				break;
				
			case self::ROW_TYPE__STDCLAS:
				$this->_setRowField = function($index,$value){
					$this->row->$index = $value;
					return $this->row->$index;
				};
				
				$this->_getRowField = function($index){
					return $this->row->$index;
				};
				
				$this->_butcher    = function($index){
					unset($this->row->$index);
				};
				break;
				
			default:
				throw new \LogicException('You must set a row type in a looper');
		}
	}
    
	/**
	 * @param string $index
	 * @param mixed $value
	 * @return mixed
	 */
	protected function setRowField(string $index,$value){
		return ($this->_setRowField)($index,$value);
	}
	
	/**
	 * @param string $index
	 * @return mixed
	 */
	protected function getRowField($index){
		return ($this->_getRowField)($index);
	}
	
	/**
	 * @param string $index
	 */
	protected function butcher($index):void{
		($this->_butcher)($index);
	}
		
    /**
     * Pre-init
     */
	protected function preInit():void{
    }
    
    /**
     * postInit
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function postInit():\SiTEL\DataUtils\aAeonLooper{
        return $this;
    }
 
    /**
     * Default error handler
     */
    protected function handle_errors():void{
        \ZimLogger\MainZim::$CurrentLogger->error('Record did not pass validation',true);
        \ZimLogger\MainZim::$CurrentLogger->error($this->row,false);
    }

    /**
     * Logic for post process
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function postProcess():\SiTEL\DataUtils\aAeonLooper{ 
        return $this;
    }
    
    /**
     * Apply filter to each record
     * @param mixed $current_index
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    final protected function apply_filters($current_index):\SiTEL\DataUtils\aAeonLooper{
        if($this->record_level_filters){
            foreach($this->record_level_filters as $RecLvlFilter){
                $this->row[$current_index] = $RecLvlFilter->filter($this->row[$current_index]);
            }
        }
    
        if(isset($this->field_level_filters[$current_index])){
            foreach($this->field_level_filters[$current_index] as $FLvlFilter){
                $this->row[$current_index]=$FLvlFilter->filter($this->row[$current_index]);
            }
        }
        return $this;
    }
    
    /**
     * validate each record, applying filters first
     * @return boolean
     */
    final protected function validate():bool{
        //first make sure we even have a record
        if(empty($this->row)) return false;
        
        //use user validator
        foreach($this->row as $place => &$field){
            $this->apply_filters($place);
    
            //record level validation
            foreach($this->record_level_validators as $RecLvlValidator){
                if(!$RecLvlValidator->validate($field)){
                    $this->last_error_message= $RecLvlValidator->message();
                    
                    return false;
                }
            }
    
            //field level validation
            if(isset($this->field_level_validators[$place])){
                foreach($this->field_level_validators[$place] as $FLvlValidator){
                    if(!$FLvlValidator->validate($field)){
                        $this->last_error_message= $FLvlValidator->message()." -> Value: {$field}";
                        return false;
                    }
                }
            }
        }
    
        return true;
    }
    
    /**
     * instantiate the filters into the record(row level)
     * and field level arrays.
     * Specific for each concrete class
     * 
     *  IN comments is a demo on how this can be used. Do not delete the comment
     *
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function load_filters():\SiTEL\DataUtils\aAeonLooper{
        /* DO NOT DELETE COMMENT
        $this->record_level_filters = [new \SiTEL\DataUtils\Filter\Trim];
        $this->field_level_filters  = [User_Upload_GuestParser::PARSED_PLACE__FIRST_NAME  => [new Form_Filter_Name, new Some_Other_Filter implementing the Form_Filter_i interface],
                                       User_Upload_GuestParser::PARSED_PLACE__MIDDLE_NAME => [new Form_Filter_Name],
                                       User_Upload_GuestParser::PARSED_PLACE__LAST_NAME   => [new Form_Filter_Name]
        ];
        */
        return $this;
    }
    
    /**
     * instantiate the validators into the record(row level)
     * and field level arrays 
     * Specific for each concrete class
     * 
     * In comment a demo how to use it.
     * 
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function load_validators():\SiTEL\DataUtils\aAeonLooper{
        /* DO NOT DELETE THIS COMMENT
          
        $this->record_level_validators = [new Form_Validator_stringLength(false,['min'=>0,'max'=>255])];
        $this->field_level_validators  = [User_Upload_GuestParser::PARSED_PLACE__EMAIL       => [new Form_Validator_notEmpty,new Form_Validator_emailAddress],
            User_Upload_GuestParser::PARSED_PLACE__FIRST_NAME  => [new Form_Validator_notEmpty],
            User_Upload_GuestParser::PARSED_PLACE__LAST_NAME   => [new Form_Validator_notEmpty]
        ];
        */
        return $this;
    }

    /**
     * This method is called for each record in the fetched dataset
     * house of actual logic
     * I make a concrete one as some loopers mark a record to be processed
     * just to apply the filters and validators. 
     */
    protected function process():void{
    	
    }

    /**
     * @param string $param_key
     * @param mixed $default
     * @return mixed
     */
    protected function getParam(string $param_key, $default = null){
    	if(isset($this->user_params[$param_key])){
    		return $this->user_params[$param_key];
    	}
    	return $default;
    }

    /**
     * @param string $param_key
     * @param mixed $param_value
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function setParam(string $param_key,$param_value):\SiTEL\DataUtils\aAeonLooper{
    	$this->user_params[$param_key] = $param_value;
    	return $this;
    }

    /**
     * @param string $param_key
     * @return \SiTEL\DataUtils\aAeonLooper
     */
    protected function unsetParam(string $param_key):\SiTEL\DataUtils\aAeonLooper{
    	unset($this->user_params[$param_key]);
    	return $this;
    }
}
