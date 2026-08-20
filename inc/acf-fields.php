<?php
/**
 * Local ACF field definitions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$field = function ( $key, $label, $name, $type = 'text', $extra = array() ) {
			return array_merge(
				array(
					'key'   => 'field_p313_' . $key,
					'label' => $label,
					'name'  => $name,
					'type'  => $type,
				),
				$extra
			);
		};
		$tab = function ( $key, $label ) use ( $field ) {
			return $field( $key, $label, '', 'tab' );
		};
		$repeater = function ( $key, $label, $name, $sub_fields, $extra = array() ) use ( $field ) {
			return $field(
				$key,
				$label,
				$name,
				'repeater',
				array_merge(
					array(
						'layout'       => 'table',
						'button_label' => 'Добавить',
						'sub_fields'   => $sub_fields,
					),
					$extra
				)
			);
		};
		$options_location = function ( $slug ) {
			return array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => $slug ) ) );
		};
		$add = function ( $key, $title, $fields, $location ) {
			acf_add_local_field_group(
				array(
					'key'      => 'group_p313_' . $key,
					'title'    => $title,
					'fields'   => $fields,
					'location' => $location,
					'style'    => 'default',
				)
			);
		};

		$add(
			'general',
			'Project 313 — Основные настройки',
			array(
				$tab( 'general_contacts_tab', 'Контакты' ),
				$field( 'org_name', 'Название организации', 'org_name' ),
				$field( 'phone', 'Телефон', 'phone', 'text', array( 'default_value' => '+7 961 884 14 74' ) ),
				$field( 'phone_href', 'Телефон для ссылки', 'phone_href', 'text', array( 'instructions' => 'Например: tel:+79618841474', 'default_value' => 'tel:+79618841474' ) ),
				$field( 'email', 'E-mail', 'email', 'email' ),
				$field( 'address', 'Адрес', 'address' ),
				$field( 'city', 'Город', 'city' ),
				$field( 'work_hours', 'Часы работы', 'work_hours' ),
				$tab( 'general_socials_tab', 'Соцсети' ),
				$repeater(
					'socials',
					'Социальные сети',
					'socials',
					array(
						$field( 'social_label', 'Название', 'label' ),
						$field( 'social_url', 'Ссылка', 'url', 'url' ),
					)
				),
				$tab( 'general_map_tab', 'Карта' ),
				$field( 'map_embed', 'Код карты', 'map_embed', 'textarea', array( 'rows' => 5, 'instructions' => 'Если пусто, карта строится автоматически по филиалам с координатами.' ) ),
				$tab( 'general_facts_tab', 'Hero facts' ),
				$repeater(
					'hero_facts',
					'Факты',
					'hero_facts',
					array(
						$field( 'hero_fact_n', 'Число', 'n' ),
						$field( 'hero_fact_label', 'Подпись', 'label' ),
					)
				),
				$tab( 'general_ages_tab', 'Возраста услуг' ),
				$repeater(
					'service_ages',
					'Возрастные фильтры',
					'service_ages',
					array(
						$field( 'service_age_id', 'ID', 'id' ),
						$field( 'service_age_label', 'Подпись', 'label' ),
					)
				),
				$tab( 'general_ticker_tab', 'Бегущая строка' ),
				$repeater( 'ticker_items', 'Элементы строки', 'ticker_items', array( $field( 'ticker_text', 'Текст', 'text' ) ) ),
				$tab( 'general_founder_tab', 'Руководитель' ),
				$field( 'founder_name', 'Имя', 'founder_name' ),
				$field( 'founder_role', 'Роль', 'founder_role' ),
				$field( 'founder_exp', 'Опыт', 'founder_exp' ),
				$field( 'founder_education', 'Образование', 'founder_education', 'textarea', array( 'rows' => 3 ) ),
				$field( 'founder_short', 'Краткое описание', 'founder_short', 'textarea', array( 'rows' => 4 ) ),
				$field( 'founder_photo', 'Фото', 'founder_photo', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
				$field( 'founder_more_url', 'Ссылка «Подробнее»', 'founder_more_url', 'url', array( 'default_value' => 'https://project313.ru/founder/' ) ),
				$repeater( 'founder_bio', 'Биография', 'founder_bio', array( $field( 'founder_bio_paragraph', 'Абзац', 'paragraph', 'textarea', array( 'rows' => 5 ) ) ), array( 'layout' => 'block', 'button_label' => 'Добавить абзац' ) ),
				$repeater(
					'founder_facts',
					'Факты о руководителе',
					'founder_facts',
					array(
						$field( 'founder_fact_n', 'Число', 'n' ),
						$field( 'founder_fact_label', 'Подпись', 'label' ),
					)
				),
				$tab( 'general_gallery_tab', 'Галерея фильтры' ),
				$field( 'gallery_cats', 'Категории', 'gallery_cats', 'text', array( 'instructions' => 'Через запятую. Эти же категории появятся в выборе у каждого фото.' ) ),
				$field( 'gallery_years', 'Годы (необязательно)', 'gallery_years', 'text', array( 'instructions' => 'Если пусто, на сайте показываются годы с текущего до 2016. Иначе — список через запятую, начиная с «Все».' ) ),
				$tab( 'general_notify_tab', 'Уведомления' ),
				$field( 'notify_email', 'E-mail для уведомлений', 'notify_email', 'email' ),
				$field( 'notify_subject', 'Тема уведомления', 'notify_subject' ),
			),
			$options_location( 'p313-settings' )
		);

		$add(
			'header_footer',
			'Project 313 — Шапка и подвал',
			array(
				$field( 'logo', 'Логотип', 'logo', 'image', array( 'return_format' => 'id' ) ),
				$field( 'logo_white', 'Белый логотип', 'logo_white', 'image', array( 'return_format' => 'id' ) ),
				$field( 'logo_sub', 'Подпись логотипа', 'logo_sub' ),
				$field( 'cta_label', 'Текст главной кнопки', 'cta_label' ),
				$field( 'nav_home', 'Меню: Главная', 'nav_home' ),
				$field( 'nav_team', 'Меню: Коллектив', 'nav_team' ),
				$field( 'nav_services', 'Меню: Услуги', 'nav_services' ),
				$field( 'nav_schedule', 'Меню: Расписание', 'nav_schedule' ),
				$field( 'nav_awards', 'Меню: Награды', 'nav_awards' ),
				$field( 'nav_gallery', 'Меню: Галерея', 'nav_gallery' ),
				$field( 'nav_blog', 'Меню: Блог', 'nav_blog' ),
				$field( 'nav_events', 'Меню: Мероприятия', 'nav_events' ),
				$field( 'nav_contacts', 'Меню: Контакты', 'nav_contacts' ),
				$field( 'footer_link_team', 'Подвал: ссылка «Коллектив»', 'footer_link_team' ),
				$field( 'footer_about', 'О студии в подвале', 'footer_about', 'textarea', array( 'rows' => 3 ) ),
				$field( 'footer_kicker', 'Подзаголовок подвала', 'footer_kicker', 'textarea', array( 'rows' => 2 ) ),
				$field( 'footer_title', 'Заголовок подвала', 'footer_title', 'textarea', array( 'rows' => 2 ) ),
				$field( 'footer_text', 'Текст подвала', 'footer_text', 'textarea', array( 'rows' => 4 ) ),
				$field( 'footer_col_menu', 'Заголовок колонки меню', 'footer_col_menu' ),
				$field( 'footer_col_contacts', 'Заголовок колонки контактов', 'footer_col_contacts' ),
				$field( 'footer_col_social', 'Заголовок колонки соцсетей', 'footer_col_social' ),
				$field( 'copyright', 'Копирайт', 'copyright' ),
			),
			$options_location( 'p313-header-footer' )
		);

		$add(
			'form',
			'Project 313 — Форма записи',
			array(
				$field( 'form_modal_kicker', 'Подзаголовок окна', 'form_modal_kicker' ),
				$field( 'form_modal_title', 'Заголовок окна', 'form_modal_title' ),
				$field( 'form_modal_text', 'Текст окна', 'form_modal_text', 'textarea', array( 'rows' => 3 ) ),
				$field( 'form_success_title', 'Заголовок успеха', 'form_success_title' ),
				$field( 'form_success_text', 'Текст успеха', 'form_success_text', 'textarea', array( 'rows' => 3 ) ),
				$field( 'form_submit_label', 'Текст кнопки', 'form_submit_label' ),
				$field( 'form_call_label', 'Текст кнопки «Позвонить»', 'form_call_label' ),
				$field( 'form_note', 'Примечание к форме', 'form_note' ),
				$repeater( 'form_directions', 'Направления', 'form_directions', array( $field( 'form_direction_label', 'Название', 'label' ) ) ),
			),
			$options_location( 'p313-form' )
		);

		$add(
			'smtp',
			'Project 313 — SMTP',
			array(
				$field( 'smtp_enabled', 'Использовать SMTP', 'smtp_enabled', 'true_false', array( 'ui' => 1 ) ),
				$field( 'smtp_host', 'SMTP-хост', 'smtp_host' ),
				$field( 'smtp_port', 'SMTP-порт', 'smtp_port' ),
				$field( 'smtp_user', 'Пользователь', 'smtp_user' ),
				$field( 'smtp_pass', 'Пароль', 'smtp_pass', 'password' ),
				$field( 'smtp_from_email', 'E-mail отправителя', 'smtp_from_email', 'email' ),
				$field( 'smtp_from_name', 'Имя отправителя', 'smtp_from_name' ),
				$field( 'smtp_secure', 'Шифрование', 'smtp_secure', 'select', array( 'choices' => array( '' => 'Нет', 'tls' => 'TLS', 'ssl' => 'SSL' ), 'allow_null' => 1 ) ),
				$field( 'smtp_auth', 'SMTP-авторизация', 'smtp_auth', 'true_false', array( 'ui' => 1 ) ),
			),
			$options_location( 'p313-smtp' )
		);

		$add(
			'vk',
			'Project 313 — VK уведомления',
			array(
				$field( 'vk_enabled', 'Включить уведомления VK', 'vk_enabled', 'true_false', array( 'ui' => 1 ) ),
				$field( 'vk_token', 'Токен сообщества', 'vk_token', 'password' ),
				$field( 'vk_peer_id', 'Peer ID', 'vk_peer_id' ),
				$field( 'vk_api_version', 'Версия API', 'vk_api_version' ),
				$field( 'vk_help', 'Инструкция', 'vk_help', 'textarea', array( 'readonly' => 1, 'default_value' => 'Создайте токен сообщества VK с правом messages и укажите ID диалога или пользователя.' ) ),
			),
			$options_location( 'p313-vk' )
		);

		$add(
			'seo',
			'Project 313 — SEO по умолчанию',
			array(
				$field( 'seo_default_title', 'Заголовок по умолчанию', 'seo_default_title' ),
				$field( 'seo_default_description', 'Описание по умолчанию', 'seo_default_description', 'textarea', array( 'rows' => 3 ) ),
				$field( 'seo_default_image', 'Изображение по умолчанию', 'seo_default_image', 'image', array( 'return_format' => 'id' ) ),
				$field( 'seo_robots', 'Robots', 'seo_robots' ),
			),
			$options_location( 'p313-seo' )
		);

		$add(
			'seo_singular',
			'Project 313 — SEO страницы',
			array(
				$field( 'singular_seo_title', 'SEO title', 'seo_title' ),
				$field( 'singular_seo_description', 'SEO description', 'seo_description', 'textarea', array( 'rows' => 3 ) ),
				$field( 'singular_seo_image', 'SEO image', 'seo_image', 'image', array( 'return_format' => 'id' ) ),
				$field( 'singular_seo_robots', 'Robots', 'seo_robots' ),
			),
			array(
				array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
				array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ),
				array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'p313_kids' ) ),
			)
		);

		$cpt_fields = array(
			'p313_service' => array(
				'Услуга',
				array(
					$field( 'service_key', 'Ключ', 'service_key' ),
					$field( 'service_price', 'Стоимость', 'price' ),
					$field( 'service_format', 'Формат на странице услуг', 'format', 'select', array( 'choices' => array( 'group' => 'Групповая', 'individual' => 'Индивидуальная' ), 'required' => 1, 'instructions' => 'По этому полю услуга попадает в фильтр «Групповые / Индивидуальные».' ) ),
					$field(
						'service_kids_group',
						'Группа',
						'kids_group',
						'select',
						array(
							'choices'      => p313_kids_group_choices(),
							'allow_null'   => 1,
							'ui'           => 1,
							'instructions' => 'Список берётся из раздела «Группы». На карточке услуги показывается название выбранной группы.',
						)
					),
					$field( 'service_duration', 'Длительность', 'duration' ),
					$field( 'service_photos', 'Фотографии', 'photos', 'gallery', array( 'return_format' => 'id', 'preview_size' => 'medium', 'insert' => 'append', 'library' => 'all', 'instructions' => 'Можно загрузить несколько фото — в карточке услуги появится слайдер.' ) ),
					$field( 'service_photo_id', 'ID фото Unsplash', 'photo_id', 'text', array( 'instructions' => 'Запасной вариант, если нет загруженных фото.' ) ),
					$field( 'service_short', 'Краткое описание', 'short' ),
					$field( 'service_text', 'Описание', 'text', 'textarea', array( 'rows' => 5 ) ),
				),
			),
			'p313_teacher' => array(
				'Педагог',
				array(
					$field( 'teacher_key', 'Ключ', 'teacher_key' ),
					$field( 'teacher_role', 'Роль / направление', 'role' ),
					$field( 'teacher_exp', 'Опыт', 'exp', 'textarea', array( 'rows' => 2, 'instructions' => 'Текст под заголовком «Опыт» в карточке.' ) ),
					$field( 'teacher_education', 'Образование', 'education', 'textarea', array( 'rows' => 3, 'instructions' => 'Текст под заголовком «Образование» в карточке.' ) ),
					$field( 'teacher_bio', 'О преподавателе', 'bio', 'textarea', array( 'rows' => 8, 'instructions' => 'Основной текст карточки. Можно писать подробнее.' ) ),
					$field( 'teacher_is_leader', 'Это руководитель', 'is_leader', 'true_false', array( 'ui' => 1, 'instructions' => 'Добавляет кнопки «Записаться» и «Подробнее».' ) ),
					$field( 'teacher_more_url', 'Ссылка «Подробнее»', 'more_url', 'url', array( 'instructions' => 'Если пусто — https://project313.ru/founder/' ) ),
					$field( 'teacher_photo_id', 'ID фото Unsplash', 'photo_id' ),
				),
			),
			'p313_kids' => array(
				'Группа',
				array(
					$field( 'kids_age', 'Возраст', 'age' ),
					$field(
						'kids_level',
						'Ступень',
						'level',
						'select',
						array(
							'choices' => array(
								'junior' => 'Младшая',
								'middle' => 'Средняя',
								'senior' => 'Старшая',
							),
							'allow_null' => 1,
							'instructions' => 'Для младшей и средней на странице группы появится переключатель филиалов.',
						)
					),
					$field( 'kids_note', 'Описание', 'note', 'textarea', array( 'rows' => 3 ) ),
					$field( 'kids_members_title', 'Заголовок блока участниц', 'members_title' ),
					$field( 'kids_photo', 'Общая фотография группы', 'photo', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
					$repeater(
						'kids_members',
						'Участницы',
						'members',
						array(
							$field( 'kids_member_name', 'Имя', 'name' ),
							$field( 'kids_member_photo', 'Фото', 'photo', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
							$field(
								'kids_member_branch',
								'Филиал',
								'branch',
								'select',
								array(
									'choices' => array(
										''       => 'Оба филиала',
										'krasny' => 'Красный Путь',
										'khimik' => 'Химик',
									),
									'allow_null' => 1,
								)
							),
						),
						array(
							'layout'       => 'block',
							'button_label' => 'Добавить участницу',
						)
					),
				),
			),
			'p313_branch' => array(
				'Филиал',
				array(
					$field( 'branch_key', 'Ключ', 'branch_key', 'select', array( 'choices' => array( 'krasny' => 'krasny (Красный Путь)', 'khimik' => 'khimik (Химик)' ), 'allow_null' => 1 ) ),
					$field( 'branch_address', 'Адрес', 'address' ),
					$field( 'branch_phone', 'Телефон', 'phone' ),
					$field( 'branch_lat', 'Широта', 'lat' ),
					$field( 'branch_lng', 'Долгота', 'lng' ),
				),
			),
			'p313_schedule' => array(
				'Занятие',
				array(
					$field( 'schedule_time', 'Время', 'time', 'text', array( 'placeholder' => '17:00', 'instructions' => 'Формат: 17:00' ) ),
					$field( 'schedule_day', 'День недели', 'day', 'select', array( 'choices' => p313_weekday_choices(), 'allow_null' => 1 ) ),
					$field(
						'schedule_service',
						'Направление',
						'service',
						'post_object',
						array(
							'post_type'      => array( 'p313_service' ),
							'return_format'  => 'id',
							'allow_null'     => 1,
							'ui'             => 1,
						)
					),
					$field(
						'schedule_group',
						'Группа',
						'group',
						'post_object',
						array(
							'post_type'     => array( 'p313_kids' ),
							'return_format' => 'id',
							'allow_null'    => 1,
							'ui'            => 1,
						)
					),
					$field(
						'schedule_teacher_ref',
						'Педагог',
						'teacher_ref',
						'post_object',
						array(
							'post_type'     => array( 'p313_teacher' ),
							'return_format' => 'id',
							'allow_null'    => 1,
							'ui'            => 1,
						)
					),
					$field(
						'schedule_branch',
						'Филиал',
						'branch',
						'post_object',
						array(
							'post_type'     => array( 'p313_branch' ),
							'return_format' => 'id',
							'allow_null'    => 1,
							'ui'            => 1,
						)
					),
				),
			),
			'p313_award' => array(
				'Награда',
				array(
					$field( 'award_year', 'Год', 'year', 'select', array( 'choices' => p313_year_choices(), 'required' => 1, 'instructions' => 'Год используется в таблице и в фильтре на странице наград.' ) ),
					$field( 'award_result', 'Результат', 'result', 'text', array( 'instructions' => 'Например: Гран-при, Лауреат I степени, Диплом.' ) ),
					$field( 'award_contest', 'Конкурс', 'contest', 'text', array( 'instructions' => 'Если пусто — берётся заголовок записи.' ) ),
					$field( 'award_age', 'Возраст', 'age' ),
					$field( 'award_date', 'Дата', 'date' ),
					$field( 'award_qty', 'Кол-во', 'qty' ),
					$field( 'award_place', 'Место (устаревшее)', 'place', 'text', array( 'instructions' => 'Старое поле. Лучше заполняйте «Результат».' ) ),
					$field( 'award_level', 'Уровень (устаревшее)', 'level', 'text', array( 'instructions' => 'Старое поле, можно оставить пустым.' ) ),
				),
			),
			'p313_review' => array(
				'Отзыв',
				array(
					$field( 'review_role', 'Автор / подпись', 'role' ),
					$field( 'review_rating', 'Оценка', 'rating', 'number', array( 'min' => 1, 'max' => 5 ) ),
					$field( 'review_text', 'Текст', 'text', 'textarea', array( 'rows' => 4 ) ),
					$field(
						'review_video',
						'Видео VK',
						'video',
						'text',
						array(
							'instructions' => 'Ссылка на видео VK (vk.com/video… или vkvideo.ru/…) либо код iframe. На сайте появится встроенный плеер.',
						)
					),
				),
			),
			'p313_event' => array( 'Мероприятие', array( $field( 'event_date_label', 'Дата', 'date_label' ), $field( 'event_time', 'Время', 'time' ), $field( 'event_place', 'Место', 'place' ), $field( 'event_photo_id', 'ID фото Unsplash', 'photo_id' ), $field( 'event_excerpt', 'Краткое описание', 'excerpt' ) ) ),
			'p313_gallery' => array(
				'Альбом галереи',
				array(
					$field( 'gallery_year', 'Год', 'year', 'select', array( 'choices' => p313_year_choices(), 'required' => 1, 'instructions' => 'Тот же список лет, что и в фильтре на сайте (до 2016).' ) ),
					$field( 'gallery_category', 'Категория', 'category', 'select', array( 'choices' => p313_gallery_category_choices(), 'required' => 1, 'instructions' => 'Категории задаются в Project 313 → Галерея фильтры.' ) ),
					$field(
						'gallery_photos',
						'Фотографии',
						'photos',
						'gallery',
						array(
							'return_format' => 'id',
							'preview_size'  => 'medium',
							'insert'        => 'append',
							'library'       => 'all',
							'instructions'  => 'Можно загрузить сразу 20–30+ фото в одно событие (например «Отчётный концерт 2026»).',
						)
					),
					$field( 'gallery_photo_id', 'ID фото Unsplash', 'photo_id', 'text', array( 'instructions' => 'Запасной вариант, если нет загруженных фото.' ) ),
					$field( 'gallery_ratio', 'Формат', 'ratio', 'select', array( 'choices' => array( 'tall' => 'Вертикальный', 'wide' => 'Горизонтальный', 'square' => 'Квадрат' ) ) ),
				),
			),
			'p313_faq' => array( 'Вопрос FAQ', array( $field( 'faq_answer', 'Ответ', 'answer', 'textarea', array( 'rows' => 4 ) ) ) ),
			'post' => array( 'Статья блога', array( $field( 'post_read_time', 'Время чтения', 'read_time' ), $field( 'post_photo_id', 'ID фото Unsplash', 'photo_id' ), $field( 'post_card_excerpt', 'Описание карточки', 'card_excerpt', 'textarea', array( 'rows' => 3 ) ) ) ),
		);
		foreach ( $cpt_fields as $post_type => $config ) {
			$add( $post_type, 'Project 313 — ' . $config[0], $config[1], array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $post_type ) ) ) );
		}

		$head_fields = function ( $prefix, $form = false ) use ( $field ) {
			$fields = array(
				$field( $prefix . '_label', 'Hero: подпись', $prefix . '_label' ),
				$field( $prefix . '_title', 'Hero: заголовок', $prefix . '_title' ),
				$field( $prefix . '_sub', 'Hero: текст', $prefix . '_sub', 'textarea', array( 'rows' => 4 ) ),
				$field( $prefix . '_text', 'Hero: дополнительный текст', $prefix . '_text', 'wysiwyg', array( 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0 ) ),
				$field( $prefix . '_image', 'Hero: изображение', $prefix . '_image', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
			);
			if ( $form ) {
				$fields[] = $field( $prefix . '_form_title', 'Заголовок формы', $prefix . '_form_title' );
				$fields[] = $field( $prefix . '_form_text', 'Текст формы', $prefix . '_form_text', 'textarea', array( 'rows' => 3 ) );
			}
			return $fields;
		};
		$home_fields = array(
			$tab( 'page_home_hero_tab', 'Hero' ),
			$field( 'page_home_hero_num', 'Hero: номер', 'page_home_hero_num' ),
			$field( 'page_home_hero_label', 'Hero: подпись', 'page_home_hero_label' ),
			$field( 'page_home_hero_title', 'Hero: заголовок', 'page_home_hero_title', 'textarea', array( 'rows' => 3 ) ),
			$field( 'page_home_hero_text', 'Hero: текст', 'page_home_hero_text', 'textarea', array( 'rows' => 3 ) ),
			$field( 'page_home_hero_cta_primary', 'Hero: основная кнопка', 'page_home_hero_cta_primary' ),
			$field( 'page_home_hero_cta_secondary', 'Hero: вторичная кнопка', 'page_home_hero_cta_secondary' ),
			$field( 'page_home_hero_badge_n', 'Hero: число бейджа', 'page_home_hero_badge_n' ),
			$field( 'page_home_hero_badge_t', 'Hero: текст бейджа', 'page_home_hero_badge_t' ),
			$field( 'page_home_hero_image', 'Hero: изображение', 'page_home_hero_image', 'image', array( 'return_format' => 'id' ) ),
			$tab( 'page_home_about_tab', 'О студии' ),
			$field( 'page_home_about_num', 'О нас: номер', 'page_home_about_num' ),
			$field( 'page_home_about_label', 'О нас: подпись', 'page_home_about_label' ),
			$field( 'page_home_about_title', 'О нас: заголовок', 'page_home_about_title', 'textarea', array( 'rows' => 3 ) ),
			$field( 'page_home_about_text1', 'О нас: текст 1', 'page_home_about_text1', 'textarea', array( 'rows' => 4 ) ),
			$field( 'page_home_about_text2', 'О нас: текст 2', 'page_home_about_text2', 'textarea', array( 'rows' => 4 ) ),
			$field( 'page_home_founder_label', 'Руководитель: подпись', 'page_home_founder_label' ),
			$field( 'page_home_founder_name', 'Руководитель: имя', 'page_home_founder_name' ),
			$field( 'page_home_founder_text', 'Руководитель: текст', 'page_home_founder_text', 'textarea', array( 'rows' => 3 ) ),
			$field( 'page_home_founder_image', 'Руководитель: фото', 'page_home_founder_image', 'image', array( 'return_format' => 'id' ) ),
			$field( 'page_home_founder_cta', 'Руководитель: кнопка записи', 'page_home_founder_cta' ),
			$field( 'page_home_founder_link', 'Руководитель: кнопка «Подробнее»', 'page_home_founder_link' ),
			$field( 'page_home_founder_url', 'Руководитель: ссылка «Подробнее»', 'page_home_founder_url', 'url' ),
			$tab( 'page_home_sections_tab', 'Секции' ),
			$field( 'page_home_services_num', 'Услуги: номер', 'page_home_services_num' ),
			$field( 'page_home_services_label', 'Услуги: подпись', 'page_home_services_label' ),
			$field( 'page_home_services_title', 'Услуги: заголовок', 'page_home_services_title' ),
			$field( 'page_home_services_link', 'Услуги: ссылка', 'page_home_services_link' ),
			$field( 'page_home_schedule_num', 'Расписание: номер', 'page_home_schedule_num' ),
			$field( 'page_home_schedule_label', 'Расписание: подпись', 'page_home_schedule_label' ),
			$field( 'page_home_schedule_title', 'Расписание: заголовок', 'page_home_schedule_title' ),
			$field( 'page_home_schedule_sub', 'Расписание: текст', 'page_home_schedule_sub', 'textarea', array( 'rows' => 3 ) ),
			$field( 'page_home_schedule_link', 'Расписание: кнопка', 'page_home_schedule_link' ),
			$field( 'page_home_gallery_num', 'Галерея: номер', 'page_home_gallery_num' ),
			$field( 'page_home_gallery_label', 'Галерея: подпись', 'page_home_gallery_label' ),
			$field( 'page_home_gallery_title', 'Галерея: заголовок', 'page_home_gallery_title' ),
			$field( 'page_home_gallery_link', 'Галерея: ссылка', 'page_home_gallery_link' ),
			$field( 'page_home_gallery_image_1', 'Галерея: фото 1', 'page_home_gallery_image_1', 'image', array( 'return_format' => 'id' ) ),
			$field( 'page_home_gallery_image_2', 'Галерея: фото 2', 'page_home_gallery_image_2', 'image', array( 'return_format' => 'id' ) ),
			$field( 'page_home_blog_num', 'Блог: номер', 'page_home_blog_num' ),
			$field( 'page_home_blog_label', 'Блог: подпись', 'page_home_blog_label' ),
			$field( 'page_home_blog_title', 'Блог: заголовок', 'page_home_blog_title' ),
			$field( 'page_home_reviews_num', 'Отзывы: номер', 'page_home_reviews_num' ),
			$field( 'page_home_reviews_label', 'Отзывы: подпись', 'page_home_reviews_label' ),
			$field( 'page_home_reviews_title', 'Отзывы: заголовок', 'page_home_reviews_title' ),
			$field( 'page_home_faq_num', 'FAQ: номер', 'page_home_faq_num' ),
			$field( 'page_home_faq_label', 'FAQ: подпись', 'page_home_faq_label' ),
			$field( 'page_home_faq_title', 'FAQ: заголовок', 'page_home_faq_title' ),
			$field( 'page_home_faq_sub', 'FAQ: подсказка', 'page_home_faq_sub', 'textarea', array( 'rows' => 2 ) ),
		);
		$add(
			'page_home',
			'Project 313 — Главная',
			$home_fields,
			array(
				array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ),
				array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-home.php' ) ),
				array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'template-home.php' ) ),
			)
		);

		$template_configs = array(
			'template-team.php'     => array( 'page_team', 'Коллектив', true, false ),
			'template-services.php' => array( 'page_services', 'Услуги', false, false ),
			'template-schedule.php' => array( 'page_schedule', 'Расписание', false, false ),
			'template-awards.php'   => array( 'page_awards', 'Награды', false, false ),
			'template-gallery.php'  => array( 'page_gallery', 'Галерея', false, false ),
			'template-blog.php'     => array( 'page_blog', 'Блог', false, false ),
			'template-events.php'   => array( 'page_events', 'Мероприятия', false, false ),
			'template-contacts.php' => array( 'page_contacts', 'Контакты', false, true ),
			'template-founder.php'  => array( 'page_founder', 'Руководитель', false, false ),
			'template-reviews.php'  => array( 'page_reviews', 'Отзывы', false, true ),
		);
		foreach ( $template_configs as $template => $config ) {
			$fields = $head_fields( $config[0], $config[3] );
			if ( $config[2] ) {
				$fields[] = $field( 'page_team_tab_teachers', 'Вкладка «Педагоги»', 'page_team_tab_teachers' );
				$fields[] = $field( 'page_team_tab_kids', 'Вкладка «Группа»', 'page_team_tab_kids' );
			}
			if ( 'page_awards' === $config[0] ) {
				$fields[] = $field( 'page_awards_photos', 'Фото справа от заголовка', 'page_awards_photos', 'gallery', array( 'return_format' => 'id', 'preview_size' => 'medium', 'insert' => 'append', 'library' => 'all' ) );
			}
			if ( 'page_contacts' === $config[0] ) {
				$fields[] = $field( 'page_contacts_phone_label', 'Подпись телефона', 'page_contacts_phone_label' );
				$fields[] = $field( 'page_contacts_vk_label', 'Подпись VK', 'page_contacts_vk_label' );
				$fields[] = $field( 'page_contacts_hours_label', 'Подпись часов работы', 'page_contacts_hours_label' );
			}
			if ( 'page_founder' === $config[0] ) {
				$fields[] = $field( 'page_founder_photo', 'Фото руководителя', 'page_founder_photo', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Главное фото на странице биографии. Сохраняется также в общих настройках.' ) );
				$fields[] = $field( 'page_founder_role', 'Роль', 'page_founder_role' );
				$fields[] = $field( 'page_founder_exp', 'Опыт', 'page_founder_exp' );
				$fields[] = $field( 'page_founder_education', 'Образование', 'page_founder_education', 'textarea', array( 'rows' => 3 ) );
				$fields[] = $field( 'page_founder_bio', 'Биография на странице', 'page_founder_bio', 'wysiwyg', array( 'tabs' => 'visual', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Если заполнено, заменяет абзацы биографии из настроек «Руководитель».' ) );
				$fields[] = $repeater(
					'page_founder_facts',
					'Цифры / факты',
					'page_founder_facts',
					array(
						$field( 'page_founder_fact_n', 'Число', 'n' ),
						$field( 'page_founder_fact_label', 'Подпись', 'label' ),
					),
					array(
						'layout'       => 'table',
						'button_label' => 'Добавить факт',
						'instructions' => 'Редактируемые цифры в карточке биографии (например 11+, 300+, 58+).',
					)
				);
			}
			$add(
				$config[0],
				'Project 313 — ' . $config[1],
				$fields,
				array(
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/' . $template ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => $template ) ),
				)
			);
		}
	}
);

add_filter(
	'acf/load_field/key=field_p313_award_year',
	function ( $field ) {
		$field['choices'] = p313_year_choices();
		return $field;
	}
);
add_filter(
	'acf/load_field/key=field_p313_gallery_year',
	function ( $field ) {
		$field['choices'] = p313_year_choices();
		return $field;
	}
);
add_filter(
	'acf/load_field/key=field_p313_gallery_category',
	function ( $field ) {
		$field['choices'] = p313_gallery_category_choices();
		return $field;
	}
);
add_filter(
	'acf/load_field/key=field_p313_service_kids_group',
	function ( $field ) {
		$field['choices'] = p313_kids_group_choices();
		return $field;
	}
);

/**
 * Keep founder page fields in sync with Project 313 → Руководитель options.
 */
