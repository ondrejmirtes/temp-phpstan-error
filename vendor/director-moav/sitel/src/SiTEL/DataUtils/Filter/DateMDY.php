<?php namespace SiTEL\DataUtils\Filter;
/**
 * Filters date
 * Takes Y-m-d and formats it to m/d/Y 
 * @author holly
 */
class DateMDY implements iString {

    /**
     * Either formats the date or returns empty string on a bad date value.
	 * (Really should come after a validator if it is important)
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
        $format_date = $filtered?@date('m/d/Y', $filtered):null;
        if(!$format_date){
            \error("Bad time data[{$data}]");
            return '';
        }
        return $format_date;
    }
}