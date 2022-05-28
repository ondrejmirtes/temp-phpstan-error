<?php
trait ViewSnipets_Data_Grid_SOPager{
	/**
	 * for $is_ajax:
	 * 0=no ajax 		where paging is through form
	 * 1=form ajax 		where datagrid is in its own form
	 * 2=js ajax		where datagrid is in other form
	 */
	public function getViewStackoverflow($id,$is_ajax = false){
		$View = new stdClass;
		
		$number_of_pages=$this->getTotalPages();
		$current_page_number=$this->getCurrentPage();
		$points='<span>...</span>';
		$from 	= $this->getCurrentPage() * $this->getPageSize() - $this->getPageSize() + 1;
		$to		= $from+$this->getCurrentPageTotal()-1;
		$prev='';
		$next='';
		
		if($number_of_pages>3){
			$c = $this->getNavCommand($id,(($this->getCurrentPage()>1)?$this->getCurrentPage()-1:1),$is_ajax);
			$prev = "<a {$c}>Prev</a>";
			$c = $this->getNavCommand($id,(($this->getCurrentPage()<$this->getTotalPages())?($this->getCurrentPage()+1):($this->getTotalPages())),$is_ajax);
			$next = "<a {$c}>Next</a>"; 
		}
		
		if($current_page_number == 1){
			$prev = '';
		}
		
		$pager_pages_nav = "<div class='results-prev-next'>{$prev}";
		$glue_to_start=false;
		if ($current_page_number<5 && $number_of_pages>1){
			for($i=1;$i<=5 && $i<=$number_of_pages;$i++):
				if($i==$current_page_number):
					$pager_pages_nav .= "&nbsp;{$i}&nbsp;";
				else:
					$c = $this->getNavCommand($id,$i,$is_ajax);
					$pager_pages_nav .= " <a {$c}>{$i}</a>";
				endif;
								
			endfor;
			$glue_to_start=true;
		}elseif($number_of_pages>5){ //we may get here when we have no pages at all.
			$c = $this->getNavCommand($id,1,$is_ajax);
			$pager_pages_nav .= " <a {$c}>1</a> {$points}";
		}
		
		if(($number_of_pages-4)<$current_page_number && !$glue_to_start && $number_of_pages>1){
			for($i=$number_of_pages-4;$i<=$number_of_pages;$i++):
				if($i==$current_page_number):
					$pager_pages_nav .= "&nbsp;{$i}&nbsp;";
				else:
					$c = $this->getNavCommand($id,$i,$is_ajax);
					$pager_pages_nav .= " <a {$c}>{$i}</a>";
				endif;
			endfor;
		}elseif(!$glue_to_start && $number_of_pages>1){
			for($i=$current_page_number-2;$i<=$current_page_number+2;$i++):
				if($i==$current_page_number):
					$pager_pages_nav .= "&nbsp;{$i}&nbsp;";
				else:
					$c = $this->getNavCommand($id,$i,$is_ajax);
					$pager_pages_nav .= " <a {$c}>{$i}</a>";
				endif;
			endfor;
			$c = $this->getNavCommand($id,$number_of_pages,$is_ajax);
			$pager_pages_nav .= " {$points} <a {$c}>{$number_of_pages}</a>";
		}elseif($number_of_pages>5){
			$c = $this->getNavCommand($id,$number_of_pages,$is_ajax);
			$pager_pages_nav .= " {$points} <a {$c}>{$number_of_pages}</a>";
		}
		
		if($number_of_pages == $current_page_number){
			$next = '';
		}
		
		$pager_pages_nav .= " {$next}</div>";
		
		//prepare return
		$View->number_of_pages = $number_of_pages;
		$View->current_page_number = $current_page_number;
		$View->from = $from;
		$View->to = $to;
		$View->pager_pages_nav = $pager_pages_nav;
		return $View;		
	}
	
	/**
	 * for $is_ajax:
	 * 0=no ajax 		where paging is through form
	 * 1=form ajax 		where datagrid is in its own form
	 * 2=js ajax		where datagrid is in other form
	 */
	private function getNavCommand($id,$page,$is_ajax){
		if($is_ajax === self::AJAX_FILTER) return "onclick='Datagrid.ObjCollection[\"{$id}\"].page = {$page};Datagrid.ObjCollection[\"{$id}\"].reloadAjaxFilter();return false;'";
		if($is_ajax === self::THE_ANSWER_TO_EVERYTHING_IE_PAGER_INSIDE_OTHER_FORM) return "onclick='Datagrid.ObjCollection[\"{$id}\"].page = {$page};Datagrid.ObjCollection[\"{$id}\"].reload();return false;'";
		if($is_ajax) return "onclick='$(\"#page\").val({$page});$(\"#dataset-form-{$id}\").submit();return false;'";
		$current_url = "https://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
		$pattern=['/&*page=[0-9]+/','/&*page_size=[0-9]+/'];
		$replacement=['',''];
		$paging = preg_replace($pattern,$replacement,$current_url);
		$a_to_boo = '?';
		if(strpos($paging,'?')){
			$a_to_boo = '&';
		}
		
		$wn='';
		if(SiTEL_View::$windowName && strpos($paging,'window_name') === false){
			$wn = '&window_name=' . SiTEL_View::$windowName;
		}
		
		//ordering - no sql injection danger here TODO might need to move it to a better place in the future...we will see.
		$order_by = '';
		
		if(isset($_REQUEST['order_by'])){
			$order_by = '&order_by='.$_REQUEST['order_by'];
			$order_by_dir = '&order_by_dir=asc';
			if(isset($_REQUEST['order_by_dir'])){
				$order_by_dir = '&order_by_dir=' . $_REQUEST['order_by_dir'];
			}
			$order_by .= $order_by_dir;
		}
		
		$paging .= "{$a_to_boo}page={$page}&page_size={$this->pageSize}{$wn}{$order_by}";
		return "href='{$paging}' ";
	}
}