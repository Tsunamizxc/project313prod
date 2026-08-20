<?php
/**
 * Helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function p313_field( $key, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $key, $post_id );
	if ( $value === null || $value === false || $value === '' ) {
		return $default;
	}
	return $value;
}

function p313_option( $key, $default = '' ) {
	return p313_field( $key, $default, 'option' );
}

/**
 * Page hero/head args from ACF with defaults.
 *
 * @param string $prefix  Field prefix, e.g. page_schedule.
 * @param array  $defaults Keys: number, label, title, sub.
 * @return array
 */
function p313_page_head_args( $prefix, $defaults = array() ) {
	$images = isset( $defaults['images'] ) && is_array( $defaults['images'] ) ? $defaults['images'] : array();
	$hero   = p313_img_url( p313_field( $prefix . '_image', '' ), 'p313-card' );
	if ( $hero && ! in_array( $hero, $images, true ) ) {
		array_unshift( $images, $hero );
	}

	return array(
		'number' => '',
		'label'  => p313_field( $prefix . '_label', $defaults['label'] ?? '' ),
		'title'  => p313_field( $prefix . '_title', $defaults['title'] ?? '' ),
		'sub'    => p313_field( $prefix . '_sub', $defaults['sub'] ?? '' ),
		'text'   => p313_field( $prefix . '_text', $defaults['text'] ?? '' ),
		'images' => $images,
	);
}

/**
 * Years from current down to $from (inclusive).
 *
 * @param int $from Oldest year.
 * @return array<string, string>
 */
function p313_year_choices( $from = 2016 ) {
	$out = array();
	for ( $year = (int) gmdate( 'Y' ); $year >= (int) $from; $year-- ) {
		$out[ (string) $year ] = (string) $year;
	}
	return $out;
}

/**
 * Year list for front-end filters: Все + current…2016.
 *
 * @param int $from Oldest year.
 * @return string[]
 */
function p313_year_filter_list( $from = 2016 ) {
	return array_merge( array( 'Все' ), array_values( p313_year_choices( $from ) ) );
}

function p313_gallery_category_choices() {
	$raw = (string) p313_option( 'gallery_cats', 'Все,Конкурсы,Мероприятия,Отчётные,Будни' );
	$out = array();
	foreach ( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) as $cat ) {
		if ( 'Все' === $cat ) {
			continue;
		}
		$out[ $cat ] = $cat;
	}
	return $out ? $out : array(
		'Конкурсы'    => 'Конкурсы',
		'Мероприятия' => 'Мероприятия',
		'Отчётные'    => 'Отчётные',
		'Будни'       => 'Будни',
	);
}

function p313_service_age_choices() {
	$out  = array( '' => 'Не указано' );
	$ages = p313_option( 'service_ages', array() );
	if ( is_array( $ages ) ) {
		foreach ( $ages as $row ) {
			$id = is_array( $row ) ? trim( (string) ( $row['id'] ?? '' ) ) : '';
			if ( $id && 'all' !== $id ) {
				$out[ $id ] = is_array( $row ) ? (string) ( $row['label'] ?? $id ) : $id;
			}
		}
	}
	return $out;
}

/**
 * Published kids groups for service select.
 *
 * @return array<string, string>
 */
