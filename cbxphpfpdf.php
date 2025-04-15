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
 * Version:           1.0.3
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
defined('CBXPHPFPDF_PLUGIN_VERSION') or define('CBXPHPFPDF_PLUGIN_VERSION', '1.0.3');
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
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
        add_action( 'admin_notices', [ $this, 'activation_error_display' ] );

        add_action( 'init', [ $this, 'load_plugin_textdomain' ]);

        add_filter( 'pre_set_site_transient_update_plugins', [
			$this,
			'pre_set_site_transient_update_plugins'
		] );
		add_filter( 'plugins_api', [ $this, 'plugin_info' ], 10, 3 );
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

    /**
	 * Load textdomain
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'cbxphpfpdf', false, CBXPHPFPDF_ROOT_PATH . 'languages/' );
	}//end method load_plugin_textdomain

    /**
	 * Custom update checker implemented
	 *
	 * @param $transient
	 *
	 * @return mixed
	 */
	public function pre_set_site_transient_update_plugins( $transient ) {
		// Ensure the transient is set
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$plugin_slug = 'cbxphpfpdf';
		$plugin_file = 'cbxphpfpdf/cbxphpfpdf.php';

		if ( isset( $transient->response[ $plugin_file ] ) ) {
			return $transient;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$url = 'https://comforthrm.com/product_updates.json'; // Replace with your remote JSON file URL
		
		// Fetch the remote JSON file
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return $transient;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );// Set true for associative array, false for object


		if ( ! isset( $data['cbxphpfpdf'] ) ) {
			return $transient;
		}

		$remote_data = $data['cbxphpfpdf'];

		$plugin_url  = isset( $remote_data['url'] ) ? $remote_data['url'] : '';
		$package_url = isset( $remote_data['api_url'] ) ? $remote_data['api_url'] : false;

		$remote_version = isset( $remote_data['new_version'] ) ? sanitize_text_field( $remote_data['new_version'] ) : '';

		if ( $remote_version != '' && version_compare( $remote_version, $transient->checked[ $plugin_file ], '>' ) ) {
			$transient->response[ $plugin_file ] = (object) [
				'slug'        => $plugin_slug,
				'new_version' => $remote_version,
				'url'         => $plugin_url,
				'package'     => $package_url, // Link to the new version
			];
		}

		return $transient;
	}//end method pre_set_site_transient_update_plugins

	public function plugin_info( $res, $action, $args ) {
		// Plugin slug
		$plugin_slug = 'cbxphpfpdf';                                      // Replace with your plugin slug

		// Ensure we're checking the correct plugin
		if ( $action !== 'plugin_information' || $args->slug !== $plugin_slug ) {
			return $res;
		}

		// Fetch detailed plugin information
		$response = wp_remote_get( 'https://comforthrm.com/product_updates.json' ); // Replace with your API URL

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return $res;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! isset( $data[ $plugin_slug ] ) ) {
			return $res;
		}

		$remote_data = $data[ $plugin_slug ];		
		$package_url = isset( $remote_data['api_url'] ) ? $remote_data['api_url'] : false;

		// Build the plugin info response
		return (object) [
			'name'          => isset( $remote_data['name'] ) ? sanitize_text_field( $remote_data['name'] ) : 'CBX PHPFPDF Library',
			'slug'          => $plugin_slug,
			'version'       => isset( $remote_data['new_version'] ) ? sanitize_text_field( $remote_data['new_version'] ) : '',
			'author'        => isset( $remote_data['author'] ) ? sanitize_text_field( $remote_data['author'] ) : '',
			'homepage'      => isset( $remote_data['url'] ) ? $remote_data['url'] : '',
			'requires'      => isset( $remote_data['requires'] ) ? sanitize_text_field( $remote_data['requires'] ) : '',
			'tested'        => isset( $remote_data['tested'] ) ? sanitize_text_field( $remote_data['tested'] ) : '',
			'download_link' => $package_url,
			'sections'      => [
				'description' => isset( $remote_data['description'] ) ? wp_kses_post( $remote_data['description'] ) : '',
				'changelog'   => isset( $remote_data['changelog'] ) ? wp_kses_post( $remote_data['changelog'] ) : '',
			],
		];

	}//end method plugin_info

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