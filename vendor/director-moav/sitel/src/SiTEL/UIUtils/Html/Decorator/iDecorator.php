<?php namespace SiTEL\UIUtils\Html\Decorator;
/**
 * Decorates a piece of string and encapsulates specific logic
 * for that encapsulation
 * 
 * @author itay
 *
 */
interface iDecorator{
    public function decorate(\SiTEL\UIUtils\Html\iHtmlElement $Element):string;
}
