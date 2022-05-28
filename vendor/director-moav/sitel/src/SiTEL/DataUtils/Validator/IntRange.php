<?php namespace SiTEL\DataUtils\Validator;

class IntRange extends a{
    /**
     * 
     * @var integer
     */
    const 
            MAX_INT_SIGNED = 2147483647;
    /**
     * @var string
     */
    protected string $message = 'The input is not in the proper range';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool{
        $min = $this->params['min']??0;
        $max = $this->params['max']??self::MAX_INT_SIGNED;
        return ((is_numeric($value) && $value>=$min && $value<=$max));
    }
}
