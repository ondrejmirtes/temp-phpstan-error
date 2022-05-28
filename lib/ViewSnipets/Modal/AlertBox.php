<?php
/**
 * class to render dissmisable alerts insode for elements 
 * after successful updates/inserts
 * 
 * TODO handle issues too.
 * 
 * @author itaymoav
 */
class ViewSnipets_Modal_AlertBox{
	const	ALERT_SUCCESS  = 'success',
			ALERT_FAILURE  = 'failure'
	;
	
	/**
	 * 
	 * @param string $label
	 */
	static public function render($label,$fail_msg=''){
		$me = new static($label,$fail_msg);
		$chk = $me->active();
		if($chk){
			$f = 'render_html_' . $chk; 
			$me->$f();
		}
	}
	
	/**
	 * 
	 * @var string
	 */
	private $label = '',
			$fail_msg =''
	;
	
	/**
	 * 
	 * @param string $label
	 */
	private function __construct($label,$fail_msg){
		$this->label = $label;
		$this->fail_msg = $fail_msg;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	private function active(){
		if(isset($_GET[$this->label])){
				return $_GET[$this->label];
		}
		return false;
	} 
	
	/**
	 * 
	 */
	private function render_html_success(){
?>
		<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		Data was updated successfully.
		</div>
<?		
	}

	/**
	 *
	 */
	private function render_html_failure(){
	?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<?=$this->fail_msg?>
			</div>
	<?		
	}
		
}//EOF CLASS