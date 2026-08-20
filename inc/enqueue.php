<?php
/**
 * Assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}
		return $urls;
	},
	10,
	2
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css_ver = file_exists( P313_DIR . '/assets/css/main.css' ) ? (string) filemtime( P313_DIR . '/assets/css/main.css' ) : P313_VERSION;
		$js_ver  = file_exists( P313_DIR . '/assets/js/main.js' ) ? (string) filemtime( P313_DIR . '/assets/js/main.js' ) : P313_VERSION;

		wp_enqueue_style(
			'p313-fonts',
			'https://fonts.googleapis.com/css2?family=Unbounded:wght@200..900&display=swap',
			array(),
			null
		);

		wp_enqueue_style( 'p313-vendor', p313_asset( 'assets/css/vendor.css' ), array(), $css_ver );
		wp_enqueue_style( 'p313-main', p313_asset( 'assets/css/main.css' ), array( 'p313-vendor', 'p313-fonts' ), $css_ver );
		$overrides_ver = file_exists( P313_DIR . '/assets/css/overrides.css' ) ? (string) filemtime( P313_DIR . '/assets/css/overrides.css' ) : P313_VERSION;
		wp_enqueue_style( 'p313-overrides', p313_asset( 'assets/css/overrides.css' ), array( 'p313-main' ), $overrides_ver );

		wp_enqueue_script( 'p313-boot', p313_asset( 'assets/js/boot.js' ), array(), $js_ver, false );
		wp_localize_script( 'p313-boot', 'P313', p313_get_frontend_data() );

		wp_enqueue_script( 'p313-main', p313_asset( 'assets/js/main.js' ), array( 'p313-boot' ), $js_ver, true );
		$enh_ver = file_exists( P313_DIR . '/assets/js/enhancements.js' ) ? (string) filemtime( P313_DIR . '/assets/js/enhancements.js' ) : P313_VERSION;
		wp_enqueue_script( 'p313-enhancements', p313_asset( 'assets/js/enhancements.js' ), array( 'p313-main' ), $enh_ver, true );
		wp_enqueue_script( 'p313-schedule', p313_asset( 'assets/js/schedule-render.js' ), array( 'p313-main' ), $js_ver, true );

		wp_localize_script(
			'p313-main',
			'P313Forms',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'p313_signup' ),
				'homeUrl' => home_url( '/' ),
			)
		);
	}
);
