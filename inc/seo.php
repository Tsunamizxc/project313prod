<?php
/**
 * Basic SEO: title, description, OG, canonical.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve SEO fields for current view.
 *
 * @return array{title:string,description:string,image:string,robots:string}
 */
function p313_get_seo() {
	$defaults = array(
		'title'       => p313_option( 'seo_default_title', get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ) ),
		'description' => p313_option( 'seo_default_description', get_bloginfo( 'description' ) ),
		'image'       => p313_img_url( p313_option( 'seo_default_image' ), 'large' ) ?: p313_asset( 'assets/img/hero.webp' ),
		'robots'      => p313_option( 'seo_robots', 'index,follow' ),
	);

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$title   = p313_field( 'seo_title', '', $post_id );
		$desc    = p313_field( 'seo_description', '', $post_id );
		$image   = p313_img_url( p313_field( 'seo_image', '', $post_id ), 'large' );
		$robots  = p313_field( 'seo_robots', '', $post_id );

		if ( $title ) {
			$defaults['title'] = $title;
		} elseif ( ! is_front_page() ) {
			$defaults['title'] = wp_strip_all_tags( get_the_title( $post_id ) ) . ' — ' . get_bloginfo( 'name' );
		}
		if ( $desc ) {
			$defaults['description'] = $desc;
		} elseif ( has_excerpt( $post_id ) ) {
			$defaults['description'] = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		}
		if ( $image ) {
			$defaults['image'] = $image;
		} elseif ( has_post_thumbnail( $post_id ) ) {
			$defaults['image'] = get_the_post_thumbnail_url( $post_id, 'large' );
		}
		if ( $robots ) {
			$defaults['robots'] = $robots;
		}
	}

	return $defaults;
}

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		$seo = p313_get_seo();
		return $seo['title'] ? $seo['title'] : $title;
	},
	20
);

add_action(
	'wp_head',
	function () {
		$seo  = p313_get_seo();
		$url  = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
		$site = get_bloginfo( 'name' );

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $seo['description'] ) ) . '">' . "\n";
		echo '<meta name="robots" content="' . esc_attr( $seo['robots'] ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";

		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $site ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags( $seo['description'] ) ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		if ( $seo['image'] ) {
			echo '<meta property="og:image" content="' . esc_url( $seo['image'] ) . '">' . "\n";
		}

		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( wp_strip_all_tags( $seo['description'] ) ) . '">' . "\n";
		if ( $seo['image'] ) {
			echo '<meta name="twitter:image" content="' . esc_url( $seo['image'] ) . '">' . "\n";
		}

		$jsonld = array(
			'@context' => 'https://schema.org',
			'@type'    => 'DanceSchool',
			'name'     => p313_option( 'org_name', $site ),
			'url'      => home_url( '/' ),
			'telephone'=> p313_phone(),
			'email'    => p313_option( 'email', '' ),
			'address'  => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => p313_option( 'address', '' ),
				'addressLocality' => p313_option( 'city', 'Омск' ),
				'addressCountry'  => 'RU',
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	},
	1
);
