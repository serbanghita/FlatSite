<?php
class WebSite {

	private $tplVarsDelimiters = array('[', ']');
	private $siteDataDir       = 'data/';


	private $settings      = array();
	private $settingsFile  = 'data/WebSite.js';
	private $template;
	private $page;
	private $callbacks     = array();
	private $callbackTypes = array('beforeLoad', 'onLoad', 'afterLoad');


	public function __construct(){

		$this->setSettings();
		$this->_router();

		$this->register_callback('template', 'beforeLoad', 'callback_set_template_vars');
		$this->register_callback('template', 'beforeLoad', 'callback_set_template_view');
		$this->register_callback('template', 'onLoad', 'callback_process_template_vars');

		$this->register_callback('page', 'beforeLoad', 'callback_set_page_view');
		$this->register_callback('page', 'beforeLoad', 'callback_set_page_body');
		$this->register_callback('page', 'onLoad', 'callback_process_page_vars');
		$this->register_callback('page', 'onLoad', 'callback_process_page_callbacks');

	}

	private function readFile( $filename ){
		if( file_exists($filename) ){
			return file_get_contents($filename);
		} else {
			return false;
		}
	}

	public function setSettings(){
		$this->settings = json_decode( $this->readFile( $this->settingsFile ) );
		if(!$this->settings){
			throw new Exception(json_last_error());
		}
	}

	private function callback_posts(){

		$pages = $this->settings->pages;
		$postPreviewView = $this->readFile( 'data/postPreview.inc.php' );

		$html = '';

		foreach( $pages as $page ){
			if( $page->type != 'post' ){ continue; }
			$tmpView = $postPreviewView;
			$this->applyVarsToView( $tmpView, $page );
			$html .= $tmpView;
		}

		return $html;

	}

	private function _router(){
		$currentUrl = $_SERVER['REQUEST_URI'];
		//$currentUrl = substr($currentUrl, 1, strlen($currentUrl));

		// Check pages.
		foreach( $this->settings->pages as $page ){
			if( $currentUrl == $page->URL ){
				$this->page = $page;
				return true;
			}
		}
	}

	private function _headers(){}

	public function register_callback( $module, $callbackType, $callbackName, $callbackPriority = 0 ){
		$this->callbacks[$module][$callbackType][] = $callbackName;
	}

	public function process_callbacks( $module ){
		foreach( $this->callbacks[$module] as $callbackType => $callbacks){
			if(!empty($callbacks) && is_array($callbacks)){
				foreach($callbacks as $callback){
					if(method_exists($this, $callback)){
						$this->{$callback}();
					} else {
						throw new Exception('Callback method '.$callback.' for '.$module.' does not exist.');
					}
				}
			}
		}
	}

	private function callback_set_template_vars(){
		$this->template = $this->settings->template;
	}

	private function callback_set_template_view(){
		$this->template->view = $this->readFile( 'data/template.inc.php' );
	}

	private function callback_process_template_vars(){
		$this->applyVarsToView( $this->template->view, $this->template );
	}

	private function callback_set_page_view(){
		$this->page->view = $this->readFile( 'data/'.$this->page->type.'.inc.php' );
	}

	private function callback_set_page_body(){
		$this->page->body = $this->readFile( 'data/'.$this->page->type.'/' . $this->page->bodyFileName );
	}

	private function callback_process_page_vars(){
		$this->applyVarsToView( $this->page->view, $this->page );
	}

	private function callback_process_page_callbacks(){
		if( !empty($this->page->callbacks) ){
			$this->applyCallbacksToView( $this->page->view, $this->page->callbacks );
		}
	}

	private function applyVarsToView( &$view, $vars ){

		foreach($vars as $key => $value){
			if( is_array($value) ){
				continue;
			}
			if(is_string($value)){
				$view = str_replace($this->tplVarsDelimiters[0] . $key . $this->tplVarsDelimiters[1], $value, $view);
			}
		}

	}

	private function applyCallbacksToView( &$view, $callbacks ){

		foreach($callbacks as $key){
			if(method_exists($this, 'callback_'.$key)){
				$view = str_replace($this->tplVarsDelimiters[0] . $key . $this->tplVarsDelimiters[1], $this->{'callback_'.$key}(), $view);
			} else {
				throw Exception('Callback does not exist.');
			}
		}

	}

	public function template(){

		$this->process_callbacks('template');
		$this->process_callbacks('page');

		// @todo: refactor
		$templateHtml = str_replace('[websiteContent]', $this->page->view, $this->template->view);

		$this->_headers();

		exit($templateHtml);

	}

	public function run(){
		$this->template();
	}

}