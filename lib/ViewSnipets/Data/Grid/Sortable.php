<?php
abstract class ViewSnipets_Data_Grid_Sortable{
	static public function render_form(BL_Header_Abstract $Header){
?>
<input	type="hidden"
		id="<?=BL_Aeon::ORDER_BY?>"
		name="<?=BL_Aeon::ORDER_BY?>"
		value="<?=$Header->getSelectedOrder()[BL_Aeon::ORDER_BY]?>" />
		
<input 	type="hidden"
		id="<?=BL_Aeon::ORDER_BY_DIRECTION?>" 
		name="<?=BL_Aeon::ORDER_BY_DIRECTION?>"
		value="<?=$Header->getSelectedOrder()[BL_Aeon::ORDER_BY_DIRECTION]?>" />
<?
	}
	
	
	
	/**
	 * Renders the table head>tr for the sorting piece
	 * 
	 * @param DataEntities_Header $Header
	 * @param string $is_server_side
	 * @param unknown $extra_fields
	 * @param boolean $has_checkbox
	 */
	static public function render_table_head(BL_Header_Abstract $Header,$is_server_side=true,$extra_fields=[],$has_checkbox=false){
		self::render_table_head_server($Header,$extra_fields,$has_checkbox);
		
	}
	
	static public function render_table_head_client(BL_Header_Abstract $Header,$extra_fields=[]){
		?>
			<tr class="tablesorter-headerRow" role="row">
	           
	           
	           
	                <th data-column="0" class="tablesorter-header tablesorter-headerUnSorted" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="active-learners" unselectable="on" style="-moz-user-select: none;" aria-sort="none" aria-label="Name
	                 : No sort applied, activate to apply an ascending sort"><div class="tablesorter-wrapper"><div class="tablesorter-header-inner">Name
	                 <i class="tablesorter-icon fa fa-sort"></i></div></div>
	                </th>
	                
	                <th data-column="1" class="tablesorter-header tablesorter-headerUnSorted" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="active-learners" unselectable="on" style="-moz-user-select: none;" aria-sort="none" aria-label="Department
	                 : No sort applied, activate to apply an ascending sort"><div class="tablesorter-wrapper"><div class="tablesorter-header-inner">Department
	                 <i class="tablesorter-icon fa fa-sort"></i></div></div>
	                </th>
	                
	                <th class="filter-select tablesorter-header tablesorter-headerUnSorted" data-column="2" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="active-learners" unselectable="on" style="-moz-user-select: none;" aria-sort="none" aria-label="Verified : No sort applied, activate to apply an ascending sort">
	                	<div class="tablesorter-wrapper"><div class="tablesorter-header-inner">Verified <i class="tablesorter-icon fa fa-sort"></i></div></div>
	                	</th>
	                	
	                <th data-column="3" class="tablesorter-header tablesorter-headerUnSorted" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="active-learners" unselectable="on" style="-moz-user-select: none;" aria-sort="none" aria-label="Roles
	                 : No sort applied, activate to apply an ascending sort"><div class="tablesorter-wrapper"><div class="tablesorter-header-inner">Roles
	                 <i class="tablesorter-icon fa fa-sort"></i></div></div>
	                 </th>
	                 
	                <th data-column="4" 
	                	class="tablesorter-header tablesorter-headerUnSorted" 
	                	scope="col" 
	                	role="columnheader" 
	                	aria-disabled="false" 
	                	aria-controls="active-learners" 
	                	unselectable="on" style="-moz-user-select: none;" 
	                	aria-sort="none" 
	                	aria-label="Transcript : No sort applied, activate to apply an ascending sort">
		                	<div class="tablesorter-wrapper">
		                	<div class="tablesorter-header-inner">Transcript</div>
		                	</div>
	                </th>
	                <th data-column="5"
	                	class="tablesorter-header tablesorter-headerUnSorted"
	                	scope="col"
	                	role="columnheader"
	                	aria-disabled="false"
	                	aria-controls="active-learners"
	                	unselectable="on"
	                	style="-moz-user-select: none;"
	                	aria-sort="none"
	                	aria-label="Remove : No sort applied, activate to apply an ascending sort">
		                	<div class="tablesorter-wrapper">
		                	<div class="tablesorter-header-inner">Remove</div>
		                	</div>
	                </th>
	                
	                <?self::render_extra_fields($extra_fields);?>
			</tr>
	<?
		}
	
	/**
	 * Minimal JS and minimal HTML version here - most cases
	 * 
	 * @param BL_Header_Abstract $Header
	 * @param array $extra_fields
	 * @param boolean $has_checkbox
	 */
	static private function render_table_head_server(BL_Header_Abstract $Header,$extra_fields=[], $has_checkbox = false){
?>
		<tr class="tablesorter-headerRow">
		<?
		if($has_checkbox){
			self::render_checkbox_field();
		}
		
		foreach($Header->get_map_titles() as $title):
			$default_next_dir =  BL_Aeon::ORDER_BY_ASC;
			if($title == $Header->getSelectedOrder()[BL_Aeon::ORDER_BY]):
				if($Header->getSelectedOrder()[BL_Aeon::ORDER_BY_DIRECTION] == BL_Aeon::ORDER_BY_DESC):
					$class_th = 'tablesorter-headerDesc';
					$chevron  = 'tablesorter-icon glyphicon glyphicon-chevron-down text-primary';
				else:
					$class_th = 'tablesorter-headerAsc';
					$chevron  = 'tablesorter-icon glyphicon glyphicon-chevron-up text-primary';
					$default_next_dir = BL_Aeon::ORDER_BY_DESC;
				endif;
			else:
				$class_th = 'tablesorter-headerUnSorted';
				$chevron  = 'tablesorter-icon fa fa-sort';
			endif;
		
		?>
		
		
			<th class="tablesorter-header <?=$class_th?>" onclick=" $('#<?=BL_Aeon::ORDER_BY?>').val('<?=$title?>');
																	$('#<?=BL_Aeon::ORDER_BY_DIRECTION?>').val('<?=$default_next_dir?>');
																	$(this).closest('form').submit();">
				<div class="tablesorter-wrapper">
				<div class="tablesorter-header-inner"><?=$title?>
				 <i class="<?=$chevron?>"></i></div></div>
			</th>
		<?
		endforeach;
		self::render_extra_fields($extra_fields);
		?>
		</tr>
<?	
	}
	
	
	
	
	/**
	 * Render "dead" fields
	 * @param array $extra_fields
	 */
	static private function render_extra_fields(array $extra_fields){
		foreach($extra_fields as $field):
?>
			<th class="tablesorter-header <?= isset($field['class'])?$field['class']:''; ?>">
				<div class="tablesorter-wrapper">
				<div class="tablesorter-header-inner"><?= isset($field['name'])?$field['name']:$field; ?></div></div>
			</th>
<?
		endforeach;
	}
	
	/**
	 *  Render checkbox field
	 *  @param 
	 */
	static private function render_checkbox_field(){
?>
		<th class="width-micro tablesorter-header sorter-false tablesorter-headerUnSorted" data-column="0" tabindex="0" scope="col" role="columnheader" aria-disabled="true" unselectable="on" style="-moz-user-select: none;" aria-sort="none">
			<div style="position:relative;height:100%;width:100%" class="tablesorter-wrapper">
				<div class="tablesorter-header-inner">
					<div title="" class="tt" data-original-title="Select All/None"><input type="checkbox" id="select-all" title="Select All"></div>
					<i class="tablesorter-icon"></i>
				</div>
			</div>
		</th>
<?
	}
}