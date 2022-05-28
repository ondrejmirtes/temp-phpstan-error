<?php
/**
 *  Generates view snippets for course content pages in course details
 *  for an enrolled user
 *  @author Admin
 *
 */
abstract class ViewSnipets_BLElems_CourseContent{
	
	/**
	 *  Displays online content tasks in curriculum course details
	 *  @param array $tasks
	 */
	static public function CurriculumOnlineContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):?>
			<tr>
				<td class="status-<?= $content->content_container_status?>">
					<?= ucfirst($content->content_container_status);?><strong><?= $content->is_mandatory?"":"(Optional)"?></strong>
				</td>
				<td class="course-task-col">
					<a class="title-link-sm" href="#"><?= $content->container_name?></a>
					<a><span class="details-toggle" data-toggle="collapse" data-target="#content-<?= $content->content_enrollment_id?>"></span></a>
					<div id="content-<?= $content->content_enrollment_id?>" class="collapse">
						<div>
						<?= $content->description;?>
						</div>
						<ul class="bulleted-list">
							<li>
								<label>Faculty: </label> <?= $content->faculty?>
							</li>
							<li>
								<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
							</li>
						</ul>
					</div>
				</td>
			</tr>
			<? 
			endforeach;
		endforeach;
	}
	
	/**
	 *  Displays online content tasks in catalog course details
	 *  @param array $tasks
	 */
	static public function CatalogOnlineContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):?>
			<tr>
				<td class="course-task-col">
					<?= $content->container_name?>
					<a><span class="details-toggle" data-toggle="collapse" data-target="#content-<?= $content->content_container_id?>"></span></a>
					<div id="content-<?= $content->content_container_id?>" class="collapse">
						<div>
						<?= $content->description;?>
						</div>
						<ul class="bulleted-list">
							<li>
								<label>Faculty: </label> <?= $content->faculty?>
							</li>
							<li>
								<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
							</li>
						</ul>
					</div>
				</td>
			</tr>
			<?
			endforeach;
		endforeach;
	}
	
	/**
	 *  Displays pre-session tasks in curriculum course details
	 *  @param array $tasks
	 */
	static public function CurriculumPresessionContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):?>
			<tr>
				<td class="status-<?= $content->content_container_status?>">
					<?= ucfirst($content->content_container_status);?><strong><?= $content->is_mandatory?"":"(Optional)"?></strong>
				</td>
				<td class="course-task-col">
					<a class="title-link-sm" href="#"><?= $content->container_name?></a>
					<a><span class="details-toggle" data-toggle="collapse" data-target="#presession-<?= $content->content_enrollment_id?>"></span></a>
					<div class="collapse" id="presession-<?= $content->content_enrollment_id?>">
						<div>
						<?= $content->description;?>
						</div>
						<ul class="bulleted-list">
							<li>
								<label>Faculty: </label> <?= $content->faculty?>
							</li>
							<li>
								<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
							</li>
						</ul>
					</div>
				</td>
			</tr>
			<? endforeach;
		 endforeach;
	}
	
	/**
	 *  Display pre-session tasks in catalog course details
	 *  @param array $tasks
	 */
	static public function CatalogPresessionContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):?>
			<tr>
				<td class="course-task-col">
					<strong><?= $content->is_mandatory?"":"(Optional)"?></strong>
					<?= $content->container_name?>
					<a><span class="details-toggle" data-toggle="collapse" data-target="#content-<?= $content->content_container_id?>"></span></a>
					<div id="content-<?= $content->content_container_id?>" class="collapse">
					<div>
						<?= $content->description;?>
						</div>
						<ul class="bulleted-list">
							<li>
								<label>Faculty: </label> <?= $content->faculty?>
							</li>
							<li>
								<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
							</li>
						</ul>
					</div>
				</td>
			</tr>
		<?	endforeach;
		endforeach;
	}
	
	/**
	 *  Displays live event tasks in curriculujm course details
	 *  @param array $tasks
	 */
	static public function CurriculumLiveContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):?>
			<tr class="subheading">
				<th colspan="2">
					<span class="task-date"><?= $content->formatted_date?></span> 
				</th>
			</tr>
			<tr>
				<td class="status-<?= $content->content_container_status?>">
					<?= ucfirst($content->content_container_status); ?>
				</td>
				<td class="course-task-col"> 
					<div class="course-task">
						<div class="course-task-details">
							<div class="task-name"><?= $content->container_name?></div>
							<div class="task-time"><?= $content->formatted_start_time . ' to ' . $content->formatted_end_time?></div>
							<? if($content->location_info):?><div class="location">Location: <?= $content->location_info?> 
							<a data-toggle="modal" 
                            	data-target="#enrolled-location-modal"
                            	data-title="<?= $content->course_title?>"
                            	data-location="<?= $content->location_info ?>"
                            	data-adfullname="<?= $content->ad_full_name?>"
                            	data-comments="<?= $content->instructions?>"
                            	data-ademail="<?= $content->ad_username?>"
                            	data-latitude="<?= $content->latitude?>"
                            	data-longitude="<?= $content->longitude?>"
                            	data-userid="<?= User_Current::id()?>"
                            	data-eventdateid="<?= $content->event_date_id?>"
                            	data-room="<?= $content->room_info?>"
                            	onclick="CurriculumMyEducationContent.initLocationModal($(this));"><span class="glyphicon glyphicon-map-marker"></span></a></div><? endif;?>
							<? if($content->room_info):?><div class="task-info"><label>Room: <?= $content->room_info?></label></div><? endif;?>
							<? if($content->instructors):?><div class="task-info"><label>Instructor: <?= $content->instructors?></label></div><? endif; ?>
						</div>
					</div>
				</td>
			</tr>
			<? endforeach;
		endforeach;
	}
	
	/**
	 *  Displays live event tasks in curriculujm course details
	 *  @param array $tasks
	 */
	static public function CatalogLiveContent($tasks){
		foreach($tasks as $task):
				foreach($task->content as $content):?>
				<tr>
					<td class="course-title-col">
            			<?= $content->container_name;?>
					</td>
				</tr>
				<? endforeach;
			endforeach;
		}
	
	
	/**
	 * 	Displays post session tasks in curriculum course details
	 *  @param array $task
	 */
	static public function CurriculumPostsessionContent($tasks){
		foreach($tasks as $task):
			foreach($task->content as $content):
			?>
				<tr>
					<td class="status-<?= $content->content_container_status?>">
						<?= ucfirst($content->content_container_status)?> <strong><?= $content->is_mandatory?"":"(Optional)"?></strong>
					</td>
					<td class="course-task-col">
						<? if(!$content->can_launch):?>
						<span class="glyphicon glyphicon-lock"></span> <?= $content->container_name?>
						<? else: ?>
						<a class="title-link-sm" href="#"><?= $content->container_name?></a>
						<? endif;?>
						<a><span class="details-toggle" data-toggle="collapse" data-target="#post-session-<?= $content->content_enrollment_id?>"></span></a>
						<div class="collapse" id="post-session-<?= $content->content_enrollment_id?>">
							<div>
							<?= $content->description?>
							</div>
							<ul class="bulleted-list">
								<li>
									<label>Faculty: </label> <?= $content->faculty?>
								</li>
								<li>
									<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
								</li>
							</ul>
						</div>
					</td>
				</tr>
			<? endforeach;
		endforeach;
	}
	
	/**
	 *  Display post session tasks in catalog course details
	 *  @param array $task
	 */
	static public function CatalogPostsessionContent($tasks){
		foreach($tasks as $task):
		foreach($task->content as $content):
		?>
					<tr>
						<td class="course-task-col">
							 <strong><?= $content->is_mandatory?"":"(Optional)"?></strong>
						<?= $content->container_name?>
					<a><span class="details-toggle" data-toggle="collapse" data-target="#content-<?= $content->content_container_id?>"></span></a>
					<div id="content-<?= $content->content_container_id?>" class="collapse">
					<div>
						<?= $content->description;?>
						</div>
						<ul class="bulleted-list">
							<li>
								<label>Faculty: </label> <?= $content->faculty?>
							</li>
							<li>
								<label>ID: </label> <?= ViewSnipets_BLElems_Course::readable_course_id($content->course_wrapper_id, 'content_wrapper_'.$content->content_type);?>
							</li>
						</ul>
					</div>
						</td>
					</tr>
				<? endforeach;
			endforeach;
		}
} 