add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( ! is_numeric( $post_id ) || 'page' !== get_post_type( $post_id ) ) {
			return;
		}
		$template = get_page_template_slug( (int) $post_id );
		if ( ! in_array( $template, array( 'page-templates/template-founder.php', 'template-founder.php' ), true ) ) {
			return;
		}
		if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
			return;
		}

		$title = trim( (string) get_field( 'page_founder_title', $post_id ) );
		if ( $title ) {
			update_field( 'founder_name', $title, 'option' );
		}
		$sub = trim( (string) get_field( 'page_founder_sub', $post_id ) );
		if ( $sub ) {
			update_field( 'founder_short', $sub, 'option' );
		}

		$map = array(
			'page_founder_photo'     => 'founder_photo',
			'page_founder_role'      => 'founder_role',
			'page_founder_exp'       => 'founder_exp',
			'page_founder_education' => 'founder_education',
		);
		foreach ( $map as $page_key => $option_key ) {
			$value = get_field( $page_key, $post_id );
			if ( $value === null || $value === false || $value === '' ) {
				continue;
			}
			update_field( $option_key, $value, 'option' );
		}

		$facts = get_field( 'page_founder_facts', $post_id );
		if ( is_array( $facts ) ) {
			$clean = array();
			foreach ( $facts as $row ) {
				$n     = trim( (string) ( $row['n'] ?? '' ) );
				$label = trim( (string) ( $row['label'] ?? '' ) );
				if ( '' === $n && '' === $label ) {
					continue;
				}
				$clean[] = array(
					'n'     => $n,
					'label' => $label,
				);
			}
			if ( $clean ) {
				update_field( 'founder_facts', $clean, 'option' );
			}
		}
	},
	20
);

/**
 * Prefill founder page fields from options when empty.
 */
add_filter(
	'acf/load_value',
	function ( $value, $post_id, $field ) {
		if ( ! is_numeric( $post_id ) || empty( $field['name'] ) ) {
			return $value;
		}
		$name = $field['name'];
		$map  = array(
			'page_founder_photo'     => 'founder_photo',
			'page_founder_role'      => 'founder_role',
			'page_founder_exp'       => 'founder_exp',
			'page_founder_education' => 'founder_education',
			'page_founder_facts'     => 'founder_facts',
			'page_founder_title'     => 'founder_name',
			'page_founder_sub'       => 'founder_short',
		);
		if ( ! isset( $map[ $name ] ) ) {
			return $value;
		}
		if ( $value !== null && $value !== false && $value !== '' && ! ( is_array( $value ) && ! $value ) ) {
			return $value;
		}
		return p313_option( $map[ $name ], $value );
	},
	10,
	3
);
