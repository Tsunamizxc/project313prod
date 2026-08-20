<?php
/**
 * Frontend data bridge (window.P313).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function p313_posts( $type, $limit = -1 ) {
	return get_posts(
		array(
			'post_type'      => $type,
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
}

function p313_get_frontend_data() {
	$services = array();
	foreach ( p313_posts( 'p313_service' ) as $post ) {
		$id     = $post->ID;
		$photo  = p313_field( 'photo_id', '', $id );
		$thumb  = get_the_post_thumbnail_url( $id, 'p313-card' );
		$photos = p313_gallery_urls( p313_field( 'photos', array(), $id ), 'p313-card' );
		if ( $thumb && ! in_array( $thumb, $photos, true ) ) {
			array_unshift( $photos, $thumb );
		}
		if ( ! $photos && $photo ) {
			$photos[] = p313_unsplash( $photo, 640, 420 );
		}
		$services[] = array(
			'id'        => p313_field( 'service_key', (string) $id, $id ),
			'title'     => get_the_title( $id ),
			'price'     => p313_field( 'price', '', $id ),
			'age'       => p313_field( 'age_label', '', $id ),
			'ageGroup'  => p313_field( 'age_group', '', $id ),
			'format'    => p313_field( 'format', 'group', $id ),
			'duration'  => p313_field( 'duration', '', $id ),
			'photo'     => $photo ?: '',
			'photoUrl'  => $photos ? $photos[0] : '',
			'photos'    => $photos,
			'short'     => p313_field( 'short', '', $id ),
			'text'      => p313_field( 'text', '', $id ),
		);
	}

	$teachers = array();
	foreach ( p313_posts( 'p313_teacher' ) as $post ) {
		$id = $post->ID;
		$photo = p313_field( 'photo_id', '', $id );
		$thumb = get_the_post_thumbnail_url( $id, 'p313-teacher' );
		$is_leader = (bool) p313_field( 'is_leader', false, $id );
		if ( ! $is_leader && get_the_title( $id ) === p313_option( 'founder_name', 'Анна Волкова' ) ) {
			$is_leader = true;
		}
		$teachers[] = array(
			'id'         => p313_field( 'teacher_key', (string) $id, $id ),
			'name'       => get_the_title( $id ),
			'role'       => p313_field( 'role', '', $id ),
			'exp'        => p313_field( 'exp', '', $id ),
			'education'  => p313_field( 'education', '', $id ),
			'bio'        => p313_field( 'bio', '', $id ),
			'isLeader'   => $is_leader,
			'moreUrl'    => p313_field( 'more_url', '', $id ) ?: p313_founder_more_url(),
			'photo'      => $photo ?: '',
			'photoUrl'   => $thumb ?: ( $photo ? p313_unsplash( $photo, 500, 620 ) : '' ),
		);
	}

	$kids = array();
	foreach ( p313_posts( 'p313_kids' ) as $post ) {
		$id        = $post->ID;
		$photo_url = p313_img_url( p313_field( 'photo', '', $id ), 'p313-teacher' ) ?: get_the_post_thumbnail_url( $id, 'p313-teacher' );
		$members   = array();
		$rows      = p313_field( 'members', array(), $id );
		if ( is_array( $rows ) ) {
			foreach ( $rows as $row ) {
				$name = trim( (string) ( $row['name'] ?? '' ) );
				$murl = p313_img_url( $row['photo'] ?? '', 'p313-teacher' );
				if ( ! $name && ! $murl ) {
					continue;
				}
				$members[] = array(
					'name'     => $name,
					'photoUrl' => $murl,
					'branch'   => (string) ( $row['branch'] ?? '' ),
				);
			}
		}
		$kids[] = array(
			'id'       => (string) $id,
			'name'     => get_the_title( $id ),
			'age'      => p313_field( 'age', '', $id ),
			'note'     => p313_field( 'note', '', $id ),
			'photoUrl' => $photo_url ?: '',
			'url'      => get_permalink( $id ),
			'level'    => p313_field( 'level', '', $id ),
			'members'  => $members,
		);
	}

	$schedule = array();
	foreach ( p313_posts( 'p313_schedule' ) as $post ) {
		$id         = $post->ID;
		$service_id = p313_field( 'service', '', $id );
		$group_id   = p313_field( 'group', '', $id );
		$teacher_id = p313_field( 'teacher_ref', '', $id );
		$branch_id  = p313_field( 'branch', '', $id );

		$dir = p313_post_title( $service_id );
		if ( ! $dir ) {
			$dir = p313_field( 'direction', get_the_title( $id ), $id );
		}

		$teacher = p313_post_title( $teacher_id );
		if ( ! $teacher ) {
			$teacher = p313_field( 'teacher', '', $id );
		}

		$branch = p313_post_title( $branch_id );
		if ( ! $branch ) {
			$branch = p313_field( 'hall', '', $id );
		}

		$schedule[] = array(
			'time'    => p313_field( 'time', '', $id ),
			'day'     => p313_field( 'day', '', $id ),
			'dir'     => $dir,
			'group'   => p313_post_title( $group_id ),
			'teacher' => $teacher,
			'branch'  => $branch,
			'hall'    => $branch,
		);
	}

	$awards = array();
	foreach ( p313_posts( 'p313_award' ) as $post ) {
		$awards[] = array(
			'year'  => (int) p313_field( 'year', 0, $post ),
			'title' => get_the_title( $post ),
			'place' => p313_field( 'place', '', $post ),
			'level' => p313_field( 'level', '', $post ),
		);
	}

	$reviews = array();
	foreach ( p313_posts( 'p313_review' ) as $post ) {
		$reviews[] = array(
			'name'   => get_the_title( $post ),
			'role'   => p313_field( 'role', '', $post ),
			'text'   => p313_field( 'text', '', $post ),
			'rating' => (int) p313_field( 'rating', 5, $post ),
		);
	}

	$events = array();
	foreach ( p313_posts( 'p313_event' ) as $post ) {
		$id = $post->ID;
		$photo = p313_field( 'photo_id', '', $id );
		$thumb = get_the_post_thumbnail_url( $id, 'p313-card' );
		$events[] = array(
			'id'       => (string) $id,
			'title'    => get_the_title( $id ),
			'date'     => p313_field( 'date_label', '', $id ),
			'time'     => p313_field( 'time', '', $id ),
			'place'    => p313_field( 'place', '', $id ),
			'excerpt'  => p313_field( 'excerpt', wp_strip_all_tags( get_the_excerpt( $id ) ), $id ),
			'photo'    => $photo ?: '',
			'photoUrl' => $thumb ?: ( $photo ? p313_unsplash( $photo, 640, 400 ) : '' ),
			'content'  => apply_filters( 'the_content', $post->post_content ),
		);
	}

	$gallery = array();
	foreach ( p313_posts( 'p313_gallery' ) as $post ) {
		$id = $post->ID;
		$photo = p313_field( 'photo_id', '', $id );
		$thumb = get_the_post_thumbnail_url( $id, 'large' );
		$gallery[] = array(
			'id'       => (string) $id,
			'year'     => (int) p313_field( 'year', 0, $id ),
			'cat'      => p313_field( 'category', '', $id ),
			'ratio'    => p313_field( 'ratio', 'square', $id ),
			'photo'    => $photo ?: '',
			'photoUrl' => $thumb ?: ( $photo ? p313_unsplash( $photo, 600, 600 ) : '' ),
		);
	}

	$faq = array();
	foreach ( p313_posts( 'p313_faq' ) as $post ) {
		$faq[] = array(
			'q' => get_the_title( $post ),
			'a' => p313_field( 'answer', '', $post ),
		);
	}

	$blog = array();
	foreach ( get_posts( array( 'post_type' => 'post', 'posts_per_page' => 12 ) ) as $post ) {
		$id = $post->ID;
		$cats = get_the_category( $id );
		$blog[] = array(
			'id'       => (string) $id,
			'title'    => get_the_title( $id ),
			'date'     => get_the_date( 'j F Y', $id ),
			'cat'      => $cats ? $cats[0]->name : '',
			'read'     => p313_field( 'read_time', '5 мин', $id ),
			'excerpt'  => p313_field( 'card_excerpt', wp_strip_all_tags( get_the_excerpt( $id ) ), $id ),
			'photo'    => p313_field( 'photo_id', '', $id ),
			'photoUrl' => get_the_post_thumbnail_url( $id, 'p313-card' ) ?: '',
			'url'      => get_permalink( $id ),
			'content'  => apply_filters( 'the_content', $post->post_content ),
		);
	}

	$facts = p313_option( 'hero_facts', array() );
	if ( ! is_array( $facts ) ) {
		$facts = array();
	}
	$facts = array_values(
		array_map(
			function ( $row ) {
				return array(
					'n'     => $row['n'] ?? '',
					'label' => $row['label'] ?? '',
				);
			},
			$facts
		)
	);

	$ticker = p313_option( 'ticker_items', array() );
	$ticker_out = array();
	if ( is_array( $ticker ) ) {
		foreach ( $ticker as $row ) {
			if ( ! empty( $row['text'] ) ) {
				$ticker_out[] = $row['text'];
			}
		}
	}

	$ages = p313_option( 'service_ages', array() );
	$ages_out = array();
	if ( is_array( $ages ) ) {
		foreach ( $ages as $row ) {
			$ages_out[] = array(
				'id'    => $row['id'] ?? '',
				'label' => $row['label'] ?? '',
			);
		}
	}

	$founder_facts = p313_option( 'founder_facts', array() );
	$founder_facts_out = array();
	if ( is_array( $founder_facts ) ) {
		foreach ( $founder_facts as $row ) {
			$founder_facts_out[] = array(
				'n'     => $row['n'] ?? '',
				'label' => $row['label'] ?? '',
			);
		}
	}

	$founder_bio = p313_option( 'founder_bio', array() );
	$bio_out = array();
	if ( is_array( $founder_bio ) ) {
		foreach ( $founder_bio as $row ) {
			if ( ! empty( $row['paragraph'] ) ) {
				$bio_out[] = $row['paragraph'];
			}
		}
	}

	return array(
		'homeUrl'      => home_url( '/' ),
		'themeUrl'     => P313_URI,
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'p313_signup' ),
		'pages'        => array(
			'home'      => home_url( '/' ),
			'team'      => p313_page_url( 'team' ),
			'services'  => p313_page_url( 'services' ),
			'schedule'  => p313_page_url( 'schedule' ),
			'awards'    => p313_page_url( 'awards' ),
			'gallery'   => p313_page_url( 'gallery' ),
			'blog'      => p313_page_url( 'blog' ),
			'events'    => p313_page_url( 'events' ),
			'contacts'  => p313_page_url( 'contacts' ),
			'founder'   => p313_page_url( 'founder' ),
			'reviews'   => p313_page_url( 'reviews' ),
		),
		'phone'        => p313_phone(),
		'phoneHref'    => p313_phone_href(),
		'vkUrl'        => p313_vk_url(),
		'BRANCHES'     => p313_get_branches(),
		'FACTS'        => $facts,
		'SERVICE_AGES' => $ages_out,
		'SERVICES'     => $services,
		'TEACHERS'     => $teachers,
		'KIDS_GROUPS'  => $kids,
		'SCHEDULE'     => $schedule,
		'AWARDS'       => $awards,
		'REVIEWS'      => $reviews,
		'EVENTS'       => $events,
		'GALLERY'      => $gallery,
		'GALLERY_CATS' => array_values( array_filter( array_map( 'trim', explode( ',', (string) p313_option( 'gallery_cats', 'Все,Конкурсы,Мероприятия,Отчётные,Будни' ) ) ) ) ),
		'GALLERY_YEARS'=> ( function () {
			$custom = array_values( array_filter( array_map( 'trim', explode( ',', (string) p313_option( 'gallery_years', '' ) ) ) ) );
			return $custom ? $custom : p313_year_filter_list();
		} )(),
		'YEAR_RANGE'   => array_values( p313_year_choices() ),
		'YEAR_VISIBLE' => 2020,
		'FAQ'          => $faq,
		'BLOG'         => $blog,
		'TICKER'       => $ticker_out,
		'FOUNDER'      => array(
			'name'       => p313_option( 'founder_name', 'Анна Волкова' ),
			'role'       => p313_option( 'founder_role', '' ),
			'exp'        => p313_option( 'founder_exp', '' ),
			'education'  => p313_option( 'founder_education', '' ),
			'short'      => p313_option( 'founder_short', '' ),
			'bio'        => $bio_out,
			'facts'      => $founder_facts_out,
			'moreUrl'    => p313_founder_more_url(),
			'photoUrl'   => p313_img_url( p313_option( 'founder_photo' ), 'large' ) ?: p313_asset( 'assets/img/founder.webp' ),
		),
		'strings'      => array(
			'formSuccess' => p313_option( 'form_success_text', 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для просмотра.' ),
			'formSubmit'  => p313_option( 'form_submit_label', 'Записаться на просмотр' ),
			'formCall'    => p313_option( 'form_call_label', 'Позвонить' ),
		),
	);
}
