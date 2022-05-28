<?php namespace Reports\Lib;

/**
 * 
 * @author itay
 *
 */
abstract class aMayhemFilterElementArray extends aMayhemFilterElement{

    /**
     * {@inheritDoc}
     * @see \Reports\Lib\aMayhemFilterElement::add_escaped_values()
     */
    public function add_escaped_values(array &$sql_escaped_params):void{
        foreach($this->values_to_filter_by as $k=>$v){
            $sql_escaped_params[static::$filter_id . $k] = $v;
        }
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        if($this->values_to_filter_by === \Reports\FILTER_ELEMENT_EMPTY_VALUE || $this->values_to_filter_by === []){
            return '';
        }
        
        $values = [];
        $filter_id=static::$filter_id;
        foreach($this->values_to_filter_by as $k=>$v){
            dbgr("Doing sql_where {$k}",$v);
            $values[] = \reports_mayhemdb()->get_escaped_param_placeholder("{$filter_id}{$k}");
        }
        $sql_field_name = static::$sql_field_name;
        return "{$sql_field_name} IN (" . join(',',$values) . ')';
    }
}
