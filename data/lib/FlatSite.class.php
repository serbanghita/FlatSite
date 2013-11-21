<?php
/**
 * /data/content/pages/ 			- all static HTML pages.
 * /data/content/settings/ 			- all dynamic temporary settings.
 * /data/content/css/ 				- all static CSS files.
 * /data/content/imgs/ 				- all static IMG files.
 * /data/content/js/ 				- all static JS files.
 * /data/content/tpl/				- all templates files.
 * 		/data/content/tpl/default/ 	- the default's template files.
 * /data/modules/					- all the custom modules.
 * 		/data/modules/example/		- a simple example of a module.
 * /data/lib/						- FlatSite.class.php
 * /data/tmp/						- temporary files (if needed)
 * /								- .htaccess
 * /								- index.php
 *
 */
class FlatSite {

	/**
	 * All the website settings.
	 * These settings are cached permanent.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * The current state of the request.
	 * @var array
	 */
	private $current = array();
	/**
	 * Minimum PHP version required
	 * in order to run the class without
	 * problems.
	 */
	const MINIMUM_PHP = '5.1.0';
	/**
	 * The current version of the class.
	 * Increment on each release.
	 */
	const VERSION     = '1.0.0';
	/**
	 * The path to the root of the application.
	 */
	const ROOT        = dirname(dirname(__DIR__));
	const BOOTSTRAP_PATHS   = 1;
	const BOOTSTRAP_CONFIG  = 2;
	const BOOTSTRAP_FILES   = 3;
	const BOOTSTRAP_MODULES = 4;
	const BOOTSTRAP_VIEW    = 5;
	/**
	 * Safety check to see if the application
	 * was installed. The value is updated
	 * upon succesful install.
	 */
	const INSTALLED   = false;

	/**
	 * The constructor can be initialized with a
	 * new set of application settings that will
	 * override the existing ones.
	 *
	 * @param [type] $settings [description]
	 */
	public function __construct( $settings = array() ){
		if( count($settings) > 0 ){
			$this->settings = array_merge($this->settings, $settings);
		}
	}
	public function set_settings( $settings );
	public function set_setting( $settingName, $settingValue );
	public function set_settings_from_file( $fileType = 'json' ){

		switch( $fileType ){
			case 'json':
			default:
				$this->settings = json_decode($this->read_file( ROOT . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . __CLASS__ . '.json' ));
			break;
		}

	}
	/**
	 * The central method of the application.
	 * Has multiple progressive stages.
	 *
	 * @param  [type] $phase [description]
	 * @return [type]        [description]
	 */
	public function bootstrap( $phase = null ) {

		if( isset($phase) ){
			$this->_bootstrap( $phase );
		} else {

			$phases = array(
				BOOTSTRAP_PATHS,
				BOOTSTRAP_CONFIG,
				BOOTSTRAP_FILES,
				BOOTSTRAP_MODULES,
				BOOTSTRAP_VIEW
			);

			foreach( $phases as $phase ){
				$this->_bootstrap( $phase );
			}

		}

	}
	/**
	 * Independently executes each phase of the bootstrap.
	 *
	 * @param  [type] $phase [description]
	 * @return [type]        [description]
	 */
	private function _bootstrap( $phase ){
		switch( $phase ){
			case BOOTSTRAP_PATHS:
				$this->init_paths();
			break;
			case BOOTSTRAP_CONFIG:
				$this->set_settings_from_file();
			break;
			case BOOTSTRAP_MODEL:
				$this->router();
				$this->set_page();
			break;
			case BOOTSTRAP_MODULES:
				$this->init_modules();
			break;
			case BOOTSTRAP_VIEW:
				$this->view();
			break;
		}
	}
	public function init_paths(){
		if( !file_exists( ROOT . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . __CLASS__ . '.json' ) ){
			exit('The configuration file was not found.');
		}
		if( !is_writable( ROOT . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'tmp' )) ){
			exit('The temporary folder is not writable.');
		}
	}
	/**
	 * Finds and sets the current url,
	 * if succesful sets the curret page data
	 * if not sets a 404 page data.
	 *
	 *
	 * @return [type] [description]
	 */
	public function router() {
		$host                          = $_SERVER['HTTP_HOST'];
		$uri                           = $_SERVER['REQUEST_URI'];
		$uri_relative                  = substr($currentUrl, 1, strlen($currentUrl));

		$this->current['host']         = $host;
		$this->current['uri']          = $uri;
		$this->current['uri_relative'] = $uri_relative;
	}
	/**
	 * Set the current page model.
	 */
	public function set_page(){
		// Check pages.
		foreach( $this->settings->pages as $page ){
			if( $this->current['uri_relative'] == $page->URL ){
				$this->page = $page;
				return true;
			}
		}

		// The current url was not found
		// among indexed pages.
		return false;

	}
	public function init_modules(){
		if( isset($this->settings->modules) && count($this->settings->modules) > 0 ){
			foreach( $this->settings->modules as $module ){
				$this->call_module($module, $hook, $args);
			}
		}
	}
	public function call_module($module, $hook, $args = array());
	public function view();
	/**
	 * Utility methods
	 */

	public function read_file( $filePath );
	public function install();
	public function index_site_data();
	public function save_site_data();

}