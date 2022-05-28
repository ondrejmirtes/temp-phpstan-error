<?php namespace SiTEL\UIUtils\Html\Decorator;
/**
 * This interface means this object is decorable.
 * Object has to atleast provide a bare minimum of html code
 * to work.
 * 
 * @author itay
 *
 */
interface iFormElementDecorable{
    /**
     * 
     * @return iFormElementDecorator|\SiTEL\UIUtils\Html\Decorator\None
     */
    public function get_decorator();
    /**
     * 
     * @param iFormElementDecorator $decorator
     * @return \SiTEL\UIUtils\Html\Form\Element\aElement
     */
    public function set_decorator(iFormElementDecorator $decorator):\SiTEL\UIUtils\Html\Form\Element\aElement;
}

