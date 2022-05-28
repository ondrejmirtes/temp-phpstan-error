<?php namespace SiTEL\DataUtils\Validator;

class IsValidEnum extends a{
    /**
     * @var string
     */
    protected string $message = 'The input is not valid enum';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool{
           return in_array($value, $this->params['enum']);
    }
}
