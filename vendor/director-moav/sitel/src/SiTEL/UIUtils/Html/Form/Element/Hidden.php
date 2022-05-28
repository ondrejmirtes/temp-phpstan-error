<?php namespace SiTEL\UIUtils\Html\Form\Element;

/**
 * @author itay
 */
class Hidden extends aElement{
    /**
     * 
     * @var string
     */
    public const TYPE='hidden';
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array<string,mixed> $attr
     */
    public function __construct(string $name, string $value='', array $attr=[]){
        parent::__construct('',$name,$value,$attr);
    }
}

