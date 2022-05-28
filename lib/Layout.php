<?php
/**
 * Represents a layout object
 * @author itaymoav
 */
abstract class Layout{
	static protected $injected_data = [];
	/**
	 * Inject data into a layout through this method
	 * 
	 * @param string $label
	 * @param string $content
	 * @return string:
	 */
	static public function inject($label,$content=false){
		if($content) self::$injected_data[$label] = $content;
		elseif(isset(self::$injected_data[$label])) return self::$injected_data[$label];
		else return '';		
	}
	
	static public function injectPluginJS($content=false){
		if($content){
			if(is_array($content)){
				self::$injected_data[self::PLUGINS_JS]	= array_merge(self::$injected_data[self::PLUGINS_JS]??[],$content);
			}else{
				self::$injected_data[self::PLUGINS_JS][]	= $content;
			}
			self::$injected_data[self::PLUGINS_JS]	= array_unique(self::$injected_data[self::PLUGINS_JS]);
		}elseif(isset(self::$injected_data[self::PLUGINS_JS])){
			return self::$injected_data[self::PLUGINS_JS];
		}else{
			return '';
		}
	}
	
	const	TITLE				= 'title',
			SUBMENU				= 'submenu',
 			SUBMENUTABS			= 'submenutabs',
 			HEADBUTTONS			= 'headbuttons',	//buttons after the sub title
 			PAGE_SPECIFIC_JS 	= 'pagespecificjs',
 			PLUGINS_JS			= 'pluginsjs',
 			SNIPPET_SPECIFIC_JS	= 'snippetspecificjs',
 			LAST_HEAD           = 'lasthead',  //just before head tag close
 			JSSCRIPTS           = 'jsscripts', // External JS scripts which depends on jQuery
 			
 			REL_URL_PAGE_SPECIFIC_JS 	= '/sitel/page_specific'
	;
	
	/**
	 * @var Request_Default
	 */
	protected $request = null;
	
	public function __construct(Request_Default $r){
		$this->request = $r;
		dbgn("LAYOUT: " . get_class($this));
	}
		
	public function renderBefore(){

	}
	
	public function renderAfter(){

	}
}