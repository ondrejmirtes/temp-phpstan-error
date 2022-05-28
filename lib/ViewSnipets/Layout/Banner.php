<?php
class ViewSnipets_Layout_Banner{
    
    /**
     *
     * @param Request_Default $r
     * @param array $tabs_in [tab_name,tab_url],[tab_name,tab_url],[tab_name,tab_url]
     * @param number $active_index 0..n
     */
    static public function render(){
        return new static();
    }
    
    /**
     *
     */
    protected function make_html(){
        $banner = "";
        
        $Redis  = (new Redis_Banner())->get_client();
        
        if($Redis->hexists('banner_text')){
            $banner_text    = $Redis->hget('banner_text');
            $banner_style   = $Redis->hget('banner_style');
            $banner = "<div  class='alert alert-{$banner_style}'><span>{$banner_text}</span></div>";
        }
        
        return $banner;
    }
    
    /**
     *
     * @return string
     */
    public function __toString(){
        return $this->make_html();
    }
}