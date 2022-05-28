<?php namespace TheKof;
/**
 * Help implementing the sort by for the query
 * 
 * @author itay
 */
class Client_QueryParts_SortBy implements Client_QueryParts_i{
    const QUERY_SORT_ORDER__ASC     = 'ASC',
          QUERY_SORT_ORDER__DESC    = 'DESC'
    ;              
    
    private $sort_by    = '',
            $sort_order = ''
    ;
    
    /**
     * @param string $order_by
     * @param string $sort_order
     */
    public function __construct(string $sort_by,string $sort_order){
        $this->sort_by    = $sort_by;
        $this->sort_order = $sort_order;
    }
    
    public function __toString(){
        return "sort_by={$this->sort_by}&sort_order={$this->sort_order}";
    }
}

