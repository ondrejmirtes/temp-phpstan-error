<?php namespace SiTEL\DataUtils\Validator;

class DateTime extends a{
    /**
     * @var string
     */
    protected string $message = 'This is not a valid datetime format';

    /**
     * {@inheritDoc}
     * @see \SiTEL\DataUtils\Validator\a::validate()
     */
    public function validate($value):bool{
        $date= \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        return ($date!== false && date_format($date, 'Y-m-d H:i:s')===$value);
    }
}
