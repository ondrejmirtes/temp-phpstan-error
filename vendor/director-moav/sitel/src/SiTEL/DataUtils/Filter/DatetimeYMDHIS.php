<?php namespace SiTEL\DataUtils\Filter;
/**
 * Formats m/d/y H:i or m/d/Y H:i to MYSQL date time format y-m-d H:i:s
 * @author holly
 */
class DatetimeYMDHIS implements iString {
    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\iString::filter()
     */
    public function filter($data):string {
        $time = strtotime($data);
        $ret = @date('Y-m-d H:i:s', $time);
        if(!$ret){
            \error("Bad time data[{$data}] time [{$time}]");
            return '';
        }
        return $ret;
    }
}