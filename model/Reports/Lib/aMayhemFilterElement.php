<?php namespace Reports\Lib;

abstract class aMayhemFilterElement{

    /** 
     * 
     * @var string
     */
    static protected string $sql_field_name;
    
    /**
     * @var string
     */
    static protected $filter_id;
    
    /**
     * @return string
     */
    static function sql_fieldname():string{
        return static::$sql_field_name;
    }
    /**
     * This is the value we use to map between UI and back end.
     * @return string
     */
    static function id():string{
        return static::$filter_id;    
    }
    /**
     * @var mixed
     */
    protected $values_to_filter_by;
    
    /**
     * 
     * @param mixed $values_to_filter_by
     */
    public function __construct($values_to_filter_by){
        $this->values_to_filter_by = $this->clean_filter_value($values_to_filter_by);
    }
    
    /**
     * @param mixed $values_to_filter_by
     * @return mixed
     */
    protected function clean_filter_value($values_to_filter_by){
        return $values_to_filter_by;
    }
    
    /**
     * Getter
     * @return mixed
     */
    public function values_to_filter_by(){
        return $this->values_to_filter_by;    
    }
    
    /**
     * @param array<string,string> $sql_escaped_params
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
        
        if($this->values_to_filter_by !== \Reports\FILTER_ELEMENT_EMPTY_VALUE){
            $sql_escaped_params[static::$filter_id] = $this->values_to_filter_by;
        }
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        if($this->values_to_filter_by !== \Reports\FILTER_ELEMENT_EMPTY_VALUE){
            $filter_place_holder = \reports_mayhemdb()->get_escaped_param_placeholder(static::$filter_id);
            $sql_field_name=static::$sql_field_name;
            return " {$sql_field_name} = {$filter_place_holder}";
        }
        return '';
    }
}
