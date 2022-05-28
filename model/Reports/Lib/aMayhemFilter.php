<?php namespace Reports\Lib;

abstract class aMayhemFilter
{ 
    
    /**
     * 
     * @var array<\Reports\Lib\aMayhemFilterElement>
     */
    protected array $filter_list=[];
    
    /**
     * Holds the data for the response to the UI
     * 
     * @var array<int,array<string,mixed>>
     */
    protected array $filter_list_response=[];
    
    /**
     * ['param_key' => value]
     * 
     * @var array<string,mixed>
     */
    protected array $sql_escaped_params = [];
    /**
     * 
     * @return array<string,string>
     */
    abstract protected function allowed_filters():array;
    
    /**
     * 
     *
     * @param array<string,string|array<string>> $input_params
     */
    public function __construct(array $input_params)
    {
        $this->createFilterList($input_params);
    }
    
    
    /**
     * Go over the list of params from the request. Any param mapped to a valid filter
     * is instantiated with the value sent from the user.
     *
     * @param array<string,string|array<string>> $input_params
     */
    protected function createFilterList(array $input_params):void{
        $allowed_filters = $this->allowed_filters();
        foreach($input_params as $filter_id => $filter_value){
            if(isset($allowed_filters[$filter_id])){
                //@phpstan-ignore-next-line
                $this->filter_list[$filter_id]= new $allowed_filters[$filter_id]($filter_value);
                $this->filter_list_response[] = [
                    'id'            => $filter_id,
                    'selectedValue' => $filter_value
                ]; 
            }
        }
        dbgr('FILTERS PREPARED',$this->filter_list);
    }
    
    /**
     * @param string $filter_id
     * @return ?aMayhemFilterElement
     */
    public function get_filter_element(string $filter_id):?aMayhemFilterElement{
        return $this->filter_list[$filter_id]??null;
    }
    
    /**
     * @return string
     */
    public function get_where():string{
        $where = "WHERE\n";
        $and   = '';
        $sql   = '';
        foreach($this->filter_list as $filter_element){
            /* @var $filter_element \Reports\Lib\aMayhemFilterElement */
            $filter_element->add_escaped_values($this->sql_escaped_params);
            $filter_sql = $filter_element->sql_where();
            if(!$filter_sql){
                continue;
            }
            $sql.= "{$where}{$and}{$filter_sql}";
            $where='';
            $and=' AND ';
        }
        dbgr('FILTER SQL',$sql);
        return $sql;
    }
    
    /**
     * @return array<string,mixed>
     */
    public function get_sql_escaped_params():array{
        return $this->sql_escaped_params;
    }
    
    /**
     * @return array<int,array<string,mixed>>
     */
    public function getFilterResponse():array{
        return $this->filter_list_response;
    }
}