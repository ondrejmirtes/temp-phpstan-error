<?php
abstract class ViewSnipets_Data_Grid_Pager{
	static public function render_top(BL_iPagedDataTransport $ResultSet,$static_page_size=false){
		$Pager = $ResultSet->getPager();
		$page_size = $Pager->getPageSize();
		$from 	= $Pager->getCurrentPage() * $page_size - $page_size + 1;
		$to		= $from+$Pager->getCurrentPageTotal()-1;
		$total	= $Pager->getTotal();
		
		$selected_15 = ($page_size == 15)? ' selected="selected"':'';
		$selected_100  = ($page_size == 100)? ' selected="selected"':'';
		
?>
		<div class="view-options">
          <div class="showing">Showing <?=$from?>-<?=$to?> of <?=$total?></div>
<?if(!$static_page_size):?>      
          <div class="per-page-selector">
            Show per page
            <select onChange="$(this).closest('form').submit()" name="page_size">
              <option<?=$selected_15?>>15</option>
              <option<?=$selected_100?>>100</option>
            </select>
          </div>
<?endif;?>
        </div>

<?
	}
		
	
		
		
	/**
	 * render the bootom part of the pager, where the single links are
	 * 
	 * @param BL_iPagedDataTransport $ResultSet
	 */
	static public function render_buttons(BL_iPagedDataTransport $ResultSet){
		$Pager = $ResultSet->getPager();
		if($Pager->getTotalPages() <= 1){//there is only one page, no pager is needed.
			return;
		}
		
		$View = self::get_pager_buttons($Pager);
?>
		<input type="hidden" id="page" name="page" value="<?=$Pager->getCurrentPage()?>" />
		<div class="pagination-wrapper" onclick="$(this).closest('form').submit()">
			<ul class="pagination">
				<?=$View->prev?>
				<?=$View->pager_pages_nav?>
				<?=$View->next?>
			</ul>
		</div>
			
		
<?	
        Response_View::javascript('
    $(".disabled").click(function(event){event.stopPropagation();});
        ');
	}
	
	
	/**
	 * Gets the number page and returns an onClick event
	 * @param integer $page
	 */
	static private function link_command($page){
		return "onclick='$(\"#page\").val({$page})'";
	}
	
	/**
	 * Generates the <li>.... for the pager
	 * 
	 * @param Data_MySQL_Pager $Pager
	 * @return stdClass
	 */
	static private function get_pager_buttons(Data_APager $Pager){
		$number_of_pages=$Pager->getTotalPages();
		$current_page_number=$Pager->getCurrentPage();
		$points='<li class="disabled"><span> ... </span></li>';
		
		$View = new stdClass;
		$View->prev = '';
		$View->next = '';
		$View->pager_pages_nav = '';
		
		if($number_of_pages>3){
			if($current_page_number>1){//no prev if u r on page 1
				$command = self::link_command($current_page_number-1);
				$View->prev = "<li><span {$command}>&laquo; Previous</span></li>";
			}			
			
			if($current_page_number<$number_of_pages){//no next if u r on the last page
				$command = self::link_command($current_page_number+1);
				$View->next = "<li><span {$command}>Next »</span></li>";
			}
		}
		
		$glue_to_start=false;
		if($current_page_number<5){
			for($i=1;$i<=5 && $i<=$number_of_pages;$i++):
				if($i==$current_page_number):
					$View->pager_pages_nav .= "<li class='active'><span>{$i}</span></li>";
				else:
					$command = self::link_command($i);
					$View->pager_pages_nav .= "<li><span {$command}>{$i}</span></li>";
				endif;
			endfor;
			$glue_to_start=true;
			
		}elseif($number_of_pages>5){ //we may get here when we have no pages at all.
			$command = self::link_command(1);
			$View->pager_pages_nav .= "<li><span {$command}>1</span></li>{$points}";
		}
		
		if(($number_of_pages-4)<$current_page_number && !$glue_to_start){
			for($i=$number_of_pages-4;$i<=$number_of_pages;$i++):
				if($i==$current_page_number):
					$View->pager_pages_nav .= "<li class='active'><span>{$i}</span></li>";
				else:
					$command = self::link_command($i);
					$View->pager_pages_nav .= "<li><span {$command}>{$i}</span></li>";
				endif;
			endfor;
		}elseif(!$glue_to_start){
			for($i=$current_page_number-2;$i<=$current_page_number+2;$i++):
				if($i==$current_page_number):
					$View->pager_pages_nav .= "<li class='active'><span>{$i}</span></li>";
				else:
					$command = self::link_command($i);
					$View->pager_pages_nav .= "<li><span {$command}>{$i}</span></li>";
				endif;
			endfor;
		}		
			
		if($number_of_pages>5 && ($current_page_number+3)<$number_of_pages){
			$command = self::link_command($number_of_pages);
			$View->pager_pages_nav .= "{$points}<li><span {$command}>{$number_of_pages}</span></li>";
		}
	    
		return $View;
	}
	
}