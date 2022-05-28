<?php namespace Reports\Lib\FilterElementSql\Learner;

/** 
 * What createds the user, which feed/self etc
 * 
 * @author itay
 *
 */
class LmsSource extends \Reports\Lib\aMayhemFilterElement{
    
    /**
     * @var string
     */
    static protected string $sql_field_name = '???';
    /**
     * @var string
     */
    static protected $filter_id = 'learner_source';
    
    /**
     * If the values need modifications, like adding % according to size, this is where u do it
     * 
     * @param mixed $values_to_filter_by
     */
    protected function clean_filter_value($values_to_filter_by){
        //return "%{$values_to_filter_by}%";
    }
    
    /**
     * @return string
     */
    public function sql_where():string{
        /*TODO
        $sql_field_name = static::$sql_field_name;
        $filter_id      = static::$filter_id;
        return " {$sql_field_name} LIKE :{$filter_id}";
        */
        \error('NOT IMPLEMENTED YET');
        return '';
    }
}
