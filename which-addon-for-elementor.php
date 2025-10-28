<?php
/**
 * Plugin Name: Which Elementor Addon
 * Plugin URI: https://obiplabon.com
 * Description: Find the unnecessary, repeating, replaceable Elementor add-ons or widgets easily with this simple and easy to use super lightweight plugin! This plugin simply adds a tooltip which shows the widget name along with the plugin name, amazing!
 * Version: 1.3.0
 * Author: obiPlabon
 * Author URI: https://obiplabon.com
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: which-elementor-addon
 * Domain Path: /languages/
 * Requires Plugins: elementor
 *
 * @package Which_Elementor_Addon
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019 obiPlabon <obiplabon@gmail.com>
*/

namespace obiPlabon;

// just like do or die
defined( 'ABSPATH' ) || die();

use Elementor\Tools;

/**
 * Class Which_Elementor_Addon
 *
 * @package obiPlabon
 */
class Which_Elementor_Addon {

	/**
	 * Plugin version
	 */
	const VERSION = '1.3.0';

	/**
	 * Required minimum php version
	 */
	const REQUIRED_PHP_VERSION = '7.0';

	/**
	 * Plugin slug
	 */
	const SLUG = 'which-elementor-addon';

	/**
	 * DB field prefix
	 */
	const DB_PREFIX = 'which_elementor_addon_';

	/**
	 * Store active plugins
	 *
	 * @var array
	 */
	private static $plugins = [];

