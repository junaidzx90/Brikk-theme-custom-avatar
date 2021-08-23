<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Avatarfix
 *
 * @wordpress-plugin
 * Plugin Name:       Brikk Theme Avatarfixer
 * Plugin URI:        https://www.fiverr.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Md Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       avatarfixer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

deefine('AVATAR_META', 'your_custom_avatar_user_url_meta_key');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AVATARFIX_VERSION', '1.0.0' );


load_plugin_textdomain(
	'avatarfix',
	false,
	dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
);


function find_post_id_from_path( $path ) {
    if ( preg_match( '/(-\d{1,4}x\d{1,4})\.(jpg|jpeg|png|gif)$/i', $path, $matches ) ) {
        $path = str_ireplace( $matches[1], '', $path );
    }

    if ( preg_match( '/uploads\/(\d{1,4}\/)?(\d{1,2}\/)?(.+)$/i', $path, $matches ) ) {
        unset( $matches[0] );
        $path = implode( '', $matches );
    }
    return attachment_url_to_postid( $path );
}


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'routiz/routiz.php' )) {
	add_action( 'init', function() {

		global $wpdb;
		
		$meta_key = AVATAR_META;

		$expectedAvatars = $wpdb->get_results("SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = '$meta_key'");
	
		if($expectedAvatars){
			foreach($expectedAvatars as $user){
				$user_id = $user->user_id;
				$avatar_url = $user->meta_value;

				$avatar = attachment_url_to_postid($avatar_url);
				$avatar = wp_json_encode( array('id' => $avatar));
				$avatar = '['.$avatar.']';
				
				if($avatar !== get_user_meta($user_id,'user_avatar', true)){
					update_user_meta($user_id,'user_avatar', $avatar);
				}
			}
		}
	});
}

