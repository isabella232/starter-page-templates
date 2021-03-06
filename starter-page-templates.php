<?php
/**
 * Plugin Name: Starter Page Templates
 * Description: Create new pages by selecting pre-built page templates.
 * Version: 1.0.0
 * Author: Automattic
 *
 * @package starter-page-templates
 */

/**
 * Retrieves a URL to a file in the plugin's directory.
 *
 * @param  string $path Relative path of the desired file.
 * @return string       Fully qualified URL pointing to the desired file.
 */
function wp_js_plugin_starter_url( $path ) {
	return plugins_url( $path, __FILE__ );
}

/**
 * Register scripts.
 */
function page_templates_register() {
	wp_register_script(
		'starter-page-templates',
		wp_js_plugin_starter_url( 'dist/index.js' ),
		array( 'wp-plugins', 'wp-edit-post', 'wp-element' ),
		filemtime( plugin_dir_path( __FILE__ ) . '/dist/index.js' ),
		true
	);
}
add_action( 'init', 'page_templates_register' );

/**
 * Enqueue scripts.
 */
function page_templates_enqueue() {
	$screen = get_current_screen();

	// Return early if we don't meet conditions to show templates.
	if ( 'page' !== $screen->id || 'add' !== $screen->action ) {
		return;
	}

	wp_enqueue_script( 'starter-page-templates' );

	$default_info = array(
		'title' => get_bloginfo( 'name' ),
	);
	$site_info    = get_site_option( 'site_contact_info', array() );

	$config = array(
		'siteInformation' => array_merge( $default_info, $site_info ),
		'templates'       => array(
			array(
				'title'   => 'Home',
				'slug'    => 'home',
				'content' => json_decode( wp_remote_get( 'http://www.mocky.io/v2/5ce680d73300009801731614' )[ 'body' ] )->body->content,
				'preview' => 'https://via.placeholder.com/200x180',
			),
			array(
				'title'   => 'Menu',
				'slug'    => 'menu',
				'content' => json_decode( wp_remote_get( 'http://www.mocky.io/v2/5ce681173300006600731617' )[ 'body' ] )->body->content,
				'preview' => 'https://via.placeholder.com/200x180',
			),
			array(
				'title'   => 'Contact Us',
				'slug'    => 'contact',
				'content' => json_decode( wp_remote_get( 'http://www.mocky.io/v2/5ce681763300004b3573161a' )[ 'body' ] )->body->content,
				'preview' => 'https://via.placeholder.com/200x180',
			),
		),
	);
	wp_localize_script( 'starter-page-templates', 'starterPageTemplatesConfig', $config );
}
add_action( 'enqueue_block_editor_assets', 'page_templates_enqueue' );



function spt_enqueue_block_assets() {
	$style_file = is_rtl()
			? 'index.rtl.css'
			: 'index.css';

	wp_enqueue_style(
		'spt-style',
		wp_js_plugin_starter_url( 'dist/' . $style_file ),
		array(),
		filemtime( plugin_dir_path( __DIR__ . '/dist/' . $style_file ) )
	);
}

add_action( 'enqueue_block_assets', 'spt_enqueue_block_assets', 100 );
