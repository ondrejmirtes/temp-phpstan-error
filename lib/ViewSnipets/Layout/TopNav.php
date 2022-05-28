<?php
class ViewSnipets_Layout_TopNav{
	
	static public function render(Request_Default $r){
		return new static($r);
	}
	
	/**
	 * @var Request_Default
	 */
	protected $r = null;
	
	public function __construct(Request_Default $r){
		$this->r = $r;
	}
	
	/**
	 * TODO cache this per user?
	 */
	protected function make_html(){
		$nav = [
			'myeducation'	=> ['class'=>'','text'=>'My Education','url'=>org_url() . '/curriculum/myeducation/courses/current/'],
			'catalog'		=> ['class'=>'','text'=>'Course Catalog','url'=>org_url() . '/course/catalog/search/all/']
		];
		
		//Am I an educator?
		if(User_Current::has_any_display_roles(
								User_RBAC_Roles_ActivityCoordinator::ID,
								User_RBAC_Roles_ActivityDirector::ID,
								User_RBAC_Roles_Instructor::ID,
								User_RBAC_Roles_Faculty::ID,
								User_RBAC_Roles_VisitingFaculty::ID
				)){
			$nav['educator']=['class'=>'','text'=>'Educator','url'=>org_url() . '/educator/course/view/active/'];
			
		}
		
		//Am I a manager?
		if(User_Current::has_any_display_roles(
				User_RBAC_Roles_ResourceAdministrator::ID,
				User_RBAC_Roles_DepartmentHead::ID,
				User_RBAC_Roles_DepartmentHeadCoordinator::ID,
				User_RBAC_Roles_DepartmentHeadCoordinator::ID
		)){
			$nav['manager']=['class'=>'','text'=>'Manager','url'=>org_url() . '/manager/learners/view/active/'];
				
		}elseif(User_Current::has_any_display_roles(User_RBAC_Roles_EducationSpecialist::ID)){
			$nav['manager']=['class'=>'','text'=>'Manager','url'=>org_url() . '/manager/educationgroup/view/all/'];
		}
		
		/**
		 * Mark the active link
		 */
		if($this->r->module == 'curriculum' && $this->r->sub_module == 'myeducation'){
			$nav['myeducation']['class'] = 'active';
		}elseif($this->r->module == 'course' && $this->r->sub_module == 'catalog'){
			$nav['catalog']['class'] = 'active';
		}elseif($this->r->module == 'educator'){
			$nav['educator']['class'] = 'active';
		}elseif($this->r->module == 'manager'){
			$nav['manager']['class'] = 'active';
		}
		
		$nav_html = '';
		foreach($nav as $link){
			$nav_html .= '<li class="' . $link['class'] . '"><a href="' . $link['url'] . '">' . $link['text'] . '</a></li>';
		}
		
		return $nav_html;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString(){
	    $r = '';
	    try{
            $r = $this->make_html();
	    }catch (Exception $e){
            error($r);
            $r = '';
	    }finally {
	        return $r;
	    }
	}
}