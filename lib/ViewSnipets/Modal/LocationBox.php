<?php
/**
 *  Modal used for displaying Google maps
 *  @author Admin
 *
 */
class ViewSnipets_Modal_LocationBox{
    //TODO check TOBEDELETED21933 for the map sections
	public static function GoogleLocationModal($id){
	    error_monitor('TOBEDELETED202142');
	?>
		<div class="light-box" id="<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h2 class="modal-title"><span id="location-modal-title"></span></h2>
					</div>
					<div class="modal-body">
						<div id="printMap">
							<div id='map_canvas' style="margin-left:0px;margin-bottom:9px; height:600px; width:860px;"></div>
						</div>
						<div class="column-clear">&nbsp;</div>
					</div>
					<div style="display:none">
						<div id="printAddress">
							<div >
								<h3>Event Information</h3>
								<ul>
									<li><label>Course</label>:  <span id="location-modal-course-title"></span></li>
									<li><label>Location</label>:  <span id="location-modal-location"></span></li>
									<li><label>Room</label>:  <span id="location-modal-room"></span></li>
									<li><label>Activity Director</label>:  <span id="location-modal-ad-full-name"></span></li>
									<li><label>Contact Email</label>:  <span id="location-modal-contact-email"></span></li>
									<li><label>Additional Comments</label>:  <span id="location-modal-comments"></span></li>
								</ul>
							</div>
						</div>
						<div class="light-box" id="emailConfirm" style="text-align:center; margin:5px;">Message sent.</div>
					</div>
				<div class="modal-footer">
					<a class="action-btn-primary" id="print-btn">Print Address</a>              
				</div>
			</div>
      	</div>
      </div>
      <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&region=US"></script>
	<? 
	/**================================= View Snippet JS ==========================================**/
	
	Layout::inject(Layout::SNIPPET_SPECIFIC_JS,'sitel/snippet_specific/location_modal.js'); //TOBEDELETED is it used properly?
	
	}
}