<?php namespace SiTEL\DataUtils\Validator;

class IsNumeric extends a{
    /**
     * @var string
     */
    protected string $message = 'The input is not numeric';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool{
        return is_numeric($value);
    }
}
