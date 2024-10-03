<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://codeboxr.com
 * @since             1.0.0
 * @package           cbxphpfpdf
 *
 * @wordpress-plugin
 * Plugin Name:       CBX PHPFPDF Library
 * Plugin URI:        https://github.com/codeboxrcodehub/cbxphpfpdf
 * Description:       fpdf library as WordPress plugin based on https://github.com/fawno/FPDF
 * Version:           1.0.0
 * Author:            Codeboxr
 * Author URI:        https://github.com/PHPOffice/PhpDomPDF
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxphpfpdf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
use Cbx\Phpfpdf\Hooks;

defined('CBXPHPFPDF_PLUGIN_NAME') or define('CBXPHPFPDF_PLUGIN_NAME', 'cbxphpfpdf');
defined('CBXPHPFPDF_PLUGIN_VERSION') or define('CBXPHPFPDF_PLUGIN_VERSION', '1.0.0');
defined('CBXPHPFPDF_BASE_NAME') or define('CBXPHPFPDF_BASE_NAME', plugin_basename(__FILE__));
defined('CBXPHPFPDF_ROOT_PATH') or define('CBXPHPFPDF_ROOT_PATH', plugin_dir_path(__FILE__));
defined('CBXPHPFPDF_ROOT_URL') or define('CBXPHPFPDF_ROOT_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, array('CBXPhpFpdf', 'activation'));
require_once CBXPHPFPDF_ROOT_PATH . "lib/vendor/autoload.php";

/**
 * Class CBXPhpFpdf
 */
class CBXPhpFpdf
{
	function __construct()
	{
		//load text domain
		load_plugin_textdomain('cbxphpfpdf', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
		new Hooks();
	}

	/**
	 * Activation hook
	 */
	public static function activation()
	{
		/*$requirements = array(
				  'PHP 7.1.0' => version_compare(PHP_VERSION, '7.1.0', '>='),
				  'PHP extension XML' => extension_loaded('xml'),
				  'PHP extension xmlwriter' => extension_loaded('xmlwriter'),
				  'PHP extension mbstring' => extension_loaded('mbstring'),
				  'PHP extension ZipArchive' => extension_loaded('zip'),
				  'PHP extension GD (optional)' => extension_loaded('gd'),
				  'PHP extension dom (optional)' => extension_loaded('dom'),
			  );*/

		if (!CBXPhpFpdf::php_version_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP version 7.1 or newer', 'cbxphpfpdf');
			die($error_message);
		}

		if (!CBXPhpFpdf::php_zip_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP php_zip extension installed and enabled', 'cbxphpfpdf');
			die($error_message);
		}

		if (!CBXPhpFpdf::php_xml_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP php_xml extension installed and enabled', 'cbxphpfpdf');
			die($error_message);
		}

		if (!CBXPhpFpdf::php_gd_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP php_gd2 extension installed and enabled', 'cbxphpfpdf');
			die($error_message);
		}

		if (!CBXPhpFpdf::php_mbstring_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP php_MBString extension installed and enabled', 'cbxphpfpdf');
			die($error_message);
		}

		if (!CBXPhpFpdf::php_dom_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = esc_html__('This plugin requires PHP DOM extension installed and enabled', 'cbxphpfpdf');
			die($error_message);
		}

	}//end method activation

	/**
	 * PHP version compatibility check
	 *
	 * @return bool
	 */
	public static function php_version_check()
	{
		if (version_compare(PHP_VERSION, '7.1.0', '<')) {
			return false;
		}

		return true;
	}//end method php_version_check

	/**
	 * php_zip enabled check
	 *
	 * @return bool
	 */
	public static function php_zip_enabled_check()
	{
		if (extension_loaded('zip')) {
			return true;
		}
		return false;
	}//end method php_zip_enabled_check

	/**
	 * php_xml enabled check
	 *
	 * @return bool
	 */
	public static function php_xml_enabled_check()
	{
		if (extension_loaded('xml')) {
			return true;
		}
		return false;
	}//end method php_xml_enabled_check

	/**
	 * php_gd2 enabled check
	 *
	 * @return bool
	 */
	public static function php_gd_enabled_check()
	{
		if (extension_loaded('gd')) {
			return true;
		}
		return false;
	}//end method php_gd_enabled_check

	/**
	 * php_mbstring enabled check
	 *
	 * @return bool
	 */
	public static function php_mbstring_enabled_check()
	{
		if (extension_loaded('mbstring')) {
			return true;
		}
		return false;
	}//end method php_mbstring_enabled_check

	/**
	 * php_dom enabled check
	 *
	 * @return bool
	 */
	public static function php_dom_enabled_check()
	{
		if (extension_loaded('dom')) {
			return true;
		}
		return false;
	}//end method php_dom_enabled_check

	/**
	 * Plugin support and doc page url
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function plugin_row_meta($links, $file)
	{
		if (strpos($file, 'cbxphpfpdf.php') !== false) {
			$new_links = array(
				'support' => '<a href="https://github.com/codeboxrcodehub/cbxphpfpdf" target="_blank">' . esc_html__('Support', 'cbxphpfpdf') . '</a>',
				'doc' => '<a href="https://github.com/dompdf/dompdf" target="_blank">' . esc_html__('PHP Dompdf Doc', 'cbxphpfpdf') . '</a>'
			);

			$links = array_merge($links, $new_links);
		}

		return $links;
	}

}//end method CBXPhpFpdf


function cbxphpfpdf_load_plugin()
{
	new CBXPhpFpdf();
}

add_action('plugins_loaded', 'cbxphpfpdf_load_plugin', 5);