<?php
/**
 * Decorators for the various filter types.
 * Also some high level/shortcuts for entire filter
 * structures
 * 
 * @author itaymoav
 */
abstract class ViewSnipets_Data_Grid_Filter{
    
    static public function hidden($filter_elm_name,BL_Filter_Abstract $Filter){
        ?>	<input type="hidden" id="<?=$filter_elm_name?>" name="<?=$filter_elm_name?>" value="<?=$Filter->getParam($filter_elm_name)?>" /><?
    }
    
    /**
     * 
     * @param unknown $filter_elm_name
     * @param BL_Filter_Abstract $Filter
     * @param string $is_server
     */
	static public function simple_search($filter_elm_name,BL_Filter_Abstract $Filter,$is_server=true){//for now we always go to server, as I do not know if it is not serverr due to filter
?>	<input type="search" class="form-control" id="<?=$filter_elm_name?>" name="<?=$filter_elm_name?>" value="<?=$Filter->getParam($filter_elm_name)?>" /><?
	}
	
	/**
	 * 
	 * @param unknown $filter_elm_name
	 * @param BL_Filter_Abstract $Filter
	 * @param array $options
	 * @param string $is_server
	 * @param string $non_selected_text
	 */
	static public function simple_dropdown($filter_elm_name, BL_Filter_Abstract $Filter,array $options,callable $options_formatter = null, $none_selected_text = 'All'){
	    if(!$options_formatter){
	        $options_formatter = function($one_option){return $one_option; };
	    }
	     
?>
    <select class="tablesorter-filter form-control" name="<?=$filter_elm_name?>" id="<?=$filter_elm_name?>" onChange="$(this).closest('form').submit()">
        <option value=""><?= $none_selected_text?></option>
        <?foreach($options as $value=>$text):?>
        <option value="<?=$value?>" <?if($Filter->getParam($filter_elm_name) == $value){echo 'selected="selected"'; }?>><?=$options_formatter($text)?></option>
        <?endforeach;?>
    </select><?
	}
	
	/**
	 * List of options is pre populated, not loaded from server
	 * 
	 * @param string $filter_elm_name
	 * @param BL_Filter_Abstract $Filter
	 * @param array $options
	 */
	static public function select2_client($filter_elm_name, BL_Filter_Abstract $Filter,array $options,callable $options_formatter = null){
	    Response_View::javascript('$("#' . $filter_elm_name . '").select2();');
        return self::simple_dropdown($filter_elm_name, $Filter, $options,$options_formatter);
	}
	
	/**
	 *  List of options is not prepopulated, loaded from the server
	 *  
	 *  @param string $filter_elm_name
	 *  @param BL_Filter_Abstract $Filter
	 *  @param string $url
	 *  @param string $none_selected_text
	 *  @param 
	 */
	static public function select2_server($filter_elm_name, BL_Filter_Abstract $Filter, $url, $none_selected_text='Select', array $initial_selection = array()){
		
		$initial = !empty($initial_selection)?
		"initSelection:function(element,callback){
		var data = {'id':'{$initial_selection['id']}','text':'{$initial_selection['text']}'};
		callback(data);
		},":
		"";
		
		Response_View::javascript("
			var options = {
	        {$initial}
			placeholder:'" . $none_selected_text . "',
			allowClear:true,
			ajax: {
				url: window.baseUrl + '" . $url . "',
				dataType: 'json',
				type: 'GET',
				data: function (term, page) {
					return {
						q: term,
						page: page,
						page_limit: 50
					};
				},//data
				
				results: function (data, page) {
					var more = (page*50)<data.data.total;//paging
					return {results: data.data.found, more: more};
				}//results
			}
		};

		//Making it live, and attaching events. The event is responsible to remove validator error message and revalidate field when it is changed	
		$('#".$filter_elm_name."').select2(options);
			");
		?>
		<input type="hidden" name="<?= $filter_elm_name?>" id="<?=$filter_elm_name?>" class="form-control"  value="<?= $Filter->getParam($filter_elm_name)?>" onChange="$(this).closest('form').submit()" />
		<?
	}
}