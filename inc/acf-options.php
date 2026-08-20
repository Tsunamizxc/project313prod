<?php
/**
 * ACF options pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page(
			array(
				'page_title' => 'Настройки сайта',
				'menu_title' => 'Project 313',
				'menu_slug'  => 'p313-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'icon_url'   => 'dashicons-admin-customizer',
				'position'   => 3,
			)
		);

		acf_add_options_sub_page(
			array(
				'page_title'  => 'Шапка и подвал',
				'menu_title'  => 'Шапка / Подвал',
				'parent_slug' => 'p313-settings',
				'menu_slug'   => 'p313-header-footer',
			)
		);

		acf_add_options_sub_page(
			array(
				'page_title'  => 'Форма записи',
				'menu_title'  => 'Форма записи',
				'parent_slug' => 'p313-settings',
				'menu_slug'   => 'p313-form',
			)
		);

		acf_add_options_sub_page(
			array(
				'page_title'  => 'SMTP и почта',
				'menu_title'  => 'SMTP',
				'parent_slug' => 'p313-settings',
				'menu_slug'   => 'p313-smtp',
			)
		);

		acf_add_options_sub_page(
			array(
				'page_title'  => 'Уведомления VK',
				'menu_title'  => 'VK уведомления',
				'parent_slug' => 'p313-settings',
				'menu_slug'   => 'p313-vk',
			)
		);

		acf_add_options_sub_page(
			array(
				'page_title'  => 'SEO по умолчанию',
				'menu_title'  => 'SEO',
				'parent_slug' => 'p313-settings',
				'menu_slug'   => 'p313-seo',
			)
		);
	}
);
