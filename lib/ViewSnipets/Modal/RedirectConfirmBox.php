<?php
class ViewSnipets_Modal_RedirectConfirmBox{
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
            <h2 class="modal-title"><?=$title?></h2>
          </div>
          <div class="modal-body">
 
<?
	}
	
	
	
	
	/**
	 * Echo the modal footer
	 */
	static public function render_footer($url,$label){
?>
		  </div>
          <div class="modal-footer">
             <a href="<?= org_url() . $url?>" class="action-btn-primary" ><?= $label?></a>               
          </div>
		</div>
      </div>
    </div>
 <?
	}	
}