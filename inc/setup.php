<?php
/**
 * Theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'project313', P313_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );

		register_nav_menus(
			array(
				'primary' => __( 'Главное меню', 'project313' ),
				'footer'  => __( 'Меню в подвале', 'project313' ),
			)
		);

		add_image_size( 'p313-card', 640, 420, true );
		add_image_size( 'p313-teacher', 500, 620, true );
		add_image_size( 'p313-hero', 1200, 900, true );
	}
);

add_action(
	'init',
	function () {
		$ver = '1.2.0';
		if ( get_option( 'p313_rewrite_ver' ) !== $ver ) {
			flush_rewrite_rules( false );
			update_option( 'p313_rewrite_ver', $ver );
		}
	},
	99
);

add_filter( 'show_admin_bar', '__return_false' );

add_filter(
	'acf/settings/save_json',
	function () {
		return P313_DIR . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		$paths[] = P313_DIR . '/acf-json';
		return $paths;
	}
);
