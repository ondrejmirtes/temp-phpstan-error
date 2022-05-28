<?php namespace SiTEL\DataUtils\Validator;

class Date extends a{
    /**
     * @var string
     */
    protected string $message = 'This is not a valid date format';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool{
        $date= \DateTime::createFromFormat('Y-m-d', $value);
        return ($date!== false && date_format($date, 'Y-m-d')===$value);
    }
}
