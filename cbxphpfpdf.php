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
 * Version:           1.0.1
 * Author:            Codeboxr
 * Author URI:        https://github.com/PHPOffice/PhpDomPDF
 * License:           MIT
 * License URI:       https://github.com/codeboxrcodehub/cbxphpfpdf/blob/master/LICENSE.txt
 * Text Domain:       cbxphpfpdf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

defined('CBXPHPFPDF_PLUGIN_NAME') or define('CBXPHPFPDF_PLUGIN_NAME', 'cbxphpfpdf');
defined('CBXPHPFPDF_PLUGIN_VERSION') or define('CBXPHPFPDF_PLUGIN_VERSION', '1.0.');
defined('CBXPHPFPDF_BASE_NAME') or define('CBXPHPFPDF_BASE_NAME', plugin_basename(__FILE__));
defined('CBXPHPFPDF_ROOT_PATH') or define('CBXPHPFPDF_ROOT_PATH', plugin_dir_path(__FILE__));
defined('CBXPHPFPDF_ROOT_URL') or define('CBXPHPFPDF_ROOT_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, array('CBXPhpFpdf', 'activation'));

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
        add_action( 'admin_notices', [ $this, 'activation_error_display' ] );
    }

    /**
     * Activation hook
     */
    public static function activation()
    {
        $errors = [];

        if ( ! self::php_version_check() ) {
            $errors[] = esc_html__('CBX PhpFPDF Library plugin requires PHP version 7.1 or newer', 'cbxphpfpdf');
        }

        if ( ! self::extension_check( [ 'zip', 'xml', 'gd', 'mbstring', 'dom' ] ) ) {
            $errors[] = esc_html__( 'CBX PhpFPDF Library plugin requires PHP extensions: Zip, XML, and GD2.', 'cbxphpfpdf' );
        }

        if ( sizeof( $errors ) > 0 ) {
            update_option( 'cbxphpfpdf_activation_error', $errors );
            //deactivate_plugins(plugin_basename(__FILE__));
            //wp_die('Plugin not activated due to dependency not fulfilled.');
            //die();
        }

    }//end method activation

    /**
     * Show error
     *
     * @return void
     */
    public function activation_error_display() {
        // Only display on specific admin pages (e.g., plugins page)
        $screen = get_current_screen();
        if ( $screen && $screen->id === 'plugins' ) {
            $errors = get_option( 'cbxphpfpdf_activation_error' );
            if ( $errors ) {
                if ( is_array( $errors ) && sizeof( $errors ) > 0 ) {
                    foreach ( $errors as $error ) {
                        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
                    }
                }

                delete_option( 'cbxphpfpdf_activation_error' );
                //deactivate_plugins('cbxphpfpdf/cbxphpfpdf.php');
            }
        }
    }//end method activation_error_display

    /**
     * PHP version compatibility check
     *
     * @return bool
     */
    private static function php_version_check(){
        return version_compare( PHP_VERSION, '7.4', '>=' );
    }//end method php_version_check

    /**
     * Check if required PHP extensions are enabled
     *
     * @param array $extensions
     *
     * @return bool
     */
    private static function extension_check( $extensions ) {
        foreach ( $extensions as $extension ) {
            if ( ! extension_loaded( $extension ) ) {
                return false;
            }
        }

        return true;
    }//end method extension_check

    /**
     * Is the environment ready for the phpfpdf package
     *
     * @return bool
     */
    public static function environment_ready() {
        return self::php_version_check() && self::extension_check(  [ 'zip', 'xml', 'gd', 'mbstring', 'dom' ]);
    }//end method environment_ready

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

/**
 * Initialize the plugin
 */
function cbxphpfpdf_load_plugin(){
    new CBXPhpFpdf();
}

add_action('plugins_loaded', 'cbxphpfpdf_load_plugin', 5);

if(!function_exists('cbxphpfpdf_loadable')){
    /**
     * Check if the enviroment ready for phpfpdf library
     *
     * @return bool
     */
    function cbxphpfpdf_loadable(){
        return CBXPhpFpdf::environment_ready();
    }//end function cbxphpfpdf_loadable
}

if(!function_exists('cbxphpfpdf_load')){
    /**
     * If the enviroment is ready then load the autoloaded
     *
     * @return void
     */
    function cbxphpfpdf_load(){
        if(CBXPhpFpdf::environment_ready()){
            require_once CBXPHPFPDF_ROOT_PATH . "lib/vendor/autoload.php";
        }
    }//end function cbxphpfpdf_load
}