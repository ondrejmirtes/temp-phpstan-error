<?php
/**
 * Some view/html/shortcuts
 * to create form elements with their wrapers
 * 
 * I like to use this as scaffolding, to create the JS base code, and them move
 * it to a JS file
 * TODO make a tool of this, in devtools
 * 
 * @author itaymoav
 */
class ViewSnipets_F_ValidatorJs{

	public static function add_js(F $form_to_validate,$id){
		$validators = new self($form_to_validate,$id);
		$validators->render_js();//adds to the JS		
	}
	
	private $form		= null,
			$html_id	= ''
	;
	
	/**
	 * 
	 * @param F $form_to_validate
	 * @param unknown $html_id
	 */
	public function __construct(F $form_to_validate,$html_id){
		$this->form = $form_to_validate;
		$this->html_id = $html_id;
	}
	
	/**
	 * Populate the JS cartridge in Response View
	 */
	private function render_js(){
		//add start of js
		Response_View::javascript("
 $('#{$this->html_id}').bootstrapValidator({
     message: 'This value is not valid',
     live: 'enabled',
     feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
     },
     

     fields: {
		");
		
		//add validators js 
		foreach($this->form->getElements() as $elm){
			$this->add_elm_validators($elm);
		}
		
		//add end
		//add start of js
		Response_View::javascript("

     }
 });				
		");
	}
	
	/**
	 * Element level
	 * @param Form_Element_Simple $elm
	 */
	private function add_elm_validators(Form_Element_Simple $elm){
		if($elm->get_validators()){
			Response_View::javascript("
		    	 {$elm->name()}: {
		    	   container: '.{$elm->name()}_msg',
		           trigger:'blur',
		             validators: {
			");
			
			$this->add_validator_js($elm);
			
			Response_View::javascript("
		             }
		         },
			");
		}
	}
	
	/**
	 * validator level js
	 * 
	 * @param Form_Element_Simple $elm
	 */
	private function add_validator_js(Form_Element_Simple $elm){
		$sep = '';
		foreach($elm->get_validators() as $validator){
			$name = str_replace('Form_Validator_','',get_class($validator));
			$msg = $validator->message();
			$params = $this->make_param_js($validator->params());
			Response_View::javascript("
						  {$sep}
		                 {$name}: {
		                     message: '{$msg}'
		                     {$params}
		                 }
			");
			$sep = ',';
		}//eof foreach
	}
	
	/**
	 * @param array $params
	 * @return string
	 */
	private function make_param_js(array $params){
		$res = '';
		if(count($params)){
			foreach($params as $k=>$v){
				$res .= ",{$k}:{$v}";				
			}
		}
		return $res;
	}
}


/*
         username: {
           trigger:'blur',
             validators: {
                 notEmpty: {
                     message: 'The email is required and cannot be empty'
                 },
                 emailAddress: {
                     message: 'The input is not a valid email address'
                 }
             }
         }    
         
         
*/