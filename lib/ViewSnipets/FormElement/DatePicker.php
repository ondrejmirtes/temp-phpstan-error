<?php
/**
 * Form element for date picker 
 * @author holly
 */
class ViewSnipets_FormElement_DatePicker {
    /**
     * Render date picker
     * 
     * Params:
     * date_picker_id
     * input_id
     * start_date (optional)
     * end_date (optional)
     * orientation (optional)
     * format (optional)
     * enableOnReadonly (optional)
     * defaultViewDate (optional)
     * 
     * @param array $params
     */
    public static function render(array $params) {
        static::loadJS($params);
        static::loadLayout();
        return static::loadHTML($params);
    }
    
    public static function loadLayout() {
        Layout::injectPluginJS(['/plugins/bootstrap-datepicker.min.js']);
    }
    
    /**
     * Load JS
     * @param array $params
     * @return string $js
     */
    public static function loadJS(array $params) {
        // TODO CHANGE THIS TO DEFAULTS
        $start_date         = $params['startDate'] ?? null;
        $end_date           = $params['endDate'] ?? null;
        $orientation        = $params['orientation'] ?? 'bottom';
        $format             = $params['format'] ?? 'mm/dd/yyyy';
        $enableOnReadonly   = $params['enableOnReadonly'] ?? 'false';
        $defaultViewDate    = $params['defaultViewDate'] ?? 'today';
        $autoclose          = $params['autoclose'] ?? 'true';
        
        $js = "
            $('#{$params['date_picker_id']}').datepicker({
                'startDate'           : '{$start_date}',
                'endDate'             : '{$end_date}',
                'orientation'         : '{$orientation}',
                'format'              : '{$format}',
                'enableOnReadonly'    : '{$enableOnReadonly}',
                'defaultViewDate'     : '{$defaultViewDate}',
                'autoclose'           : '{$autoclose}'
            });
        ";
        
        Response_View::javascript($js);
        return $js;
    }

    /**
     * Load html
     * 
     * Following parameters needed:
     * input_placeholder
     * 
     * @param array $params
     * @return string $html
     */
    public static function loadHTML(array $params) {
        $status         = '';
        if(isset($params['input_status'])){
            $status     = $params['input_status']=='disabled'?'disabled="disabled"':'';
        }
        
        $value          = $params['input_value'] ?? '';
        $placeholder    = $params['input_placeholder'] ?? '';
        
        $html = 
<<<HTML
	<div class="input-group date" data-provide="datepicker" style="width:360px" id="{$params['date_picker_id']}">
	    <input type="text" name="{$params['input_id']}" id="{$params['input_id']}" value="{$value}" placeholder="{$placeholder}" {$status} class="form-control width-jumbo">
	    <div class="input-group-addon">
	        <span class="glyphicon glyphicon-th"></span>
	    </div>
	</div>
HTML;
        
        return $html;
    }
}

