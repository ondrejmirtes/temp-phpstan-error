<?php namespace SiTEL\UIUtils\Html\Decorator;

class None implements iDecorator
{

    /**
     * convert object to string
     * 
     * {@inheritDoc}
     * @see \SiTEL\UIUtils\Html\Decorator\iDecorator::decorate()
     */
    public function decorate(\SiTEL\UIUtils\Html\iHtmlElement $Element):string{
        return $Element->html();
    }
}
