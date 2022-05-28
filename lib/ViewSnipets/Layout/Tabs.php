<?php
class ViewSnipets_Layout_Tabs{
	
	/**
	 * 
	 * @param Request_Default $r
	 * @param array $tabs_in [tab_name,tab_url],[tab_name,tab_url],[tab_name,tab_url]
	 * @param number $active_index 0..n
	 */
	static public function render(Request_Default $r,$active_index=0,array $tabs_in = []){
		return new static($r,$active_index,$tabs_in);
	}
	
	/**
	 * @var Request_Default
	 */
	protected $r		= null,
			  $tabs_in	= [],
			  $active_index = 0
	;
	
	public function __construct(Request_Default $r,$active_index,array $tabs_in=[]){
		$this->r = $r;
		$this->tabs_in = $tabs_in;
		$this->active_index = $active_index;
	}
	
	/**
	 *
	 */
	protected function make_html(){
		$tabs = '';
		foreach($this->tabs_in as $i=>$one_tab){
			$class ='';
			if($i == $this->active_index){
				$class='active';
			}
			if(!isset($one_tab['tasks']) || User_Current::canDisplayTasks($one_tab['tasks'])){
				$tabs .= '<li class="' . $class . '"><a href="' . $one_tab[1] . '">' . $one_tab[0] . '</a></li>';
			}
		}
		return $tabs;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString(){
		return $this->make_html();	
	}
}