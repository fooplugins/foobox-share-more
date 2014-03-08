<?php
/*
Plugin Name: FooBox Share More Extension
Plugin URI: http://fooplugins.com/
Description: More social share icons for FooBox
Version: 1.0.0
Author: FooPlugins
Author URI: http://fooplugins.com/
License: GPL2
*/

if ( !class_exists( 'foobox_share_more' ) ) {

	class foobox_share_more {

		const CSS                = 'foobox-share-more.css';
		const FOOBOX_MIN_VERSION = '2.1.0.21';

		private $plugin_slug = 'foobox-share-more';
		private $plugin_ver = '1.0.0';

		function __construct() {
			add_action( 'init', array($this, 'init') );
		}

		function init() {
			if ( is_admin() ) {
				add_action( 'admin_notices', array($this, 'admin_warnings') );
				add_action( 'foobox_pre_section', array($this, 'add_settings') );
				add_action( 'foobox_admin_print_styles', array($this, 'frontend_styles'), 99 );
			} else {
				add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles'), 99 );
			}

			add_filter( 'foobox_social_icons', array($this, 'add_social_icons') );
		}

		function add_social_icons($social_icons) {

			$foobox = $GLOBALS['foobox'];

			if ($foobox === false) return $social_icons;

			$options = $foobox->get_options();

			if ($this->is_option_checked( $options, 'social_vkontakte' )) {

				$social_icon = "{ css: 'fbx-vkontakte', title: 'VK', url: 'http://vkontakte.ru/share.php?url={url}&title={title}&description={desc}&image={img}&noparse=true' }";

				if ($this->is_option_checked( $options, 'social_vkontakte_at_front' )) {
					array_unshift( $social_icons, $social_icon );
				} else {
					$social_icons[] = $social_icon;
				}
			}

			return $social_icons;
		}

		function is_option_checked($options, $key, $default = false) {
			if ( $options ) {
				return array_key_exists( $key, $options );
			}

			return $default;
		}

		function add_settings($section_id) {
			if ($section_id === 'social-opengraph') {
				$foobox = $GLOBALS['foobox'];

				if ($foobox === false) return;

				$foobox->admin_settings_add( array(
					'id'      => 'social_vkontakte',
					'title'   => __( 'VK.com Enabled', 'foobox' ),
					'type'    => 'checkbox',
					'section' => 'settings',
					'tab'     => 'social'
				) );

				$foobox->admin_settings_add( array(
					'id'      => 'social_vkontakte_at_front',
					'title'   => __( 'VK.com icon in front', 'foobox' ),
					'type'    => 'checkbox',
					'section' => 'settings',
					'tab'     => 'social'
				) );
			}
		}

		function frontend_styles() {
			//if foobox styles not added get out
			if ( !apply_filters( 'foobox_enqueue_styles', true ) ) return;

			$css_src_url = plugins_url( 'css/' . self::CSS, __FILE__ );

			wp_register_style(
				$handle = $this->plugin_slug,
				$src = $css_src_url,
				$ver = $this->plugin_ver );

			wp_enqueue_style( $this->plugin_slug );
		}

		function admin_warnings() {
			if ( !isset($GLOBALS['foobox']) ) {

				$message = __( 'The FooBox plugin is required for the FooBox Share More extension to work!', 'foobox-share-more' );

			} else if ( version_compare( $GLOBALS['foobox']->plugin_version(), self::FOOBOX_MIN_VERSION ) < 0 ) {

				$message = sprintf( __( 'FooBox version %s is required for the FooBox Share More extension to work!', 'foobox-share-more' ), self::FOOBOX_MIN_VERSION );

			}

			if ( empty($message) ) {
				return;
			}

			?>
			<div class="error">
				<p>
					<strong><?php _e( 'FooBox Share More Notice', 'foobox-share-more' ); ?>:</strong><br/>
					<?php echo $message; ?>
				</p>
			</div>
		<?php
		}
	}

	//run the plugin!
	$GLOBALS['foobox_share_more'] = new foobox_share_more();
}