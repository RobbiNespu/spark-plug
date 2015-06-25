<?php namespace Rougin\SparkPlug;

/**
 * Instance Class
 *
 * @package SparkPlug
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Instance
{

	/**
	 * Set some definitions and load required classes
	 */
	public function __construct()
	{
		/**
		 * Define the APPPATH, VENDOR, and BASEPATH paths
		 */
		
		if ( ! defined('VENDOR')) {
			define('VENDOR',   realpath('vendor') . '/');
		}

		define('APPPATH',  realpath('application') . '/');
		define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
		define('VIEWPATH', APPPATH . '/views/');

		/**
		 * Search for the directory and defined it as the BASEPATH
		 */

		$directory = new \RecursiveDirectoryIterator(getcwd(), \FilesystemIterator::SKIP_DOTS);
		$slash = (strpos(PHP_OS, 'WIN') !== FALSE) ? '\\' : '/';

		foreach (new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST) as $path) {
			if (strpos($path->__toString(), 'core' . $slash . 'CodeIgniter.php') !== FALSE) {
				$basepath = str_replace('core' . $slash . 'CodeIgniter.php', '', $path->__toString());
				define('BASEPATH', $basepath);

				break;
			}
		}

		/**
		 * Load the Common and Base Controller class
		 */

		require BASEPATH . 'core/Common.php';
		require BASEPATH . 'core/Controller.php';

		/**
		 * Load the framework constants
		 */

		if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php')) {
			require APPPATH . 'config/' . ENVIRONMENT . '/constants.php';
		} else {
			require APPPATH . 'config/constants.php';
		}

		/**
		 * Important charset-related stuff
		 */

		$charset = strtoupper(config_item('charset'));
		ini_set('default_charset', $charset);

		if (extension_loaded('mbstring')) {
			define('MB_ENABLED', TRUE);
			// mbstring.internal_encoding is deprecated starting with PHP 5.6
			// and it's usage triggers E_DEPRECATED messages.
			@ini_set('mbstring.internal_encoding', $charset);
			// This is required for mb_convert_encoding() to strip invalid characters.
			// That's utilized by CI_Utf8, but it's also done for consistency with iconv.
			mb_substitute_character('none');
		} else {
			define('MB_ENABLED', FALSE);
		}

		// There's an ICONV_IMPL constant, but the PHP manual says that using
		// iconv's predefined constants is "strongly discouraged".
		if (extension_loaded('iconv')) {
			define('ICONV_ENABLED', TRUE);
			// iconv.internal_encoding is deprecated starting with PHP 5.6
			// and it's usage triggers E_DEPRECATED messages.
			@ini_set('iconv.internal_encoding', $charset);
		} else {
			define('ICONV_ENABLED', FALSE);
		}

		if (is_php('5.6')) {
			ini_set('php.internal_encoding', $charset);
		}

		/**
		 * Set global configurations
		 */

		$GLOBALS['CFG'] = & load_class('Config', 'core');
		$GLOBALS['UNI'] = & load_class('Utf8', 'core');
		$GLOBALS['SEC'] = & load_class('Security', 'core');

		/**
		 * Load the CodeIgniter's core classes
		 */

		load_class('Loader', 'core');
		load_class('Router', 'core');
		load_class('Input', 'core');
		load_class('Lang', 'core');
	}

	/**
	 * Get the instance of CodeIgniter
	 * 
	 * @return CodeIgniter
	 */
	public function get()
	{
		require 'GetInstance.php';

		return new \CI_Controller();
	}

}