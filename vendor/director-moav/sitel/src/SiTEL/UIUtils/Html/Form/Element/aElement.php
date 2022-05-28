<?php namespace SiTEL\UIUtils\Html\Form\Element;

/**
 * 
 * @author itay
 *
 */
abstract class aElement implements \SiTEL\UIUtils\Html\iHtmlElement,\SiTEL\UIUtils\Html\Decorator\iFormElementDecorable{
    
    /**
     * 
     * @var string
     */
    public const TYPE='abstract';

    /**
     * @var array<string,mixed> of actual html attributes
     */
    protected array $attr = [];
    
    /**
     * @var string text ONLY of the form label
     */
    protected string $label;
    
    /**
     * @var \SiTEL\UIUtils\Html\Decorator\iFormElementDecorator|\SiTEL\UIUtils\Html\Decorator\None
     */
    protected $decorator;
    
    /**
     * 
     * @var string
     */
    protected string $default_value ='';
    
    
    /**
     *
     * @param String $label
     * @param String $name
     * @param String $value
     * @param array<string,mixed> $attr
     * @param \SiTEL\UIUtils\Html\Decorator\iFormElementDecorator|NULL $decorator
     */
    function __construct(string $label, string $name, string $value='', array $attr=[], \SiTEL\UIUtils\Html\Decorator\iFormElementDecorator $decorator=null){
        $this->decorator = $decorator?:new \SiTEL\UIUtils\Html\Decorator\None;
        $this->attr = $attr;
        $this->attr['name']=$name;
        $this->set_id();
        $this->label = $label;
        $this->set_value($value);
        $this->attr['type'] = static::TYPE;
    }
    
    /**
     * Sets the id
     * 
     */
    protected function set_id():void{
        if (!isset($this->attr['id'])){
            $this->attr['id'] = str_replace(array('[',']'),'_',$this->attr['name']);
        }
    }
    
    /**
     * Sets the value for appropriate elements in below priority
     * 1. if value was set in the attributes then it will go with that value
     * 3. otherwise the defualt value will be assign to the element
     *
     * @return \SiTEL\UIUtils\Html\Form\Element\aElement
     */
    public function set_value(string $value):\SiTEL\UIUtils\Html\Form\Element\aElement{
        if($value){
            $this->attr['value']=$value;
        }
        
        if(!isset($this->attr['value'])){
            $this->attr['value'] = $this->default_value;
        }
        return $this;
    }
    /**
     * get the Name attr
     * @return string
     */
    public function name():string{
        return $this->attr['name'];
    }
    
    /**
     * I do not render it, as the decorator might
     * need to ask me some questions before he runs the 
     * render.
     * You can always render this element without a decorator
     * using the html() method
     * {@inheritDoc}
     * @see \SiTEL\UIUtils\Html\iHtmlElement::__toString()
     */
    public function __toString():string{
        try {
            return $this->decorator->decorate($this);
            
        } catch(\Exception $e){
            \fatal($e->getMessage());
            return '';
        }
    }
    
    /**
     * 
     * @param string $class
     */
    public function add_css_class(string $class):void{
        if (isset($this->attr['class'])){
            $this->attr['class'] .= " {$class}";
        } else {
            $this->attr['class'] = $class;
        }
    }
    
    /**
     * 
     * @return string
     */
    public function get_id():string{
        return $this->attr['id'];
    }
    
    /**
     * Generates the element's string
     *
     * @return String
     */
    public function html():string{
        return '<input ' . $this->unpack_attr() . '/>';
    }
    
     /**
      * 
      * @return mixed
      */
     public function get_value(){
         return $this->attr['value'];
     }
     
    /**
     * 
     * @return string
     */
     public function get_label():string{
         return $this->label;
     }
     /**
      * 
      * @param string $value
      * @return aElement
      */
     public function set_label(string $value):aElement{
         $this->label = $value;
         return $this;
     }
     
     /**
      *
      * @param \SiTEL\UIUtils\Html\Decorator\iFormElementDecorator $decorator
      * @return aElement
      */
     public function set_decorator(\SiTEL\UIUtils\Html\Decorator\iFormElementDecorator $decorator):\SiTEL\UIUtils\Html\Form\Element\aElement{
         $this->decorator = $decorator;
         return $this;
     }
     
     /**
      * 
      * {@inheritDoc}
      * @see \SiTEL\UIUtils\Html\Decorator\iFormElementDecorable::get_decorator()
      */
     public function get_decorator(){
         return $this->decorator;
     }
     
     /**
      * From array to string which is html attributes legit
      * @return string
      */
     protected function unpack_attr():string{
         // Attribute string formatted for use inside HTML element
         $unpacked_attribs = '';
         foreach($this->attr as $k=>$v){
             $v = htmlspecialchars($v);
             $unpacked_attribs .= "{$k}='{$v}' ";
         }
         return $unpacked_attribs;
     }
}

