<?php
/**
 *  Class to render  alerts inside for elements 
 *  Will always be visible when called unaffected by $_GET
 * 
 *  @author Admin
 */
class ViewSnipets_Modal_StaticMessageBox {

	/**
	 *  Display with success styling
	 */
	public static function render_html_success($message = 'Data updated successfully'){
	?>
		<div class="alert alert-success" role="alert">
		<?= $message?>
		</div>
	<?		
	}

	/**
	 *  Display with failure styling
	 */
	public static function render_html_failure($message = 'Failed to update data'){
	?>
			<div class="alert alert-danger" role="alert">
			<?=$message?>
			</div>
	<?		
	}
	
	/**
	 *  Render warning messages 
	 */
	public static function render_html_warning($message = ''){
	?>
		<div class="alert alert-warning" role="alert">
		<?= $message?>
		</div>
	<?
	}
	
	/**
	 *  Render muted messages 
	 */
	public static function render_html_muted($message = ''){
	?>
		<div class="alert alert-muted" role="alert">
		<?= $message?>
		</div>
	<?
	}
		
}//EOF CLASS