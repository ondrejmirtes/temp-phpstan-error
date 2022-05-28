<?php namespace SiTEL\DataUtils\Validator;
class NotEmpty extends a{
    /**
     * @var string
     */
    protected string $message = 'The input is empty';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool {
        return trim($value)!=='';
    }
    }
