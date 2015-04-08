<?php
/**
 * Plugin Name: SO Remove ICL Promotion
 * Plugin URI:  http://wordpress.org/plugins/so-remove-icl-promotion/
 * Description: The SO Remove ICL Promotion plugin removes all promotion for ICanLocalize from the WPML plugin.
 * Version:     1.0
 * Author:      Piet Bos
 * Author URI:  http://so-wp.com/plugins/
 * License:     GPL v3
 * 
 * Copyright (c) 2015, SO WP Plugins
 * 
 * SO Remove ICL Promotion is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SO Remove ICL Promotion is distributed in the hope that it will be useful,
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
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Version check; any WP version under 4.0 is not supported (if only to "force" users to stay up to date)
 * 
 * adapted from example by Thomas Scholz (@toscho) http://wordpress.stackexchange.com/a/95183/2015, Version: 2013.03.31, Licence: MIT (http://opensource.org/licenses/MIT)
 *
 * @since 1.0
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

function soriclp_check_admin_notices()
{
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
         * - Adds the weekly cron to the existing schedules.
         * - Schedules a hook which will be executed by the WordPress actions core on the weekly interval.
         * - Attaches the auto salts functionality to the weekly hook.
         * - Registers activation hook which will add new security keys and salts to wp-config.php for the first time.
         * - Registers plugin deactivation hook which will remove everything from the weekly schedule.
         */
        function __construct() {

            $this->wp_config = $this->soriclp_get_wp_config_path();

            register_activation_hook( __FILE__, array( $this, 'wpconfig_add_idp' ) );
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
         * Add the definition that disables the ICanLocalize promotion to wp-config.php.
         * @source: //wordpress.org/plugins/wp-auto-salts/
         * @source: //stackoverflow.com/a/24035277/1381553
         * @return null Return null if wp-config.php doesn't exist or is not writeable.
         */
		function wpconfig_add_idp() {
            
            if ( false === $this->wp_config || ! $this->soriclp_can_write_to_wp_config() ) {
                return null;
            }

		    $config = file_get_contents ( $this->wp_config );
		    $config = preg_replace ( "/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php define('ICL_DONT_PROMOTE', true);", $config );
		    
		    file_put_contents( $this->wp_config, $config, LOCK_EX );
		    
		}
		
        /**
         * On deactivation, remove the definition that disables the ICanLocalize promotion from wp-config.php.
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
 * This function checks whether WPML is active (WPML needs to be active for this to have any use)
 * and gives a warning message with affiliate-link to WPML if it is not active.
 *
 * modified using http://wpengineer.com/1657/check-if-required-plugin-is-active/ and the _no_wpml_warning function
 *
 * @since 1.0
 */

$plugins = get_option( 'active_plugins' );

$required_plugin = 'sitepress-multilingual-cms/sitepress.php';

// multisite throws the error message by default, because the plugin is installed on the network site, therefore check for multisite @since 1.0
if ( ! in_array( $required_plugin , $plugins ) && ! is_multisite() ) {

	add_action( 'admin_notices', 'soriclp_no_wpml_warning' );

}

function soriclp_no_wpml_warning() {
    
    // display the warning message
    echo '<div class="message error"><p>';
    
    printf( __( 'The <strong>SO Remove ICL Promotion plugin</strong> only works if you have the <a href="%s">WPML</a> plugin installed.', 'so-remove-icl-promotion' ), 
        'https://wpml.org/?aid=140&affiliate_key=qGikbikRYa9M' );
    
    echo '</p></div>';
    
    // and deactivate the plugin @since 1.0
    deactivate_plugins( plugin_basename( __FILE__ ) );
}

