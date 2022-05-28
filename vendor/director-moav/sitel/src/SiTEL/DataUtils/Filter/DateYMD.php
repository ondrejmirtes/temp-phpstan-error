<?php namespace SiTEL\DataUtils\Filter;
/**
 * Filters date
 * Takes m/d/Y or m/d/y and formats it to Y-m-d
 * @author holly
 */
class DateYMD implements iString {
    
    /**
     * Formats the date or return empty string, use validator if data is important.
     * @param string $data
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\iString::filter()
     */
    public function filter($data):string {
        if (!$data){
            \error("No input for DateMDY filter [{$data}]");
            return '';
        }
        
        $filtered = strtotime(str_replace('-', '/', $data));
        $format_date =$filtered?@date('Y-m-d', $filtered):null;
        if(!$format_date){
            \error("Bad time data[{$data}]");
            return '';
        }
        return $format_date;
    }
}