	/**
	 * Initialize the plugin function here
	 *
	 * @return void
	 */
	public static function init() {
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::REQUIRED_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'show_required_php_version_missing_notice' ] );
			return;
		}

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'show_elementor_missing_notice' ] );
			return;
		}

		add_action( 'admin_init', [ __CLASS__, 'admin_init' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
	}

//    private static function get_elementor_post_types() {
//        return get_post_types_by_support( 'elementor' );
//    }

	public static function admin_init() {
//        foreach ( self::get_elementor_post_types() as $type ) {
//            add_filter( 'views_edit-'. $type, [ __CLASS__, 'add_list_table_link' ] );
//        }

		add_action( 'elementor/admin/after_create_settings/elementor-tools', [ __CLASS__, 'add_settings_page' ] );
	}

	public static function get_setting( $field_id, $default = '' ) {
		return get_option( self::DB_PREFIX . $field_id, $default );
	}

	public static function add_settings_page( Tools $tools ) {
		$tools->add_tab(
			self::SLUG,
			[
				'label' => __( 'Which Elementor Addon', 'which-elementor-addon' ),
				'sections' => [
					'which_elementor_addon_settings' => [
						'callback' => function() {
							echo '<h2>' . esc_html__( 'Label Settings', 'which-elementor-addon' ) . '</h2>';
						},
						'fields' => [
							'enable_on' => [
								'label' => __( 'Enable On', 'which-elementor-addon' ),
								'full_field_id' => self::DB_PREFIX . 'enable_on',
								'field_args' => [
									'type' => 'checkbox_list',
									'std' => ['editor'],
									'options' => [
										'editor'   => __( 'Editor', 'which-elementor-addon' ),
										'frontend' => __( 'Frontend', 'which-elementor-addon' ),
									],
								],
							],
							'show_label_on' => [
								'label' => __( 'Show Label On', 'which-elementor-addon' ),
								'full_field_id' => self::DB_PREFIX . 'show_label_on',
								'field_args' => [
									'type' => 'select',
									'std' => 'hover',
									'options' => [
										'hover'  => __( 'Hover', 'which-elementor-addon' ),
										'always' => __( 'Always', 'which-elementor-addon' ),
									],
								],
							],
							'label_position' => [
								'label' => __( 'Label Position', 'which-elementor-addon' ),
								'full_field_id' => self::DB_PREFIX . 'label_position',
								'field_args' => [
									'type' => 'select',
									'std' => 'hover',
									'options' => [
										'top-left'      => __( 'Top Left', 'which-elementor-addon' ),
										'top-center'    => __( 'Top Center', 'which-elementor-addon' ),
										'center-center' => __( 'Center Center', 'which-elementor-addon' ),
									],
								],
							],
							'show_widget_name' => [
								'label' => __( 'Show Widget Name', 'which-elementor-addon' ),
								'full_field_id' => self::DB_PREFIX . 'show_widget_name',
								'field_args' => [
									'type' => 'checkbox',
									'value' => 1,
									'std' => 0,
									'sub_desc' => esc_html__( 'Check this box to show widget name with plugin name.', 'which-elementor-addon' ),
								],
							],
						]
					]
				]
			]
		);
	}

	public static function add_list_table_link( $views ) {
		$views[ self::SLUG ] = sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'edit.php' ),
			esc_html__( 'Which Elmentor Addon', 'which-elementor-addon' )
			);
		return $views;
	}

	/**
	 * Show required minimum php version missing notice to admin
	 *
	 * @return void
	 */
	public static function show_required_php_version_missing_notice() {
		if ( ! self::user_can_see_notice() ) {
			return;
		}

		$notice = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'which-elementor-addon' ),
			'<strong>' . esc_html__( 'Which Elementor Addon', 'which-elementor-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'which-elementor-addon' ) . '</strong>',
			self::REQUIRED_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
	}

	/**
	 * Show Elementor missing notice to admin
	 *
	 * @return void
	 */
	public static function show_elementor_missing_notice() {
		if ( ! self::user_can_see_notice() ) {
			return;
		}

		$notice = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Elementor installation link */
			__( '%1$s requires %2$s to be installed and activated to function properly. %3$s', 'which-elementor-addon' ),
			'<strong>' . __( 'Which Elementor Addon', 'which-elementor-addon' ) . '</strong>',
			'<strong>' . __( 'Elementor', 'which-elementor-addon' ) . '</strong>',
			'<a href="' . esc_url( admin_url( 'plugin-install.php?s=Elementor&tab=search&type=term' ) ) . '">' . __( 'Please click on this link and install Elementor', 'which-elementor-addon' ) . '</a>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
	}

	/**
	 * Check if current user has the capability to install or activate plugins
	 *
	 * @return bool
	 */
	private static function user_can_see_notice() {
		return current_user_can( 'install_plugins' ) || current_user_can( 'activate_plugins' );
	}

	/**
	 * Get plugin directory name from plugin base
	 *
	 * @param $plugin_base_name
	 * @return bool|string
	 */
	private static function get_plugin_slug( $plugin_base_name ) {
		return substr( $plugin_base_name, 0, strpos( $plugin_base_name, '/' ) );
	}

	/**
	 * Get the active plugins.
	 *
	 * @return array
	 */
	protected static function get_active_plugins() {
		if ( empty( self::$plugins ) ) {
			// Ensure get_plugins function is loaded
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$active_plugins = get_option( 'active_plugins' );
			self::$plugins = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );
		}
		return self::$plugins;
	}

	/**
	 * @return array
	 */
	private static function get_data() {
		$widget_types = \Elementor\Plugin::instance()->widgets_manager->get_widget_types();
		$data_map = [];
		$plugins = self::get_active_plugins();

		foreach ( $widget_types as $widget_key => $widget_data ) {
			$reflection = new \ReflectionClass( $widget_data );

			$widget_file = plugin_basename( $reflection->getFileName() );
			$plugin_slug = self::get_plugin_slug( $widget_file );

			foreach ( $plugins as $plugin_root => $plugin_meta ) {
				$_plugin_slug = self::get_plugin_slug( $plugin_root );
				if ( $plugin_slug === $_plugin_slug ) {
					$data_map[ $widget_key ] = [
						'plugin' => $plugin_meta['Name'],
						'widget' => $widget_data->get_title()
					];
				}
			}
		}

		return $data_map;
	}

	public static function enqueue_scripts() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$enable_on = self::get_setting( 'enable_on', [ 'editor' ] );
		if ( empty( $enable_on ) || ! ( in_array( 'editor', $enable_on ) || in_array( 'frontend', $enable_on ) ) ) {
			return;
		}

		wp_enqueue_style(
			self::SLUG,
			plugins_url( 'assets/css/which-elementor-addon.min.css', __FILE__ ),
			[],
			self::VERSION
		);

		wp_enqueue_script(
			self::SLUG,
			plugins_url( 'assets/js/which-elementor-addon.min.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		wp_localize_script(
			self::SLUG,
			'whichElementorAddon',
			[
				'widgetPluginMap' => self::get_data(),
				'settings' => [
					'enableOn'       => self::get_setting( 'enable_on', ['editor'] ),
					'showLabelOn'    => self::get_setting( 'show_label_on', 'hover' ),
					'labelPosition'  => self::get_setting( 'label_position', 'top-left' ),
					'showWidgetName' => (bool) self::get_setting( 'show_widget_name', 0 ),
				]
			]
		);
	}

}

Which_Elementor_Addon::init();
