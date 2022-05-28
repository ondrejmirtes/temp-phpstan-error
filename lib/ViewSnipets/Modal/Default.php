<?php
abstract class ViewSnipets_Modal_Default{
	/**
	 * Echo the modale header
	 * 
	 * @param string $id for js/css purposes
	 * @param string $title
	 */
	static public function render_header_form($id,$title,$action,$class=''){
?>
    <div class="light-box" id="<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <form class="form-horizontal <?=$class?>" method="post" id="<?=$id?>-form" action="<?=$action?>">
 		 <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h2 class="modal-title"><?=$title?></h2>
          </div>
          <div class="modal-body">
 
<?
	}
	
	
	
	
	/**
	 * Echo the modal footer
	 * @param string $label
	 */
	static public function render_footer_form($label = 'Save'){
?>
		  </div>
          <div class="modal-footer">
             <button type="button" class="modal-cancel" data-dismiss="modal">Cancel</button>
             <input type="submit" class="action-btn-primary" value=<?= $label?> />               
          </div>
		</div>
		</form>
      </div>
    </div>
 <?
	}	
	
	/**
	 * Echo the modale header
	 *
	 * @param string $id for js/css purposes
	 * @param string $title
	 */
	static public function render_header($id,$title){
		?>
	    <div class="light-box" id="<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
	      <div class="modal-dialog modal-lg">
	 		 <div class="modal-content">
	          <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	            <h2 class="modal-title" id="<?= $id?>-title"><?=$title?></h2>
	          </div>
	<?
	}
	
	/**
	 * Echo the modal footer
	 * @param string $label
	 */
	static public function render_footer($label = 'Save'){
		?>
			</div>
	      </div>
	    </div>
	 <?
		}	
		
	
	/**
	 *  A delete dialog modal
	 * @param unknown $id
	 * @param string $title
	 */
	
	static public function dialog_delete($id, $title=""){
?>
		<div class="light-box" id="<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="<?= $id?>-title"><?=$title?></h2>
			        </div>
			    	<div id="<?= $id?>-body" class="modal-body">
			    	
			    	</div>
          			<div class="modal-footer">
             			<button type="button" class="modal-cancel" data-dismiss="modal">Cancel</button>
             			<button id="<?= $id?>-action-btn" class="btn btn-danger" type="button" data-dismiss="modal" >Delete</button>             
          			</div>
				</div>
      		</div>
    	</div>
<?
	}
}