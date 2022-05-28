<?php
/**
 * Some view/html/shortcuts
 * to create form html
 * 
 * I like to use this as scaffolding, to create the JS base code, and them move
 * it to a JS file
 * 
 * TODO make a tool of this, in devtools
 * 
 * @author itaymoav
 */
class ViewSnipets_F_BasicForm{

	public static function print_form(F $form_to_html,$id){
	    echo(new ViewSnipets_F_BasicForm($form_to_html,$id));
	}
	
	private $form		= null,
			$html_id	= ''
	;
	
	/**
	 * 
	 * @param F $form_to_validate
	 * @param unknown $html_id
	 */
	public function __construct(F $form_to_html,$html_id){
		$this->form = $form_to_html;
		$this->html_id = $html_id;
	}
	
	/**
	 */
	private function render(){
	    $this->render_header()
	         ->render_body()
	         ->render_footer()
        ;
	    ViewSnipets_F_ValidatorJs::add_js($this->form,$this->html_id);
	}
	
	private function render_header(){
	    echo "<form id=\"{$this->html_id}\" action=\"<?=org_url()?>\" method=\"post\" autocomplete=\"off\" class=\"form-horizontal static-form\" data-toggle=\"validator\" role=\"form\">\n";
	    return $this;
	}
	
	private function render_body(){
	    foreach($this->form->getElements() as $elm){
	        ViewSnipets_F_E::text($elm,$elm->name(),false,ViewSnipets_F_E::FIELD_SIZE_LG);
	        echo "\n";
	    }
	    return $this;
	}
	
	private function render_footer(){
	    echo "</form>\n";
	    return $this;
	}
	
	public function __toString(){
	    $this->render();
	}
}