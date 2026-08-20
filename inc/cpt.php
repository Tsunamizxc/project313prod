<?php
/**
 * Custom post types.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		$types = array(
			'p313_service'  => array(
				'label'     => 'Услуги',
				'singular'  => 'Услуга',
				'menu_icon' => 'dashicons-welcome-learn-more',
				'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
			),
			'p313_teacher'  => array(
				'label'     => 'Педагоги',
				'singular'  => 'Педагог',
				'menu_icon' => 'dashicons-groups',
				'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
			),
			'p313_kids'     => array(
				'label'     => 'Группы',
				'singular'  => 'Группа',
				'menu_icon' => 'dashicons-buddicons-activity',
				'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
				'public'    => true,
				'rewrite'   => array( 'slug' => 'gruppa', 'with_front' => false ),
			),
			'p313_branch'   => array(
				'label'     => 'Филиалы',
				'singular'  => 'Филиал',
				'menu_icon' => 'dashicons-location',
				'supports'  => array( 'title', 'page-attributes' ),
			),
			'p313_schedule' => array(
				'label'     => 'Расписание',
				'singular'  => 'Занятие',
				'menu_icon' => 'dashicons-calendar-alt',
				'supports'  => array( 'title', 'page-attributes' ),
			),
			'p313_award'    => array(
				'label'     => 'Награды',
				'singular'  => 'Награда',
				'menu_icon' => 'dashicons-awards',
				'supports'  => array( 'title', 'page-attributes' ),
			),
			'p313_review'   => array(
				'label'     => 'Отзывы',
				'singular'  => 'Отзыв',
				'menu_icon' => 'dashicons-format-quote',
				'supports'  => array( 'title', 'page-attributes' ),
			),
			'p313_event'    => array(
				'label'     => 'Мероприятия',
				'singular'  => 'Мероприятие',
				'menu_icon' => 'dashicons-tickets-alt',
				'supports'  => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
			),
			'p313_gallery'  => array(
				'label'     => 'Галерея',
				'singular'  => 'Событие / альбом',
				'menu_icon' => 'dashicons-format-gallery',
				'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
			),
			'p313_faq'      => array(
				'label'     => 'FAQ',
				'singular'  => 'Вопрос',
				'menu_icon' => 'dashicons-editor-help',
				'supports'  => array( 'title', 'page-attributes' ),
			),
		);

		foreach ( $types as $slug => $cfg ) {
			$is_public = ! empty( $cfg['public'] );
			register_post_type(
				$slug,
				array(
					'labels'              => array(
						'name'          => $cfg['label'],
						'singular_name' => $cfg['singular'],
						'add_new_item'  => 'Добавить: ' . $cfg['singular'],
						'edit_item'     => 'Редактировать: ' . $cfg['singular'],
					),
					'public'              => $is_public,
					'publicly_queryable'  => $is_public,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'menu_icon'           => $cfg['menu_icon'],
					'supports'            => $cfg['supports'],
					'has_archive'         => false,
					'rewrite'             => $is_public ? ( $cfg['rewrite'] ?? array( 'slug' => $slug ) ) : false,
				)
			);
		}
	}
);
