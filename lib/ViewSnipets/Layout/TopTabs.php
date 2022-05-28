<?php
abstract class ViewSnipets_Layout_TopTabs{
	
	/**
	 *  course/catalog/.../
	 * 
	 *  @param Request_Default $r
	 *  @param string $active_index
	 *  @return ViewSnipets_Layout_Tabs
	 */
	static public function catalog(Request_Default $r, $active_index=OVERRIDE){
		if($active_index == OVERRIDE){ //override this value
			if($r->action == 'all') $active_index = 0;
			elseif($r->action == 'calendar') $active_index = 1;
		}
		
		return ViewSnipets_Layout_Tabs::render($r, $active_index, [
			['Search Catalog', org_url() . '/course/catalog/search/all/'],
			['Calendar', org_url() . '/course/catalog/search/calendar/']
		]);
	}
	
	/**
	 * curriculum/myeducation/..../
	 * 
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education(Request_Default $r,$active_index=OVERRIDE){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'current') $active_index = 0;
			elseif($r->action == 'saved') $active_index = 2;
			elseif($r->controller == 'history') $active_index = 1;
		}
		
		return ViewSnipets_Layout_Tabs::render($r,$active_index,[
			['Current Courses',org_url() . '/curriculum/myeducation/courses/current/'],
			['History &amp; Transcript',org_url() . '/curriculum/myeducation/history/completed/'],
			['Saved',org_url() . '/curriculum/myeducation/courses/saved/']
		]);
	}
	
	/**
	 * /curriculum/myeducation/history/.../
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education_history(Request_Default $r,$active_index=OVERRIDE){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'completed') $active_index = 0;
			elseif($r->action == 'incomplete') $active_index = 1;
			elseif($r->action == 'reports') $active_index = 2;
		}
	
		return ViewSnipets_Layout_Tabs::render($r,$active_index,[
				['Completed',org_url() . '/curriculum/myeducation/history/completed/'],
				['Incomplete',org_url() . '/curriculum/myeducation/history/incomplete/'],
				['My Historical Reports',org_url() . '/curriculum/myeducation/history/reports/']
				]);
	}
	
	/**
	 *  /curriculum/myeducation/courses/..../
	 *  @param Request_Default $r
	 * 	@param string $active_index
	 *  @param array $request_override for when values need to differ from request
	 *  @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education_course_enrolled_ondemand(Request_Default $r, $active_index=OVERRIDE, array $request_override = []){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'info') $active_index = 0;
			elseif($r->action == 'content') $active_index = 1;
			elseif($r->action == 'reschedule') $active_index = 2;
		}
	
		$enrollment_id = isset($request_override['course_enrollment_id'])?$request_override['course_enrollment_id']:$r->get('enrollment');
		
		$tabs =
		[
			['Course Details',org_url() . '/curriculum/myeducation/courses/info/enrollment/'.$enrollment_id],
			['Course Content',org_url() . '/curriculum/myeducation/courses/content/enrollment/'.$enrollment_id],
		];
	
	
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	/**
	 *  /curriculum/myeducation/courses/..../
	 *  @param Request_Default $r
	 * 	@param string $active_index
	 *  @param array $request_override for when values need to differ from request
	 *  @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education_course_enrolled_live(Request_Default $r, $active_index=OVERRIDE, array $request_override = []){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'info') $active_index = 0;
			elseif($r->action == 'content') $active_index = 1;
			elseif($r->action == 'reschedule') $active_index = 2;
		}
		
		$enrollment_id = isset($request_override['course_enrollment_id'])?$request_override['course_enrollment_id']:$r->get('enrollment');
				
		$tabs =
		[
			['Course Details',org_url() . '/curriculum/myeducation/courses/info/enrollment/'.$enrollment_id],
			['Course Content',org_url() . '/curriculum/myeducation/courses/content/enrollment/'.$enrollment_id],
			['Reschedule',org_url() . '/curriculum/myeducation/courses/reschedule/enrollment/'.$enrollment_id]
		];
		
	
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	/**
	 *  /curriculum/myeducation/courses/..../
	 *  @param Request_Default $r
	 * 	@param string $active_index
	 *  @param array $request_override for when values need to differ from request
	 *  @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education_course_ondemand(Request_Default $r, $active_index=OVERRIDE,array $request_override = []){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'info') $active_index = 0;
			elseif($r->action == 'content') $active_index = 1;
			elseif($r->action == 'reschedule') $active_index = 2;
		}
		$course_id = isset($request_override['course_id'])?$request_override['course_id']:$r->get('course');
	
		$tabs = [
			['Course Details',org_url() . '/curriculum/myeducation/courses/info/course/'.$course_id],
			['Course Content',org_url() . '/curriculum/myeducation/courses/content/course/'.$course_id]
		];
		
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	/**
	 *  /curriculum/myeducation/courses/..../
	 *  @param Request_Default $r
	 * 	@param string $active_index
	 *  @param array $request_override for when values need to differ from request
	 *  @return ViewSnipets_Layout_Tabs
	 */
	static public function my_education_course_live(Request_Default $r, $active_index=OVERRIDE, array $request_override = []){
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'info') $active_index = 0;
			elseif($r->action == 'content') $active_index = 1;
			elseif($r->action == 'reschedule') $active_index = 2;
		}
		$course_id = isset($request_override['course_id'])?$request_override['course_id']:$r->get('course');
	
		$tabs =
		[
			['Course Details',org_url() . '/curriculum/myeducation/courses/info/course/'.$course_id],
			['Course Content',org_url() . '/curriculum/myeducation/courses/content/course/'.$course_id],
			['Schedule (Enroll)',org_url() . '/curriculum/myeducation/courses/reschedule/course/'.$course_id]
		];
		
	
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	/**
	 * educator/.../.../.../
	 *
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function educator_tools(Request_Default $r,$active_index=OVERRIDE){
		/*TODO refactor when it beomes necessary, or delete
		if($active_index == OVERRIDE){//override this value
			if($r->action == 'current') $active_index = 0;
			elseif($r->action == 'saved') $active_index = 2;
			elseif($r->controller == 'history') $active_index = 1;
		}
		*/
		return ViewSnipets_Layout_Tabs::render($r,$active_index,[
				['Courses',org_url() . '/educator/courses/view/active/','tasks'=>[User_RBAC_Tasks_CourseView::ID]],
				['Content',org_url() . '/educator/content/view/active/','tasks'=>[User_RBAC_Tasks_ContentView::ID]],
				['Educator Reports',org_url() . '/educator/reports/view/active/','tasks'=>[User_RBAC_Tasks_CourseCreate::ID, User_RBAC_Tasks_CourseUpdate::ID, User_RBAC_Tasks_ContentCreate::ID, User_RBAC_Tasks_ContentUpdate::ID]]
				]);
	}
	
	/**
	 * educator/courses/view/.../
	 *
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function educator_tools_courses(Request_Default $r,$active_index=OVERRIDE){
		if($active_index == OVERRIDE){//override this value
			$action_map = [
				'active'		=> 0,
				'mycalendar'	=> 1,
				'inactive'		=> 2,
				'expired'		=> 3
			];
			$active_index = $action_map[$r->action];
		}

		return ViewSnipets_Layout_Tabs::render($r,$active_index,[
				['Active Courses',org_url() . '/educator/courses/view/active/'],
				['My Course Calendar',org_url() . '/educator/courses/view/mycalendar/'],
				['Inactive Courses',org_url() . '/educator/courses/view/inactive/'],
				['Expired Courses',org_url() . '/educator/courses/view/expired/']
		]);
	}
	
	/**
	 * manager/.../.../.../
	 *
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function manager_tools(Request_Default $r,$active_index=OVERRIDE){
		$tabs = [
				'learners'		=> ['Learners',					org_url() . '/manager/learners/view/active/','tasks'=>[User_RBAC_Tasks_UserUpdate::ID]],
				'departments'	=> ['Departments',				org_url() . '/manager/departments/view/departments/', 'tasks'=>[User_RBAC_Tasks_UserUpdate::ID]],
				'rollout'		=> ['Rollout (Assign Courses)',	org_url() . '/manager/rollout/view/active/','tasks'=>[User_RBAC_Tasks_EnrollmentView::ID]],
				'edugrp'		=> ['Education Groups',			org_url() . '/manager/educationgroup/view/all/','tasks'=>[User_RBAC_Tasks_UserEdugrpUpdate::ID]],
				'reports'		=> ['Manager Reports',			org_url() . '/manager/reports/view/dashboard/', 'tasks'=>[User_RBAC_Tasks_UserUpdate::ID, User_RBAC_Tasks_UserEdugrpUpdate::ID]]
		];
		
		/**if(!User_Current::has_any_roles(
				User_RBAC_Roles_ResourceAdministrator::ID,
				User_RBAC_Roles_DepartmentHead::ID,
				User_RBAC_Roles_DepartmentHeadCoordinator::ID,
		        User_RBAC_Roles_EducationSpecialist::ID
		)){
			unset($tabs['learners']);
			unset($tabs['departments']);
		}**/
				
		
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	/**
	 * manager/learners/view/.../
	 *
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function manager_tools_learners(Request_Default $r,$active_index=OVERRIDE){
		if($active_index == OVERRIDE){//override this value
			$active_index= $r->action;
		}
	
		$tabs = [
				'active' 		=> ['Active Learners',	org_url() . '/educator/courses/view/active/'],
				'pending'		=> ['Pending',			org_url() . '/educator/courses/view/mycalendar/'],
				'unassigned'	=> ['Unassigned',		org_url() . '/educator/courses/view/inactive/'],
				'terminated'	=> ['Terminated',		org_url() . '/educator/courses/view/expired/'],
				'feed'			=> ['Feed',				org_url() . '/educator/courses/view/expired/']
		];
		
		if(!Organization_Current::hrFeed()){
			unset($tabs['feed']);
		}
		
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
	
	
	/**
	 * manager/.../.../.../
	 *
	 * @param Request_Default $r
	 * @param string $active_index
	 * @return ViewSnipets_Layout_Tabs
	 */
	static public function manager_tools_edugroup(Request_Default $r,$active_index=OVERRIDE){
		$tabs = [
		'learners'	=> ['Manage Learners',					org_url() . '/manager/educationgroup/users/all/?group_id='.$r->get('group_id')],
		'edugrp'	=> ['Edit Group Settings',				org_url() . '/manager/educationgroup/edit/group/?group_id='.$r->get('group_id')]
		];
	
		//  Unset edi group tab when not the owner of the group
		try{
			EducationGroup_Owner::check(User_Current::id(), Organization_Current::id(), $r->get('group_id'));
		}catch(EducationGroup_Exception_Owner_CheckNotOwner $e){
			unset($tabs['edugrp']);
		}
	
	
		return ViewSnipets_Layout_Tabs::render($r,$active_index,$tabs);
	}
}
    







