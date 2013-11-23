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
	private $ROOT;
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

	const BOOTSTRAP_PATHS   = 1;
	const BOOTSTRAP_CONFIG  = 2;
	const BOOTSTRAP_MODEL	= 3;
	const BOOTSTRAP_FILES   = 4;
	const BOOTSTRAP_MODULES = 5;
	const BOOTSTRAP_VIEW    = 6;
	/**
	 * Safety check to see if the application
	 * was installed. The value is updated
	 * upon succesful install.
	 */
	const INSTALLED   = false;

	private $loaded_modules = array('page', 'template');
	private $callback_types = array('before', 'after');

	/**
	 * The constructor can be initialized with a
	 * new set of application settings that will
	 * override the existing ones.
	 *
	 * @param [type] $settings [description]
	 */
	public function __construct(){
		$this->ROOT = dirname(dirname(__DIR__));
	}
	public function set_settings( $settings ){}
	public function set_setting( $settingName, $settingValue ){}
	public function set_settings_from_file( $fileType = 'json' ){

		switch( $fileType ){
			case 'json':
			default:
				$this->settings = json_decode($this->read_file( $this->ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . __CLASS__ . '.json' ));
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
				self::BOOTSTRAP_PATHS,
				self::BOOTSTRAP_CONFIG,
				self::BOOTSTRAP_MODEL,
				self::BOOTSTRAP_FILES,
				self::BOOTSTRAP_MODULES,
				self::BOOTSTRAP_VIEW
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
			case self::BOOTSTRAP_PATHS:
				$this->init_paths();
			break;
			case self::BOOTSTRAP_CONFIG:
				$this->set_settings_from_file();
			break;
			case self::BOOTSTRAP_MODEL:
				$this->router();
				$this->set_page();
			break;
			case self::BOOTSTRAP_MODULES:
				$this->init_modules();
			break;
			case self::BOOTSTRAP_VIEW:
				$this->view();
			break;
		}
	}
	public function init_paths() {
		if( !file_exists( $this->ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . __CLASS__ . '.json' ) ){
			exit('The configuration file was not found.');
		}
		if( !is_writable( $this->ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tmp' ) ){
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
		$uri_relative                  = substr($uri, 1, strlen($uri));

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
		/*
		if( isset($this->settings->modules) && count($this->settings->modules) > 0 ){
			foreach( $this->settings->modules as $module ){
				$this->call_module($module, $hook, $args);
			}
		}
		*/
		$this->call_callback();
	}
	public function call_callback(){

		// Read all potential modules from /modules/*
		$modules_dir = $this->ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
		$Directory = new RecursiveDirectoryIterator( $modules_dir );
		$Iterator = new RecursiveIteratorIterator( $Directory );
		$Regex = new RegexIterator($Iterator, '/([a-z_]+)\.module\.php$/i', RecursiveRegexIterator::GET_MATCH);
		foreach($Regex as $moduleFilePath => $match){
			// Include the module.
			include_once $moduleFilePath;
			// Add module to the loaded modules array.
			$this->loaded_modules[] = $match[1];
			// Init the module's functions.
			foreach( $this->loaded_modules as $_module ) {
				foreach( $this->callback_types as $_callback_type ){
					if( function_exists($match[1] . '_' . $_module . '_' . $_callback_type) ){
						call_user_func_array($match[1] . '_' . $_module . '_' . $_callback_type, array($this));
					}
				}
			}

		}



	}

	public function view(){
		print_r($this->page);
	}
	/**
	 * Utility methods
	 */

	public function read_file( $filePath ){

		if( file_exists($filePath) ){
			return file_get_contents($filePath);
		} else {
			return false;
		}

	}
	public function install(){}
	public function index_site_data(){}
	public function save_site_data(){}

}