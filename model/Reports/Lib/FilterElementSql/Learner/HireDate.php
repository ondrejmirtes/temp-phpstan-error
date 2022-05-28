<?php namespace Reports\Lib\FilterElementSql\Learner;

/**
 *  FROM: date id TO date id
 * @author itay
 *
 */
class HireDate extends \Reports\Lib\aMayhemFilterElementArray{
    
    /**
     * @var string
     */
    static protected $filter_id = 'hired_date';
   
    /**
     *
     * @var array<string,int>
     */
    private array $sql_parts = [];
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     * 
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        if(!isset($values_to_filter_by[\Reports\UI_FILTER_DATERANGE__FROM]) || !isset($values_to_filter_by[\Reports\UI_FILTER_DATERANGE__TO])){
            throw new \Exception('Hire Date is a range/array of FROM - TO ' . print_r($values_to_filter_by,true));
        }
        $values_to_filter_by[\Reports\UI_FILTER_DATERANGE__FROM] = intval($values_to_filter_by[\Reports\UI_FILTER_DATERANGE__FROM]);
        $values_to_filter_by[\Reports\UI_FILTER_DATERANGE__TO] = intval($values_to_filter_by[\Reports\UI_FILTER_DATERANGE__TO]);
        return $values_to_filter_by;
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        //check modified string
        if(count($this->values_to_filter_by()) === 0){
            return '';
        }
        $from = \reports_mayhemdb()->get_escaped_param_placeholder(static::$filter_id . \Reports\UI_FILTER_DATERANGE__FROM);
        $to   = \reports_mayhemdb()->get_escaped_param_placeholder(static::$filter_id . \Reports\UI_FILTER_DATERANGE__TO);
        $sql  = " dim_user.hired_date BETWEEN {$from} AND {$to} ";
        return $sql;
    }
}
