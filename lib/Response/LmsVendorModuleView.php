<?php
/**
 * Response class to handle common view files in Lms modules
 */
class Response_LmsVendorModuleView extends Response_View{
    private $lms_module_name = '';
    
    public function set_module_name(string $module_name):void{
        $this->lms_module_name = $module_name;
    }
    
    /**
     * Echo headers
     * Echo layouts
     * Echo view file itself
     */
    public function render():Response_LmsVendorModuleView{
        //headers
        foreach($this->headers as $header) header($header);
        
        //layouts before
        foreach($this->layouts as $layout) $layout->renderBefore();
        
        $view_file = CORE_PATH              . DIRECTORY_SEPARATOR  .
        'vendor'               . DIRECTORY_SEPARATOR  .
        'lms_module'           . DIRECTORY_SEPARATOR  .
        strtolower($this->lms_module_name) . DIRECTORY_SEPARATOR  .
        'src'                  . DIRECTORY_SEPARATOR  .
        $this->lms_module_name . DIRECTORY_SEPARATOR  .
        'VC'                   . DIRECTORY_SEPARATOR  .
        'views'                . DIRECTORY_SEPARATOR  .
        strtolower($this->controller) . DIRECTORY_SEPARATOR  .
        strtolower($this->action)     . '.php'
            ;
            
            if(!@include $view_file){
                throw new Exception_ViewNotFound($view_file);
            }
            
            //layouts after
            $render_after_layouts = array_reverse($this->layouts);
            foreach($render_after_layouts as $layout) $layout->renderAfter();
            return $this;
    }
}