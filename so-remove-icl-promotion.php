<?php
/**
 * Plugin Name: SO Remove Translation Services
 * Plugin URI:  http://wordpress.org/plugins/so-remove-icl-promotion/
 * Description: The SO Remove Translation Services plugin removes the block of translation services from the Translation Management > Translators page of the WPML plugin.
 * Version:     2.0.0
 * Author:      Piet Bos
 * Author URI:  http://so-wp.com/plugins/
 * License:     GPL v3
 * 
 * Copyright (c) 2015, SO WP Plugins
 * 
 * SO Remove Translation Services is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SO Remove Translation Services is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have recieved a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses>.
 */

/**
 * Prevent direct access to files
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Version check; any WP version under 4.0 is not supported (if only to "force" users to stay up to date)
 * 
 * adapted from example by Thomas Scholz (@toscho) http://wordpress.stackexchange.com/a/95183/2015, Version: 2013.03.31, Licence: MIT (http://opensource.org/licenses/MIT)
 *
 * @since 1.0.0
 */

//Only do this when on the Plugins page.
if ( ! empty ( $GLOBALS['pagenow'] ) && 'plugins.php' === $GLOBALS['pagenow'] )
	
	/* soriclp_ prefix is derived from [so] [r]emove [icl] [p]romotion. */
	add_action( 'admin_notices', 'soriclp_check_admin_notices', 0 );

function soriclp_min_wp_version() {
	global $wp_version;
	$require_wp = '4.0';
	$update_url = get_admin_url( null, 'update-core.php' );

	$errors = array();

	if ( version_compare( $wp_version, $require_wp, '<' ) ) 

		$errors[] = "You have WordPress version $wp_version installed, but <b>this plugin requires at least WordPress $require_wp</b>. Please <a href='$update_url'>update your WordPress version</a>.";

	return $errors; 
}

function soriclp_check_admin_notices() {
	$errors = soriclp_min_wp_version();

	if ( empty ( $errors ) )
		return;

	// Suppress "Plugin activated" notice.
	unset( $_GET['activate'] );

	// this plugin's name
	$name = get_file_data( __FILE__, array ( 'Plugin Name' ), 'plugin' );

	printf( __( '<div class="error"><p>%1$s</p><p><i>%2$s</i> has been deactivated.</p></div>', 'so-remove-icl-promotion' ),
		join( '</p><p>', $errors ),
		$name[0]
	);
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

if ( ! class_exists( 'SO_Remove_ICL_Promotion' ) ) {
    
    class SO_Remove_ICL_Promotion {

        /**
         * Holds the absolute filesystem path to wp-config.php.
         * 
         * @var string
         */
        private $wp_config;

        /**
         * Class constructor.
         * 
         * - Determines the full filesystem path to wp-config.php.
         * - Registers activation hook.
         * - Registers plugin deactivation hook.
         *
         * @since 1.0.0
         * @modified 2.0.0
         */
        function __construct() {

            $this->wp_config = $this->soriclp_get_wp_config_path();

            register_activation_hook( __FILE__, array( $this, 'wpconfig_remove_idp' ) ); // remove the definition of activation too as it has become redundant
            register_deactivation_hook( __FILE__, array( $this, 'wpconfig_remove_idp' ) );

        }

        /**
         * Get the absolute filesystem path to wp-config.php.
         * 
         * @return string|boolean Full filesystem path if determined, false otherwise.
         */
        function soriclp_get_wp_config_path() {

            $paths = array(
                ABSPATH . 'wp-config.php',
                dirname( ABSPATH ) . '/wp-config.php'
            );

            foreach ( $paths as $path ) {
                if ( file_exists( $path ) ) {
                    return $path;
                }
            }

            return false;

        }

        /**
         * Check if it's possible to write to wp-config.php.
         * 
         * @return boolean True if the file is writeable, otherwise false.
         */
        function soriclp_can_write_to_wp_config() {

            if ( is_writable( $this->wp_config ) ) {
                return true;
            }

            return false;

        }

        /**
         * On de/activation, remove the ICL_DONT_PROMOTE definition from wp-config.php.
         */
		function wpconfig_remove_idp() {
		    
		    $config = file_get_contents ( $this->wp_config );
		    $config = preg_replace ("/( ?)(define)( ?)(\()( ?)(['\"])ICL_DONT_PROMOTE(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config);
		    
		    file_put_contents ( $this->wp_config, $config, LOCK_EX );
		}
		
    }

    new SO_Remove_ICL_Promotion();
}



/**
 * This function checks whether the WPML Translation Management Addon is active (needs to be active for this to have any use)
 * and gives a warning message with affiliate-link to WPML if it is not active.
 *
 * modified using http://wpengineer.com/1657/check-if-required-plugin-is-active/ and the _no_wpml_warning function
 *
 * @since 1.0.0
 * @modified 2.0.0
 */

$plugins = get_option( 'active_plugins' );

$required_plugin = 'wpml-translation-management/plugin.php';

// multisite throws the error message by default, because the plugin is installed on the network site, therefore check for multisite @since 1.0.0
if ( ! in_array( $required_plugin , $plugins ) && ! is_multisite() ) {

	add_action( 'admin_notices', 'soriclp_no_wpml_warning' );

}

/**
 * Adjusted warning message to new check and new name
 *
 * @modified 2.0.0
 */
function soriclp_no_wpml_warning() {
    
    // display the warning message
    echo '<div class="message error"><p>';
    
    printf( __( 'The <strong>SO Remove Translation Services plugin</strong> only works if you have the <a href="%s">Translation Management addon of WPML</a> installed. As you have not you can either remove this plugin (it is useless without the addon) or install the addon.', 'so-remove-icl-promotion' ), 
        'https://wpml.org/?aid=140&affiliate_key=qGikbikRYa9M' );
    
    echo '</p></div>';
    
    // and deactivate the plugin @since 1.0.0
    deactivate_plugins( plugin_basename( __FILE__ ) );
}


add_action( 'admin_head', 'so_riclp_style' );

function so_riclp_style() {
	echo '<style type="text/css">
	.icl_tm_wrap .icl-admin-information, .icl_tm_wrap > a, .icl_tm_wrap #translation_services > h3, .icl_tm_wrap .js-available-services {display:none;}
	</style>';
}

