<?php
/**
 * Default Project 313 content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function p313_seed_update_fields( $post_id, $fields ) {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	foreach ( $fields as $name => $value ) {
		update_field( $name, $value, $post_id );
	}
}

function p313_seed_post_type_empty( $post_type ) {
	$counts = wp_count_posts( $post_type );
	foreach ( (array) $counts as $count ) {
		if ( (int) $count > 0 ) {
			return false;
		}
	}
	return true;
}

function p313_seed_posts( $post_type, $items ) {
	if ( ! p313_seed_post_type_empty( $post_type ) ) {
		return;
	}

	foreach ( $items as $order => $item ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post_title'  => $item['title'],
				'post_content' => $item['content'] ?? '',
				'menu_order'  => $order,
			)
		);
		if ( ! is_wp_error( $post_id ) && $post_id ) {
			p313_seed_update_fields( $post_id, $item['fields'] ?? array() );
		}
	}
}

function p313_seed_pages() {
	$pages = array(
		'home'     => array( 'title' => 'Главная', 'template' => 'page-templates/template-home.php' ),
		'team'     => array( 'title' => 'Коллектив', 'template' => 'page-templates/template-team.php' ),
		'services' => array( 'title' => 'Услуги', 'template' => 'page-templates/template-services.php' ),
		'schedule' => array( 'title' => 'Расписание', 'template' => 'page-templates/template-schedule.php' ),
		'awards'   => array( 'title' => 'Награды', 'template' => 'page-templates/template-awards.php' ),
		'gallery'  => array( 'title' => 'Галерея', 'template' => 'page-templates/template-gallery.php' ),
		'blog'     => array( 'title' => 'Блог', 'template' => 'page-templates/template-blog.php' ),
		'events'   => array( 'title' => 'Мероприятия', 'template' => 'page-templates/template-events.php' ),
		'contacts' => array( 'title' => 'Контакты', 'template' => 'page-templates/template-contacts.php' ),
		'founder'  => array( 'title' => 'Руководитель', 'template' => 'page-templates/template-founder.php' ),
		'reviews'  => array( 'title' => 'Отзывы', 'template' => 'page-templates/template-reviews.php' ),
	);

	$created = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$page_id = $existing->ID;
		} else {
			$page_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => $page['title'],
					'post_name'   => $slug,
				)
			);
		}
		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
			$created[ $slug ] = (int) $page_id;
		}
	}

	if ( ! empty( $created['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['home'] );
	}

	return $created;
}

function p313_seed_options() {
	p313_seed_update_fields(
		'option',
		array(
			'org_name'         => 'Project 313',
			'phone'            => '+7 961 884 14 74',
			'phone_href'       => 'tel:+79618841474',
			'email'            => 'hello@project313.ru',
			'address'          => 'ул. Красный Путь, 59 (этаж 3, офис 5)',
			'city'             => 'Омск',
			'work_hours'       => 'Пн–Сб: 10:00–21:00',
			'hero_facts'       => array(
				array( 'n' => '11', 'label' => 'лет на сцене Омска' ),
				array( 'n' => '340', 'label' => 'учеников в семье' ),
				array( 'n' => '58', 'label' => 'наград и гран-при' ),
				array( 'n' => '6', 'label' => 'направлений' ),
			),
			'service_ages'     => array(
				array( 'id' => 'all', 'label' => 'Все возраста' ),
				array( 'id' => '3-6', 'label' => '3–6 лет' ),
				array( 'id' => '7-9', 'label' => '7–9 лет' ),
				array( 'id' => '10-13', 'label' => '10–13 лет' ),
				array( 'id' => '14+', 'label' => '14+' ),
				array( 'id' => 'adults', 'label' => 'Взрослые' ),
			),
			'ticker_items'     => array(
				array( 'text' => 'Гран-при «Танцующий город» 2025' ),
				array( 'text' => 'Образцовый коллектив' ),
				array( 'text' => 'Лауреат I степени «Волна успеха»' ),
				array( 'text' => '58 наград' ),
				array( 'text' => '«Хрустальная туфелька» — гран-при' ),
				array( 'text' => '11 лет на сцене' ),
			),
			'founder_name'     => 'Анна Волкова',
			'founder_role'     => 'Художественный руководитель Project 313',
			'founder_exp'      => '15 лет',
			'founder_education'=> 'Высшее хореографическое образование, лауреат всероссийских конкурсов.',
			'founder_short'    => 'Художественный руководитель студии и «мама» коллектива. Создаёт атмосферу, в которой дети и взрослые становятся семьёй, а танец — общим языком.',
			'founder_more_url' => 'https://project313.ru/founder/',
			'founder_bio'      => array(
				array( 'paragraph' => 'Анна основала Project 313, чтобы в Омске появилось место, где современная хореография сочетается с академической базой и настоящей семейной заботой. За 11 лет студия выросла в образцовый коллектив с сотнями учеников и десятками наград.' ),
				array( 'paragraph' => 'Как художественный руководитель она ставит номера, ведёт направления Modern и сопровождает учеников от первого занятия до большой сцены. Её подход — строгость к форме и теплота к человеку.' ),
				array( 'paragraph' => 'Лауреат всероссийских конкурсов, хореограф-постановщик. Верит, что танец — способ рассказать историю без слов, а главная победа — глаза ученика, который впервые полюбил сцену.' ),
			),
			'founder_facts'    => array(
				array( 'n' => '15', 'label' => 'лет педагогического опыта' ),
				array( 'n' => '11', 'label' => 'лет во главе Project 313' ),
				array( 'n' => '58+', 'label' => 'наград коллектива' ),
			),
			'gallery_cats'     => 'Все,Конкурсы,Мероприятия,Отчётные,Будни',
			'gallery_years'    => '',
			'logo_sub'         => 'школа современной хореографии, Омск',
			'cta_label'        => 'Записаться',
			'footer_kicker'    => 'присоединяйтесь',
			'nav_team'         => 'Коллектив',
			'footer_title'     => 'Первый шаг на нашу сцену',
			'footer_text'      => 'Оставьте заявку — и мы подберём направление, педагога и время для просмотра.',
			'footer_about'     => 'Образцовый коллектив, школа современной хореографии. Омск.',
			'footer_col_menu'  => 'Меню',
			'footer_col_contacts' => 'Контакты',
			'footer_col_social'=> 'Мы в сети',
			'copyright'        => '© 2025 Project 313. Все права защищены.',
			'socials'          => array(
				array( 'label' => 'VK', 'url' => 'https://vk.ru/dance.project313' ),
			),
			'form_modal_kicker' => 'Просмотр',
			'form_modal_title' => 'Запишитесь на просмотр',
			'form_modal_text'  => 'Оставьте контакты — мы подберём направление и удобное время.',
			'form_success_title' => 'Спасибо за заявку!',
			'form_success_text' => 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для просмотра.',
			'form_submit_label' => 'Записаться на просмотр',
			'form_call_label'  => 'Позвонить',
			'form_note'        => 'Нажимая кнопку, вы соглашаетесь с обработкой персональных данных.',
			'form_directions'  => array(
				array( 'label' => 'Детские группы' ), array( 'label' => 'Классическая хореография' ), array( 'label' => 'Актёрское мастерство' ), array( 'label' => 'Растяжка / Stretching' ), array( 'label' => 'Jazz-modern' ), array( 'label' => 'Modern' ),
			),
			'seo_default_title' => 'Project 313 — школа танца в Омске',
			'seo_default_description' => 'Project 313 — школа современной хореографии в Омске для детей и взрослых.',
			'seo_robots'       => 'index,follow',
		)
	);
}

function p313_seed_page_fields( $pages ) {
	$defaults = array(
		'home' => array(
			'page_home_hero_num' => '«313»',
			'page_home_hero_label' => 'образцовый коллектив · Омск',
			'page_home_hero_title' => "Танец как\nобщая история",
			'page_home_hero_text' => 'Школа современной хореографии, где дети и взрослые становятся семьёй, а движение — способом рассказать о себе.',
			'page_home_hero_cta_primary' => 'Записаться на просмотр',
			'page_home_hero_cta_secondary' => 'Смотреть расписание',
			'page_home_hero_badge_n' => '11 лет',
			'page_home_hero_badge_t' => 'на сцене Омска',
			'page_home_about_num' => '«01»',
			'page_home_about_label' => 'о студии',
			'page_home_about_title' => 'Мы растим танцовщиков и характеры',
			'page_home_about_text1' => 'Project 313 — это не просто студия. Это семья, где каждый ученик важен: от трёхлетних малышей до взрослых, впервые вышедших к станку.',
			'page_home_services_title' => 'Что мы преподаём',
			'page_home_services_link' => 'Все услуги',
			'page_home_schedule_title' => 'Найдите своё время',
			'page_home_schedule_sub' => 'Занятия проходят ежедневно в двух залах. Выберите удобный день — и запишитесь прямо из таблицы.',
			'page_home_schedule_link' => 'Полное расписание',
			'page_home_gallery_title' => 'Моменты со сцены',
			'page_home_gallery_link' => 'Вся галерея',
			'page_home_blog_title' => 'Из дневника',
			'page_home_reviews_title' => 'Голоса нашей семьи',
			'page_home_faq_title' => 'Частые вопросы',
			'page_home_faq_sub' => 'Не нашли ответ? Позвоните нам — +7 961 884 14 74',
			'page_home_founder_label' => 'Художественный руководитель',
			'page_home_founder_cta' => 'Записаться',
			'page_home_founder_link' => 'Подробнее',
		),
		'team' => array(
			'page_team_label' => 'коллектив',
			'page_team_title' => 'Те, кто ведёт на сцену',
			'page_team_sub' => 'Педагоги Project 313 — хореографы, наставники и часть большой семьи студии.',
			'page_team_tab_teachers' => 'Педагоги',
			'page_team_tab_kids' => 'Группа',
		),
		'services' => array(
			'page_services_num' => '«03»',
			'page_services_label' => 'услуги',
			'page_services_title' => 'Направления и форматы',
			'page_services_sub' => 'Групповые и индивидуальные занятия для детей и взрослых — выберите своё.',
		),
		'schedule' => array(
			'page_schedule_num' => '«04»',
			'page_schedule_label' => 'расписание',
			'page_schedule_title' => 'Когда мы танцуем',
			'page_schedule_sub' => 'Два зала, шесть направлений, каждый день недели. Запишитесь прямо из строки.',
		),
		'awards' => array(
			'page_awards_num' => '«05»',
			'page_awards_label' => 'награды',
			'page_awards_title' => '58 поводов гордиться',
			'page_awards_sub' => 'Гран-при, лауреатства и звание образцового коллектива — путь длиной в 11 лет.',
		),
		'gallery' => array(
			'page_gallery_num' => '«07»',
			'page_gallery_label' => 'галерея',
			'page_gallery_title' => 'Кадры со сцены и из зала',
			'page_gallery_sub' => 'Конкурсы, отчётные концерты и будни Project 313.',
		),
		'blog' => array(
			'page_blog_num' => '«08»',
			'page_blog_label' => 'блог',
			'page_blog_title' => 'Дневник студии',
			'page_blog_sub' => 'Заметки педагогов, советы родителям и истории из-за кулис.',
		),
		'events' => array(
			'page_events_num' => '«09»',
			'page_events_label' => 'мероприятия',
			'page_events_title' => 'Афиша событий',
			'page_events_sub' => 'Концерты, мастер-классы и дни открытых дверей Project 313.',
		),
		'contacts' => array(
			'page_contacts_num' => '«10»',
			'page_contacts_label' => 'контакты',
			'page_contacts_title' => 'Как нас найти',
			'page_contacts_sub' => 'Приходите на просмотр — или напишите, и мы сами подскажем удобное время.',
			'page_contacts_form_title' => 'Записаться на просмотр',
			'page_contacts_form_text' => 'Оставьте контакты — перезвоним и подберём направление.',
		),
		'founder' => array(
			'page_founder_label' => 'руководитель',
			'page_founder_title' => 'Анна Волкова',
			'page_founder_sub' => 'Художественный руководитель Project 313.',
		),
		'reviews' => array(
			'page_reviews_num' => '«06»',
			'page_reviews_label' => 'отзывы',
			'page_reviews_title' => 'Голоса нашей семьи',
			'page_reviews_sub' => 'Истории учеников и родителей, для которых Project 313 стал важной частью жизни.',
		),
	);

	foreach ( $defaults as $slug => $fields ) {
		if ( empty( $pages[ $slug ] ) ) {
			continue;
		}
		// Only fill empty fields so re-seed does not overwrite edits.
		foreach ( $fields as $key => $value ) {
			$current = function_exists( 'get_field' ) ? get_field( $key, $pages[ $slug ] ) : null;
			if ( $current === null || $current === false || $current === '' ) {
				p313_seed_update_fields( $pages[ $slug ], array( $key => $value ) );
			}
		}
	}
}

function p313_seed_content() {
	$pages = p313_seed_pages();
	p313_seed_options();
	p313_seed_page_fields( $pages );

	$services = array(
		array( 'title' => 'Детские группы', 'fields' => array( 'service_key' => 'kids', 'price' => 'от 400 ₽', 'age_label' => '3–6', 'age_group' => '3-6', 'format' => 'group', 'duration' => '45 минут', 'photo_id' => '1580724430485-ed43daa4b4f3', 'short' => 'Первый танец — через игру и радость.', 'text' => 'Ритмика, координация и музыкальность в игровом формате. Малыши влюбляются в движение и делают первые шаги к большой сцене.' ) ),
		array( 'title' => 'Классическая хореография', 'fields' => array( 'service_key' => 'classical', 'price' => 'от 600 ₽', 'age_label' => '7–9', 'age_group' => '7-9', 'format' => 'group', 'duration' => '60 минут', 'photo_id' => '1478604793707-b3a982845b32', 'short' => 'Академическая база — осанка, выворотность, культура движения.', 'text' => 'Классика — фундамент любого танцовщика. Станок, середина, adagio и allegro формируют силу, линию и дисциплину, на которых строится всё остальное.' ) ),
		array( 'title' => 'Актёрское мастерство', 'fields' => array( 'service_key' => 'acting', 'price' => 'от 500 ₽', 'age_label' => '10–13', 'age_group' => '10-13', 'format' => 'group', 'duration' => '75 минут', 'photo_id' => '1558905566-ddbeb2fc2c2f', 'short' => 'Сцена требует не только тела, но и характера.', 'text' => 'Этюды, речь, работа с образом и эмоцией. Ученики учатся не бояться зрителя и наполнять номер смыслом.' ) ),
		array( 'title' => 'Растяжка / Stretching', 'fields' => array( 'service_key' => 'stretching', 'price' => 'от 450 ₽', 'age_label' => '10–13', 'age_group' => '10-13', 'format' => 'group', 'duration' => '60 минут', 'photo_id' => '1520732789276-a0ebed2eeabd', 'short' => 'Мягко и безопасно — к свободе в теле.', 'text' => 'Функциональная растяжка с акцентом на суставы и глубокие мышцы. Постепенно, без боли, к шпагату и лёгкости в каждом движении.' ) ),
		array( 'title' => 'Jazz-modern', 'fields' => array( 'service_key' => 'jazz-modern', 'price' => 'от 550 ₽', 'age_label' => '14+', 'age_group' => '14+', 'format' => 'group', 'duration' => '90 минут', 'photo_id' => '1529229504105-4ea795dcbf59', 'short' => 'Энергия джаза и свобода модерна в одном потоке движения.', 'text' => 'Jazz-modern соединяет чёткую ритмику джазового танца с текучестью современной хореографии.' ) ),
		array( 'title' => 'Modern', 'fields' => array( 'service_key' => 'modern', 'price' => 'от 550 ₽', 'age_label' => '14+', 'age_group' => '14+', 'format' => 'group', 'duration' => '90 минут', 'photo_id' => '1524594152303-9fd13543fe6e', 'short' => 'Современный танец как язык — про честность и присутствие.', 'text' => 'Модерн — это про контакт с полом, дыхание и вес тела. Класс развивает пластику, чувство пространства и умение импровизировать.' ) ),
		array( 'title' => 'Modern для взрослых', 'fields' => array( 'service_key' => 'modern-adults', 'price' => 'от 600 ₽', 'age_label' => '18+', 'age_group' => 'adults', 'format' => 'group', 'duration' => '75 минут', 'photo_id' => '1524594152303-9fd13543fe6e', 'short' => 'Группа для взрослых — без оценки, с вниманием к телу и музыке.', 'text' => 'Мягкий вход, работа с дыханием, пластикой и уверенностью на сцене.' ) ),
		array( 'title' => 'Индивидуальный Modern', 'fields' => array( 'service_key' => 'ind-modern', 'price' => 'от 1500 ₽', 'age_label' => 'любой', 'age_group' => '', 'format' => 'individual', 'duration' => '60 минут', 'photo_id' => '1524594152303-9fd13543fe6e', 'short' => 'Персональный урок под ваши задачи и темп.', 'text' => 'Индивидуальная работа с педагогом: техника, постановка номера или мягкий вход в движение.' ) ),
		array( 'title' => 'Индивидуальная классика', 'fields' => array( 'service_key' => 'ind-classical', 'price' => 'от 1600 ₽', 'age_label' => 'любой', 'age_group' => '', 'format' => 'individual', 'duration' => '60 минут', 'photo_id' => '1478604793707-b3a982845b32', 'short' => 'Станок и середина один на один с педагогом.', 'text' => 'Классическая база в индивидуальном формате: осанка, выворотность, координация.' ) ),
		array( 'title' => 'Индивидуальная растяжка', 'fields' => array( 'service_key' => 'ind-stretching', 'price' => 'от 1200 ₽', 'age_label' => 'любой', 'age_group' => '', 'format' => 'individual', 'duration' => '55 минут', 'photo_id' => '1520732789276-a0ebed2eeabd', 'short' => 'Безопасная растяжка с учётом именно вашего тела.', 'text' => 'Персональный стретчинг без боли и гонки за результатом. Работаем с суставами, дыханием и подвижностью под ваш запрос.' ) ),
		array( 'title' => 'Индивидуальный Jazz-modern', 'fields' => array( 'service_key' => 'ind-jazz', 'price' => 'от 1500 ₽', 'age_label' => 'любой', 'age_group' => '', 'format' => 'individual', 'duration' => '60 минут', 'photo_id' => '1529229504105-4ea795dcbf59', 'short' => 'Техника, амплитуда и сцена — в вашем темпе.', 'text' => 'Личный урок jazz-modern: ритмика, координация, артистизм. Можно готовить конкурсный номер или просто углублять технику.' ) ),
	);
	p313_seed_posts( 'p313_service', $services );

	p313_seed_posts( 'p313_teacher', array(
		array( 'title' => 'Анна Волкова', 'fields' => array( 'teacher_key' => 'volkova', 'role' => 'Художественный руководитель · Modern', 'photo_id' => '1650465811226-de19b0502e94', 'exp' => '15 лет педагогической практики', 'education' => 'Высшее хореографическое образование. Лауреат всероссийских конкурсов.', 'bio' => 'Художественный руководитель Project 313. Хореограф-постановщик. Верит, что танец — это способ рассказать историю без слов.', 'is_leader' => 1, 'more_url' => 'https://project313.ru/founder/' ) ),
		array( 'title' => 'Мария Соколова', 'fields' => array( 'teacher_key' => 'sokolova', 'role' => 'Классическая хореография', 'photo_id' => '1646929429688-28056c9d35d3', 'exp' => '12 лет на сцене и в зале', 'education' => 'Академия русского балета.', 'bio' => 'Ведёт классику для всех возрастов, ставит академическую линию и культуру движения.' ) ),
		array( 'title' => 'Дмитрий Орлов', 'fields' => array( 'teacher_key' => 'orlov', 'role' => 'Jazz-modern · Актёрское мастерство', 'photo_id' => '1624427537414-e9037f1c2392', 'exp' => '10 лет преподавания', 'education' => 'Музыкальный театр, педагогика современного танца.', 'bio' => 'Танцовщик и педагог. Ставит энергичные конкурсные номера и учит держать сцену.' ) ),
		array( 'title' => 'Ольга Лебедева', 'fields' => array( 'teacher_key' => 'lebedeva', 'role' => 'Растяжка · Группы', 'photo_id' => '1559157306-406ce1382742', 'exp' => '8 лет', 'education' => 'Сертифицированный тренер по стретчингу.', 'bio' => 'Мягкий, внимательный подход — особенно бережно работает с малышами.' ) ),
	) );
	p313_seed_posts( 'p313_kids', array(
		array( 'title' => '«Капельки»', 'fields' => array( 'age' => '3–4 года', 'level' => 'junior', 'note' => 'Ритмика и первые движения' ) ),
		array( 'title' => '«Искорки»', 'fields' => array( 'age' => '5–6 лет', 'level' => 'junior', 'note' => 'Основы классики через игру' ) ),
		array( 'title' => '«Крылья»', 'fields' => array( 'age' => '7–9 лет', 'level' => 'middle', 'note' => 'Классика и современный танец' ) ),
		array( 'title' => '«Формация 313»', 'fields' => array( 'age' => '10–13 лет', 'level' => 'senior', 'note' => 'Конкурсная группа' ) ),
		array( 'title' => '«Проект-старшие»', 'fields' => array( 'age' => '14–17 лет', 'level' => 'senior', 'note' => 'Профессиональная подготовка' ) ),
	) );

	$schedule = array(
		array( 'Понедельник', '17:00', 'Детские группы', '«Капельки»', 'Ольга Лебедева', 'Красный Путь' ),
		array( 'Понедельник', '19:00', 'Modern', '', 'Анна Волкова', 'Химик' ),
		array( 'Вторник', '18:00', 'Классическая хореография', '«Крылья»', 'Мария Соколова', 'Красный Путь' ),
		array( 'Вторник', '20:00', 'Растяжка / Stretching', '«Формация 313»', 'Ольга Лебедева', 'Химик' ),
		array( 'Среда', '17:30', 'Jazz-modern', '«Проект-старшие»', 'Дмитрий Орлов', 'Красный Путь' ),
		array( 'Четверг', '18:30', 'Актёрское мастерство', '', 'Дмитрий Орлов', 'Химик' ),
		array( 'Пятница', '19:00', 'Modern', '', 'Анна Волкова', 'Красный Путь' ),
		array( 'Суббота', '11:00', 'Детские группы', '«Искорки»', 'Ольга Лебедева', 'Красный Путь' ),
		array( 'Суббота', '13:00', 'Классическая хореография', '«Крылья»', 'Мария Соколова', 'Химик' ),
	);
	p313_seed_posts(
		'p313_branch',
		array(
			array( 'title' => 'Красный Путь', 'fields' => array( 'branch_key' => 'krasny', 'address' => 'ул. Красный Путь, 59 (этаж 3, офис 5)', 'lat' => '54.9954', 'lng' => '73.3577' ) ),
			array( 'title' => 'Химик', 'fields' => array( 'branch_key' => 'khimik', 'address' => 'просп. Королёва, 1', 'lat' => '55.0423', 'lng' => '73.2950' ) ),
		)
	);
	if ( p313_seed_post_type_empty( 'p313_schedule' ) ) {
		foreach ( $schedule as $row ) {
			p313_schedule_import_row(
				array(
					'day'       => $row[0],
					'time'      => $row[1],
					'direction' => $row[2],
					'group'     => $row[3],
					'teacher'   => $row[4],
					'branch'    => $row[5],
				)
			);
		}
	}

	$awards = array( array( 2025, 'Гран-при «Танцующий город»', 'Москва', 'Всероссийский' ), array( 2025, 'Лауреат I степени «Сибирь зажигает»', 'Новосибирск', 'Межрегиональный' ), array( 2024, 'Гран-при «Хрустальная туфелька»', 'Омск', 'Областной' ), array( 2024, 'Лауреат I степени «Волна успеха»', 'Сочи', 'Международный' ), array( 2023, 'Лауреат II степени «Ритмы планеты»', 'Казань', 'Всероссийский' ), array( 2023, 'Диплом «За артистизм»', 'Омск', 'Городской' ), array( 2022, 'Лауреат I степени «Первый шаг»', 'Тюмень', 'Межрегиональный' ), array( 2022, 'Звание «Образцовый коллектив»', 'Омск', 'Областной' ) );
	$award_items = array();
	foreach ( $awards as $row ) { $award_items[] = array( 'title' => $row[1], 'fields' => array( 'year' => $row[0], 'place' => $row[2], 'level' => $row[3] ) ); }
	p313_seed_posts( 'p313_award', $award_items );

	p313_seed_posts( 'p313_review', array(
		array( 'title' => 'Екатерина М.', 'fields' => array( 'role' => 'мама ученицы, 7 лет', 'rating' => 5, 'text' => 'Дочка бежит на занятия вприпрыжку. За год — осанка, характер, первые медали. Педагоги стали почти семьёй.' ) ),
		array( 'title' => 'Игорь П.', 'fields' => array( 'role' => 'ученик, 16 лет', 'rating' => 5, 'text' => 'Пришёл «попробовать» — остался на три года. Здесь по-настоящему учат чувствовать музыку и сцену.' ) ),
		array( 'title' => 'Наталья В.', 'fields' => array( 'role' => 'мама, двое детей', 'rating' => 5, 'text' => 'Тёплая атмосфера и при этом серьёзная подготовка. Отчётные концерты — всегда до мурашек.' ) ),
		array( 'title' => 'Алина С.', 'fields' => array( 'role' => 'взрослая группа', 'rating' => 5, 'text' => 'Ходила на стретчинг и модерн для себя. Никакого осуждения, только поддержка. Тело будто заново открыла.' ) ),
	) );
	p313_seed_posts( 'p313_event', array(
		array( 'title' => 'Отчётный концерт «Полёт»', 'fields' => array( 'date_label' => '24 мая 2025', 'time' => '18:00', 'place' => 'ДК им. Малунцева, Омск', 'photo_id' => '1560088161-ca82e528afc9', 'excerpt' => 'Главное событие сезона — все группы Project 313 на большой сцене.' ) ),
		array( 'title' => 'Открытый мастер-класс по модерну', 'fields' => array( 'date_label' => '12 апреля 2025', 'time' => '15:00', 'place' => 'Студия Project 313', 'photo_id' => '1524594152303-9fd13543fe6e', 'excerpt' => 'Бесплатный класс с художественным руководителем. Для всех желающих 14+.' ) ),
		array( 'title' => 'День открытых дверей', 'fields' => array( 'date_label' => '1 сентября 2025', 'time' => '11:00', 'place' => 'Студия Project 313', 'photo_id' => '1596315458574-d99efaea3b3b', 'excerpt' => 'Знакомство со студией, педагогами и направлениями. Пробные занятия весь день.' ) ),
	) );

	$gallery = array( array( 2025, 'Конкурсы', '1550026593-cb89847b168d', 'tall' ), array( 2025, 'Отчётные', '1560088161-ca82e528afc9', 'wide' ), array( 2025, 'Будни', '1524594152303-9fd13543fe6e', 'square' ), array( 2024, 'Мероприятия', '1558905566-ddbeb2fc2c2f', 'square' ), array( 2024, 'Конкурсы', '1529229504105-4ea795dcbf59', 'wide' ), array( 2024, 'Будни', '1604954055722-7f80571fbfc3', 'tall' ), array( 2023, 'Отчётные', '1596315458574-d99efaea3b3b', 'square' ), array( 2023, 'Мероприятия', '1580724430485-ed43daa4b4f3', 'wide' ), array( 2023, 'Будни', '1478604793707-b3a982845b32', 'tall' ) );
	$gallery_items = array();
	foreach ( $gallery as $index => $row ) { $gallery_items[] = array( 'title' => $row[1] . ' ' . $row[0] . ' #' . ( $index + 1 ), 'fields' => array( 'year' => $row[0], 'category' => $row[1], 'photo_id' => $row[2], 'ratio' => $row[3] ) ); }
	p313_seed_posts( 'p313_gallery', $gallery_items );
	p313_seed_posts( 'p313_faq', array(
		array( 'title' => 'С какого возраста можно начать?', 'fields' => array( 'answer' => 'Мы принимаем малышей с 3 лет в группу «Капельки» и не ставим верхней границы — во взрослых группах занимаются ученики любого возраста.' ) ),
		array( 'title' => 'Нужна ли подготовка перед первым занятием?', 'fields' => array( 'answer' => 'Нет. Пробное занятие рассчитано на любой уровень — вы просто приходите в удобной одежде, остальное покажет педагог.' ) ),
		array( 'title' => 'Сколько стоит пробное занятие?', 'fields' => array( 'answer' => 'Первое пробное занятие бесплатное. Дальше действуют абонементы и разовые посещения — от 400 ₽.' ) ),
		array( 'title' => 'Можно ли перейти в другую группу?', 'fields' => array( 'answer' => 'Да. Педагоги подбирают направление и уровень индивидуально и помогают перейти, когда вы будете готовы.' ) ),
		array( 'title' => 'Есть ли выступления и концерты?', 'fields' => array( 'answer' => 'Конечно. Отчётные концерты, конкурсы и фестивали — важная часть жизни коллектива. Участие добровольное.' ) ),
	) );

	if ( p313_seed_post_type_empty( 'post' ) ) {
		$blog = array(
			array( 'Как выбрать первое направление для ребёнка', '5 мин', '1677603142061-d19e9cbd5e4f', 'Классика, модерн или ритмика? Разбираем, с чего начать и как понять, что подходит именно вашему ребёнку.', 'Родителям' ),
			array( 'Что происходит за кулисами конкурсного номера', '7 мин', '1550026593-cb89847b168d', 'От первой репетиции до выхода на сцену — история одного гран-при глазами педагога.', 'Закулисье' ),
			array( 'Растяжка без боли: 5 правил безопасности', '4 мин', '1520732789276-a0ebed2eeabd', 'Почему шпагат — это марафон, а не спринт, и как не навредить себе в погоне за результатом.', 'Здоровье' ),
		);
		foreach ( $blog as $item ) {
			$post_id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $item[0], 'post_content' => $item[3], 'post_excerpt' => $item[3] ) );
			if ( ! is_wp_error( $post_id ) && $post_id ) {
				p313_seed_update_fields( $post_id, array( 'read_time' => $item[1], 'photo_id' => $item[2], 'card_excerpt' => $item[3] ) );
				wp_set_object_terms( $post_id, $item[4], 'category', false );
			}
		}
	}
}

function p313_seed_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || empty( $_GET['p313_seeded'] ) ) {
		return;
	}
	echo '<div class="notice notice-success is-dismissible"><p>Project 313: страницы и демонстрационный контент созданы.</p></div>';
}
add_action( 'admin_notices', 'p313_seed_admin_notice' );

function p313_seed_from_admin() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Недостаточно прав.' );
	}
	check_admin_referer( 'p313_seed' );
	p313_seed_content();
	wp_safe_redirect( add_query_arg( 'p313_seeded', '1', admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_p313_seed', 'p313_seed_from_admin' );
add_action( 'after_switch_theme', 'p313_seed_content' );

function p313_migrate_copy() {
	$ver = '1.4.0';
	if ( get_option( 'p313_copy_ver' ) === $ver ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$replace_option = function ( $key, $from, $to ) {
		$current = p313_option( $key, '' );
		if ( $current === $from || '' === $current ) {
			if ( function_exists( 'update_field' ) ) {
				update_field( $key, $to, 'option' );
			}
		}
	};

	$replace_option( 'phone', '+7 (3812) 31-33-13', '+7 961 884 14 74' );
	$replace_option( 'phone', '+7 996 188-41-74', '+7 961 884 14 74' );
	$replace_option( 'phone_href', 'tel:+73812313313', 'tel:+79618841474' );
	$replace_option( 'phone_href', 'tel:+79961884174', 'tel:+79618841474' );
	$replace_option( 'footer_kicker', '«00» / присоединяйтесь', 'присоединяйтесь' );
	$replace_option( 'nav_team', 'Команда', 'Коллектив' );
	$replace_option( 'founder_role', 'Художественный руководитель · основатель Project 313', 'Художественный руководитель Project 313' );
	$replace_option( 'founder_short', 'Основатель студии и «мама» коллектива. Создаёт атмосферу, в которой дети и взрослые становятся семьёй, а танец — общим языком.', 'Художественный руководитель студии и «мама» коллектива. Создаёт атмосферу, в которой дети и взрослые становятся семьёй, а танец — общим языком.' );
	if ( ! p313_option( 'founder_more_url', '' ) ) {
		update_field( 'founder_more_url', 'https://project313.ru/founder/', 'option' );
	}
	$gallery_years = trim( (string) p313_option( 'gallery_years', '' ) );
	if ( in_array( $gallery_years, array( 'Все,2025,2024,2023', 'Все, 2025, 2024, 2023' ), true ) ) {
		update_field( 'gallery_years', '', 'option' );
	}
	$replace_option( 'form_modal_kicker', 'Первое занятие', 'Просмотр' );
	$replace_option( 'form_modal_title', 'Запишитесь на пробное занятие', 'Запишитесь на просмотр' );
	$replace_option( 'form_modal_title', 'Записаться на пробное', 'Запишитесь на просмотр' );
	$replace_option( 'form_submit_label', 'Записаться на пробное', 'Записаться на просмотр' );
	$replace_option( 'form_success_text', 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для пробного занятия.', 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для просмотра.' );
	$replace_option( 'footer_text', 'Оставьте заявку — и мы подберём направление, педагога и время для бесплатного пробного занятия.', 'Оставьте заявку — и мы подберём направление, педагога и время для просмотра.' );

	$pages = array();
	$template_map = array(
		'page-templates/template-home.php'    => 'home',
		'page-templates/template-team.php'    => 'team',
		'page-templates/template-contacts.php' => 'contacts',
		'page-templates/template-founder.php' => 'founder',
	);
	foreach ( $template_map as $template => $slug ) {
		$found = get_pages( array( 'meta_key' => '_wp_page_template', 'meta_value' => $template, 'number' => 1 ) );
		if ( $found ) {
			$pages[ $slug ] = $found[0];
		} else {
			$by_path = get_page_by_path( $slug );
			if ( $by_path ) {
				$pages[ $slug ] = $by_path;
			}
		}
	}

	$field_map = array(
		'home'     => array(
			'page_home_hero_cta_primary' => array( 'Записаться на пробное', 'Записаться на просмотр' ),
			'page_home_faq_sub'          => array( 'Не нашли ответ? Позвоните нам — +7 (3812) 31-33-13', 'Не нашли ответ? Позвоните нам — +7 961 884 14 74' ),
			'page_home_founder_label'    => array( 'Основатель', 'Художественный руководитель' ),
		),
		'team'     => array(
			'page_team_label'    => array( 'команда', 'коллектив' ),
			'page_team_tab_kids' => array( 'Детские группы', 'Группа' ),
		),
		'contacts' => array(
			'page_contacts_sub'        => array( 'Приходите на пробное занятие — или напишите, и мы сами подскажем удобное время.', 'Приходите на просмотр — или напишите, и мы сами подскажем удобное время.' ),
			'page_contacts_form_title' => array( 'Записаться на пробное', 'Записаться на просмотр' ),
		),
		'founder'  => array(
			'page_founder_label' => array( 'основатель', 'руководитель' ),
			'page_founder_sub'   => array( 'Основатель студии и художественный руководитель Project 313.', 'Художественный руководитель Project 313.' ),
		),
	);

	foreach ( $field_map as $slug => $fields ) {
		if ( empty( $pages[ $slug ] ) ) {
			continue;
		}
		$page_id = (int) $pages[ $slug ]->ID;
		foreach ( $fields as $key => $pair ) {
			$current = p313_field( $key, '', $page_id );
			if ( $current === $pair[0] || '' === $current ) {
				update_field( $key, $pair[1], $page_id );
			}
		}
	}

	if ( ! empty( $pages['home'] ) ) {
		$faq_sub = (string) p313_field( 'page_home_faq_sub', '', $pages['home']->ID );
		if ( false !== strpos( $faq_sub, '+7 996 188-41-74' ) || false !== strpos( $faq_sub, '+7 (3812) 31-33-13' ) ) {
			update_field( 'page_home_faq_sub', str_replace( array( '+7 996 188-41-74', '+7 (3812) 31-33-13' ), '+7 961 884 14 74', $faq_sub ), $pages['home']->ID );
		}
	}
	if ( ! empty( $pages['team'] ) ) {
		$tab = p313_field( 'page_team_tab_kids', '', $pages['team']->ID );
		if ( in_array( $tab, array( 'Детские', 'Детские группы' ), true ) ) {
			update_field( 'page_team_tab_kids', 'Группа', $pages['team']->ID );
		}
		if ( 'Команда' === $pages['team']->post_title ) {
			wp_update_post( array( 'ID' => $pages['team']->ID, 'post_title' => 'Коллектив' ) );
		}
	}
	if ( ! empty( $pages['founder'] ) && 'Основатель' === $pages['founder']->post_title ) {
		wp_update_post( array( 'ID' => $pages['founder']->ID, 'post_title' => 'Руководитель' ) );
	}

	$leader_id = p313_find_post_by_title( 'p313_teacher', p313_option( 'founder_name', 'Анна Волкова' ) );
	if ( $leader_id && ! p313_field( 'is_leader', false, $leader_id ) ) {
		update_field( 'is_leader', 1, $leader_id );
		if ( ! p313_field( 'more_url', '', $leader_id ) ) {
			update_field( 'more_url', 'https://project313.ru/founder/', $leader_id );
		}
	}

	update_option( 'p313_copy_ver', $ver );
}
add_action( 'init', 'p313_migrate_copy', 20 );

function p313_migrate_locations() {
	if ( get_option( 'p313_loc_ver' ) === '1.3.0' ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$socials = p313_option( 'socials', array() );
	$has_vk  = false;
	if ( is_array( $socials ) ) {
		foreach ( $socials as &$social ) {
			$label = mb_strtolower( p313_row_label( $social ) );
			$url   = is_array( $social ) ? trim( (string) ( $social['url'] ?? '' ) ) : '';
			if ( false !== strpos( $label, 'vk' ) && ( ! $url || '#' === $url ) ) {
				$social['url'] = 'https://vk.ru/dance.project313';
				$has_vk        = true;
			}
			if ( false !== strpos( $url, 'vk.' ) ) {
				$has_vk = true;
			}
		}
		unset( $social );
	}
	if ( ! $has_vk ) {
		$socials = array(
			array(
				'label' => 'VK',
				'url'   => 'https://vk.ru/dance.project313',
			),
		);
	}
	update_field( 'socials', $socials, 'option' );

	foreach ( p313_default_branches() as $branch ) {
		$id = p313_find_post_by_title( 'p313_branch', $branch['title'] );
		if ( ! $id && 'krasny' === $branch['key'] ) {
			$id = p313_find_post_by_title( 'p313_branch', 'Зал А' );
		}
		if ( ! $id && 'khimik' === $branch['key'] ) {
			$id = p313_find_post_by_title( 'p313_branch', 'Зал Б' );
		}
		if ( ! $id ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'p313_branch',
					'post_status' => 'publish',
					'post_title'  => $branch['title'],
				)
			);
		} else {
			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => $branch['title'],
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_field( 'branch_key', $branch['key'], $id );
			update_field( 'address', $branch['address'], $id );
			update_field( 'lat', $branch['lat'], $id );
			update_field( 'lng', $branch['lng'], $id );
		}
	}

	$levels = array(
		'«Капельки»'       => 'junior',
		'Капельки'         => 'junior',
		'«Искорки»'        => 'junior',
		'Искорки'          => 'junior',
		'«Крылья»'         => 'middle',
		'Крылья'           => 'middle',
		'«Формация 313»'   => 'senior',
		'Формация 313'     => 'senior',
		'«Проект-старшие»' => 'senior',
		'Проект-старшие'   => 'senior',
	);
	foreach ( $levels as $title => $level ) {
		$kid_id = p313_find_post_by_title( 'p313_kids', $title );
		if ( $kid_id && ! p313_field( 'level', '', $kid_id ) ) {
			update_field( 'level', $level, $kid_id );
		}
	}

	update_option( 'p313_loc_ver', '1.3.0' );
}
add_action( 'init', 'p313_migrate_locations', 21 );

add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'p313-settings',
			'Импорт демо',
			'Импорт демо',
			'manage_options',
			'p313-seed',
			function () {
				$url = wp_nonce_url( admin_url( 'admin-post.php?action=p313_seed' ), 'p313_seed' );
				echo '<div class="wrap"><h1>Импорт демо-контента Project 313</h1>';
				echo '<p>Создаёт недостающие страницы и CPT (не затирает уже существующие записи CPT).</p>';
				echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">Запустить импорт</a></p></div>';
			}
		);
	}
);
