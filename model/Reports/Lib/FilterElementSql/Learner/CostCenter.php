<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * All cost centers in the population fetched - will use hr snapshot, will see how it goes
 * NOTICE cost center is actually two fields business unit and business unit code
 * Expected input: 
 * [xxxxxx-xxxxxx
 *  xxxxxx-xxxxxx
 *  ....
 *  xxxxxx-xxxxxx
 * ]
 * 
 * @author itay
 *
 */
class CostCenter extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = 'primary_business_unit,primary_business_unit_code';//NOT USED
    /**
     * @var string
     */
    static protected $filter_id = 'cost_center';
    
    /**
     *
     * @var array<int,string>
     */
    private array $sql_parts = [];
    
    /**
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        $tokenized_values_to_filter_by = [];
        foreach($values_to_filter_by as $cost_center){
            $cost_center=trim($cost_center);
            $parts=explode('-',$cost_center);
            if(count($parts) !== 2){
                \warning('Bad cost center filter value ' . print_r($cost_center,true));
                continue;
            }
            
            $tokenized_values_to_filter_by[] = [
                'primary_business_unit' => $parts[0],
                'primary_business_unit_code' => $parts[1]
            ];
        }
        return $tokenized_values_to_filter_by;
    }
    
    /**
     * {@inheritDoc}
     * @see \Reports\Lib\aMayhemFilterElement::add_escaped_values()
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
        if(!$this->values_to_filter_by()){
            return;
        }
        
        dbgr('ESCAPING',$this->values_to_filter_by());
        foreach($this->values_to_filter_by() as $idx=>$cost_center){
            $place_holder_name_bu = self::$filter_id . "bu{$idx}";
            $place_holder_name_bu_code = self::$filter_id . "bucode{$idx}";
            $filter_sql_place_holder_bu = \reports_mayhemdb()->get_escaped_param_placeholder($place_holder_name_bu);
            $filter_sql_place_holder_bu_code = \reports_mayhemdb()->get_escaped_param_placeholder($place_holder_name_bu_code);

            $this->sql_parts[]= "
                ( 
                    dim_user_hr_snapshot.primary_business_unit = {$filter_sql_place_holder_bu} AND dim_user_hr_snapshot.primary_business_unit_code = {$filter_sql_place_holder_bu_code}
                )
            ";
            $sql_escaped_params[$place_holder_name_bu] = $cost_center['primary_business_unit'];
            $sql_escaped_params[$place_holder_name_bu_code] = $cost_center['primary_business_unit_code'];
        }
        dbgr('sql_escaped_params',$sql_escaped_params);
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        if(count($this->sql_parts) == 0){
            return '';
        }
        return '('.join(' OR ', $this->sql_parts).')';
    }
}