function p313_kids_group_choices() {
	$out   = array();
	$posts = get_posts(
		array(
			'post_type'      => 'p313_kids',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	foreach ( $posts as $post ) {
		$out[ (string) $post->ID ] = get_the_title( $post );
	}
	return $out;
}

function p313_founder_more_url() {
	$url = trim( (string) p313_option( 'founder_more_url', '' ) );
	return $url ? $url : 'https://project313.ru/founder/';
}

/**
 * Founder page ID by path/template.
 *
 * @return int
 */
function p313_founder_page_id() {
	$page = get_page_by_path( 'founder' );
	if ( $page ) {
		return (int) $page->ID;
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/template-founder.php',
		)
	);
	return $pages ? (int) $pages[0] : 0;
}

/**
 * Founder field: page value first, then options.
 *
 * @param string $page_key   Page field name.
 * @param string $option_key Option field name.
 * @param mixed  $default    Default.
 * @return mixed
 */
function p313_founder_value( $page_key, $option_key, $default = '' ) {
	$page_id = p313_founder_page_id();
	if ( $page_id && function_exists( 'get_field' ) ) {
		$value = get_field( $page_key, $page_id );
		if ( ! ( $value === null || $value === false || $value === '' || ( is_array( $value ) && ! $value ) ) ) {
			return $value;
		}
	}
	return p313_option( $option_key, $default );
}

/**
 * Normalize founder facts repeater.
 *
 * @param mixed $rows Raw repeater.
 * @return array<int, array{n:string,label:string}>
 */
function p313_normalize_founder_facts( $rows ) {
	$out = array();
	if ( ! is_array( $rows ) ) {
		return $out;
	}
	foreach ( $rows as $row ) {
		$n     = trim( (string) ( $row['n'] ?? '' ) );
		$label = trim( (string) ( $row['label'] ?? '' ) );
		if ( '' === $n && '' === $label ) {
			continue;
		}
		$out[] = array(
			'n'     => $n,
			'label' => $label,
		);
	}
	return $out;
}

/**
 * Build VK video iframe from URL or embed code.
 *
 * @param string $raw URL or iframe HTML.
 * @return string Safe HTML or empty.
 */
function p313_vk_video_embed( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	if ( false !== stripos( $raw, '<iframe' ) ) {
		return wp_kses(
			$raw,
			array(
				'iframe' => array(
					'src'             => true,
					'width'           => true,
					'height'          => true,
					'allow'           => true,
					'allowfullscreen' => true,
					'frameborder'     => true,
					'style'           => true,
					'class'           => true,
					'title'           => true,
					'loading'         => true,
				),
			)
		);
	}

	$oid = '';
	$id  = '';
	if ( preg_match( '/video_ext\.php\?([^#\s"\']+)/i', $raw, $m ) ) {
		parse_str( html_entity_decode( $m[1] ), $query );
		$oid = isset( $query['oid'] ) ? (string) $query['oid'] : '';
		$id  = isset( $query['id'] ) ? (string) $query['id'] : '';
	} elseif ( preg_match( '/(?:vk\.com|vkvideo\.ru)\/video(-?\d+)_(\d+)/i', $raw, $m ) ) {
		$oid = $m[1];
		$id  = $m[2];
	} elseif ( preg_match( '/oid=(-?\d+).*?[?&]id=(\d+)/i', $raw, $m ) ) {
		$oid = $m[1];
		$id  = $m[2];
	}

	if ( '' === $oid || '' === $id ) {
		return '';
	}

	$src = add_query_arg(
		array(
			'oid' => $oid,
			'id'  => $id,
			'hd'  => 2,
		),
		'https://vk.com/video_ext.php'
	);

	return sprintf(
		'<div class="review-card__video"><iframe src="%s" title="Видео VK" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" allowfullscreen loading="lazy"></iframe></div>',
		esc_url( $src )
	);
}

/**
 * Repeater row label (supports label / name / title).
 *
 * @param mixed $row Row array or string.
 * @return string
 */
function p313_row_label( $row ) {
	if ( is_string( $row ) ) {
		return $row;
	}
	if ( ! is_array( $row ) ) {
		return '';
	}
	return (string) ( $row['label'] ?? $row['name'] ?? $row['title'] ?? '' );
}

/**
 * Post title from ACF post_object / ID / WP_Post.
 *
 * @param mixed $value Field value.
 * @return string
 */
function p313_post_title( $value ) {
	if ( empty( $value ) ) {
		return '';
	}
	if ( is_object( $value ) && isset( $value->post_title ) ) {
		return (string) $value->post_title;
	}
	if ( is_array( $value ) && ! empty( $value['post_title'] ) ) {
		return (string) $value['post_title'];
	}
	if ( is_numeric( $value ) ) {
		$title = get_the_title( (int) $value );
		return $title && 'Auto Draft' !== $title ? $title : '';
	}
	return is_string( $value ) ? $value : '';
}

/**
 * Find CPT post by exact title.
 *
 * @param string $post_type Post type slug.
 * @param string $title     Post title.
 * @return int Post ID or 0.
 */
function p313_find_post_by_title( $post_type, $title ) {
	$title = trim( (string) $title );
	if ( '' === $title ) {
		return 0;
	}
	$posts = get_posts(
		array(
			'post_type'              => $post_type,
			'title'                  => $title,
			'posts_per_page'         => 1,
			'post_status'            => 'publish',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( $posts ) {
		return (int) $posts[0];
	}

	$needle = p313_normalize_title( $title );
	if ( '' === $needle ) {
		return 0;
	}
	$all = get_posts(
		array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		)
	);
	foreach ( $all as $post_id ) {
		if ( p313_normalize_title( get_the_title( $post_id ) ) === $needle ) {
			return (int) $post_id;
		}
	}
	return 0;
}

/**
 * Weekday choices for schedule.
 *
 * @return array<string, string>
 */
function p313_weekday_choices() {
	return array(
		'Понедельник' => 'Понедельник',
		'Вторник'     => 'Вторник',
		'Среда'       => 'Среда',
		'Четверг'     => 'Четверг',
		'Пятница'     => 'Пятница',
		'Суббота'     => 'Суббота',
		'Воскресенье' => 'Воскресенье',
	);
}

function p313_default_phone() {
	return '+7 961 884 14 74';
}

function p313_default_phone_href() {
	return 'tel:+79618841474';
}

function p313_legacy_phones() {
	return array(
		'+7 (3812) 31-33-13',
		'+7(3812)31-33-13',
		'+73812313313',
		'+7 996 188-41-74',
		'+79961884174',
	);
}

function p313_phone() {
	$phone = trim( (string) p313_option( 'phone', '' ) );
	if ( ! $phone || in_array( $phone, p313_legacy_phones(), true ) ) {
		return p313_default_phone();
	}
	return $phone;
}

function p313_phone_href() {
	$href   = trim( (string) p313_option( 'phone_href', '' ) );
	$legacy = array( 'tel:+73812313313', 'tel:+7 (3812) 31-33-13', 'tel:+79961884174' );
	if ( ! $href || in_array( $href, $legacy, true ) ) {
		$digits = preg_replace( '/\D+/', '', p313_phone() );
		return $digits ? 'tel:+' . $digits : p313_default_phone_href();
	}
	return $href;
}

function p313_vk_url() {
	$url = '';
	$socials = p313_option( 'socials', array() );
	if ( is_array( $socials ) ) {
		foreach ( $socials as $social ) {
			$label = mb_strtolower( p313_row_label( $social ) );
			$href  = is_array( $social ) ? trim( (string) ( $social['url'] ?? '' ) ) : '';
			if ( $href && ( false !== strpos( $label, 'vk' ) || false !== strpos( $href, 'vk.' ) ) ) {
				$url = $href;
				break;
			}
		}
	}
	if ( ! $url || '#' === $url ) {
		return 'https://vk.ru/dance.project313';
	}
	return $url;
}

function p313_default_branches() {
	return array(
		array(
			'title'   => 'Красный Путь',
			'key'     => 'krasny',
			'address' => 'ул. Красный Путь, 59 (этаж 3, офис 5)',
			'lat'     => '54.9954',
			'lng'     => '73.3577',
		),
		array(
			'title'   => 'Химик',
			'key'     => 'khimik',
			'address' => 'просп. Королёва, 1',
			'lat'     => '55.0423',
			'lng'     => '73.2950',
		),
	);
}

function p313_get_branches() {
	$out   = array();
	$posts = get_posts(
		array(
			'post_type'      => 'p313_branch',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	foreach ( $posts as $post ) {
		$id  = $post->ID;
		$key = p313_field( 'branch_key', '', $id );
		$out[] = array(
			'id'      => (string) $id,
			'title'   => get_the_title( $id ),
			'key'     => $key ?: sanitize_title( get_the_title( $id ) ),
			'address' => p313_field( 'address', '', $id ),
			'phone'   => p313_field( 'phone', '', $id ),
			'lat'     => p313_field( 'lat', '', $id ),
			'lng'     => p313_field( 'lng', '', $id ),
		);
	}
	if ( $out ) {
		return $out;
	}
	$fallback = array();
	foreach ( p313_default_branches() as $branch ) {
		$fallback[] = array(
			'id'      => $branch['key'],
			'title'   => $branch['title'],
			'key'     => $branch['key'],
			'address' => $branch['address'],
			'phone'   => '',
			'lat'     => $branch['lat'],
			'lng'     => $branch['lng'],
		);
	}
	return $fallback;
}

function p313_map_embed() {
	$custom = trim( (string) p313_option( 'map_embed', '' ) );
	if ( $custom ) {
		return $custom;
	}

	$points = array();
	$lats   = array();
	$lngs   = array();
	foreach ( p313_get_branches() as $branch ) {
		if ( '' === (string) $branch['lat'] || '' === (string) $branch['lng'] ) {
			continue;
		}
		$lats[]   = (float) $branch['lat'];
		$lngs[]   = (float) $branch['lng'];
		$label    = preg_replace( '/[~,]/u', ' ', (string) $branch['title'] );
		$points[] = $branch['lng'] . ',' . $branch['lat'] . ',pm2rdm' . ( $label ? ',' . $label : '' );
	}
	if ( ! $points ) {
		return '';
	}

	$ll  = ( array_sum( $lngs ) / count( $lngs ) ) . ',' . ( array_sum( $lats ) / count( $lats ) );
	$src = add_query_arg(
		array(
			'll' => $ll,
			'z'  => 12,
			'l'  => 'map',
			'pt' => implode( '~', $points ),
		),
		'https://yandex.ru/map-widget/v1/'
	);

	return '<iframe class="contacts__map-frame" src="' . esc_url( $src ) . '" title="Филиалы Project 313 на карте" loading="lazy" allowfullscreen></iframe>';
}

function p313_normalize_title( $value ) {
	$value = mb_strtolower( trim( (string) $value ) );
	$value = str_replace( array( '«', '»', '"', "'", '“', '”', 'ё' ), array( '', '', '', '', '', '', 'е' ), $value );
	return preg_replace( '/\s+/u', ' ', $value );
}

function p313_gallery_urls( $value, $size = 'p313-card' ) {
	$urls = array();
	if ( empty( $value ) ) {
		return $urls;
	}
	if ( ! is_array( $value ) ) {
		$url = p313_img_url( $value, $size );
		return $url ? array( $url ) : array();
	}
	foreach ( $value as $item ) {
		$url = p313_img_url( $item, $size );
		if ( $url && ! in_array( $url, $urls, true ) ) {
			$urls[] = $url;
		}
	}
	return $urls;
}

function p313_img_url( $image, $size = 'large' ) {
	if ( empty( $image ) ) {
		return '';
	}
	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, $size );
		return $url ? $url : '';
	}
	if ( is_array( $image ) ) {
		if ( ! empty( $image['sizes'][ $size ] ) ) {
			return $image['sizes'][ $size ];
		}
		return isset( $image['url'] ) ? $image['url'] : '';
	}
	if ( is_string( $image ) ) {
		return $image;
	}
	return '';
}

function p313_unsplash( $id, $w = 640, $h = 420 ) {
	return sprintf(
		'https://images.unsplash.com/photo-%s?w=%d&h=%d&fit=crop&auto=format&q=80',
		rawurlencode( $id ),
		(int) $w,
		(int) $h
	);
}

function p313_asset( $path ) {
	return trailingslashit( P313_URI ) . ltrim( $path, '/' );
}

function p313_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function p313_is_current( $slug ) {
	if ( $slug === 'home' || $slug === 'index' ) {
		return is_front_page();
	}
	return is_page( $slug );
}

function p313_nav_class( $slug, $base = 'header__link' ) {
	$classes = array( $base );
	if ( p313_is_current( $slug ) ) {
		$classes[] = $base . '--active';
	}
	return esc_attr( implode( ' ', $classes ) );
}

function p313_sweep( $class = '', $opacity = '0.7' ) {
	$class = trim( 'sweep ' . $class );
	printf(
		'<svg class="%s" style="opacity:%s" viewBox="0 0 1200 220" fill="none" preserveAspectRatio="none" aria-hidden="true"><path class="sweep__path" d="M0 168 C 220 60, 360 200, 560 120 S 940 8, 1200 96" stroke="var(--color-navy)" stroke-width="1.25" stroke-opacity="0.5" stroke-linecap="round"></path></svg>',
		esc_attr( $class ),
		esc_attr( $opacity )
	);
}

function p313_icon( $name ) {
	$icons = array(
		'arrow' => '<svg class="icon icon--md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>',
		'close' => '<svg class="icon icon--md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"></path></svg>',
		'plus'  => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"></path></svg>',
		'spark' => '<svg class="icon icon--md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M12 3v6M12 15v6M3 12h6M15 12h6"></path></svg>',
		'clock' => '<svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7.5V12l3 2"></path></svg>',
		'pin'   => '<svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>',
		'play'  => '<svg class="icon icon--lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"></circle><path d="M10 8.5l6 3.5-6 3.5z"></path></svg>',
	);
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}
