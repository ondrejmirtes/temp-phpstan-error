<?php  namespace SiTEL\DataUtils\Filter;
/**
 * Formats data 
 * Removes unexpected characters
 *
 * @author Itay
 */
class EmailHeader implements iString{
    /**
     * 
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Filter\iString::filter()
     */
    public function filter($data):string{
       $data = str_replace('´', '', $data);
       return $data;
    }
}