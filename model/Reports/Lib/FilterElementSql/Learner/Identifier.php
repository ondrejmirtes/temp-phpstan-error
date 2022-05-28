<?php namespace Reports\Lib\FilterElementSql\Learner;

/**
 *  
 * @author itay
 *
 */
class Identifier extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected $filter_id = 'learner_identifier';
    
    /**
     * 
     * @var array<int,string>
     */
    private array $sql_parts = [];
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     * 
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        return trim(str_replace('  ',' ',$values_to_filter_by));
    }
    
    /**
     * {@inheritDoc}
     * @see \Reports\Lib\aMayhemFilterElement::add_escaped_values()
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
        dbgr('ESCAPING',$this->values_to_filter_by());
        //check initial string
        if(strlen($this->values_to_filter_by())<\Reports\SEARCH_MIN_CHAR_CNT_TO_SEARCH){
            return;
        }
        $parts = explode(' ',$this->values_to_filter_by());
        foreach($parts as $idx=>$part){
            $part = trim($part);//might be a serise of empty spaces
            if(!$part){
                continue;
            }
            $place_holder_name = self::$filter_id . $idx;
            $filter_sql_place_holder = \reports_mayhemdb()->get_escaped_param_placeholder($place_holder_name);
            $this->sql_parts[]= "
                (       dim_user.lms_username LIKE {$filter_sql_place_holder}
                    OR  dim_user.last_name LIKE {$filter_sql_place_holder}
                    OR  dim_user.first_name LIKE {$filter_sql_place_holder}
                    OR  dim_user.medstar_employee_id LIKE {$filter_sql_place_holder}
                    OR  dim_user.all_identifiers LIKE {$filter_sql_place_holder}
                )";
            $sql_escaped_params[$place_holder_name] = "%{$part}%";
        }
        dbgr('sql_escaped_params',$sql_escaped_params);
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        //check modified string
        if(count($this->sql_parts) == 0){
            return '';
        }
        
        $sql = '(' . join(' AND ', $this->sql_parts) .') ';
        return $sql;
    }
}
