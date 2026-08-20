<?php
$hero_num      = p313_field( 'page_home_hero_num', '«313»' );
$hero_label    = p313_field( 'page_home_hero_label', 'образцовый коллектив · Омск' );
$hero_title    = p313_field( 'page_home_hero_title', p313_option( 'hero_title', "Танец как\nобщая история" ) );
$hero_text     = p313_field( 'page_home_hero_text', p313_option( 'hero_text', 'Школа современной хореографии, где дети и взрослые становятся семьёй, а движение — способом рассказать о себе.' ) );
$hero_cta_1    = p313_field( 'page_home_hero_cta_primary', 'Записаться на просмотр' );
$hero_cta_2    = p313_field( 'page_home_hero_cta_secondary', 'Смотреть расписание' );
$hero_badge_n  = p313_field( 'page_home_hero_badge_n', p313_option( 'hero_badge_number', '11 лет' ) );
$hero_badge_t  = p313_field( 'page_home_hero_badge_t', p313_option( 'hero_badge_text', 'на сцене Омска' ) );
$hero_image    = p313_img_url( p313_field( 'page_home_hero_image', p313_option( 'hero_image' ) ), 'full' ) ?: p313_asset( 'assets/img/hero.webp' );

$about_num     = p313_field( 'page_home_about_num', '«01»' );
$about_label   = p313_field( 'page_home_about_label', 'о студии' );
$about_title   = p313_field( 'page_home_about_title', 'Мы растим танцовщиков и характеры' );
$about_text1   = p313_field( 'page_home_about_text1', 'Project 313 — это не просто студия. Это семья, где каждый ученик важен: от трёхлетних малышей до взрослых, впервые вышедших к станку.' );
$about_text2   = p313_field( 'page_home_about_text2', '' );
$founder_image = p313_img_url( p313_field( 'page_home_founder_image', p313_option( 'founder_photo' ) ), 'large' ) ?: p313_asset( 'assets/img/founder.webp' );
$founder_label = p313_field( 'page_home_founder_label', p313_option( 'founder_role', 'Художественный руководитель' ) );
$founder_name  = p313_field( 'page_home_founder_name', p313_option( 'founder_name', 'Анна Волкова' ) );
$founder_text  = p313_field( 'page_home_founder_text', p313_option( 'founder_short', 'Художественный руководитель студии и «мама» коллектива.' ) );
$founder_cta   = p313_field( 'page_home_founder_cta', p313_option( 'cta_label', 'Записаться' ) );
$founder_link  = p313_field( 'page_home_founder_link', 'Подробнее' );
$founder_url   = p313_field( 'page_home_founder_url', '' ) ?: p313_founder_more_url();
$gallery_img_1 = p313_img_url( p313_field( 'page_home_gallery_image_1' ), 'p313-card' ) ?: p313_asset( 'assets/img/hero-square.webp' );
$gallery_img_2 = p313_img_url( p313_field( 'page_home_gallery_image_2' ), 'p313-card' ) ?: p313_asset( 'assets/img/color.webp' );

$services_num   = p313_field( 'page_home_services_num', '«02»' );
$services_label = p313_field( 'page_home_services_label', 'направления' );
$services_title = p313_field( 'page_home_services_title', 'Что мы преподаём' );
$services_link  = p313_field( 'page_home_services_link', 'Все услуги' );

$schedule_num   = p313_field( 'page_home_schedule_num', '«03»' );
$schedule_label = p313_field( 'page_home_schedule_label', 'расписание' );
$schedule_title = p313_field( 'page_home_schedule_title', 'Найдите своё время' );
$schedule_sub   = p313_field( 'page_home_schedule_sub', 'Занятия проходят ежедневно в двух залах. Выберите удобный день — и запишитесь прямо из таблицы.' );
$schedule_link  = p313_field( 'page_home_schedule_link', 'Полное расписание' );

$gallery_num   = p313_field( 'page_home_gallery_num', '«04»' );
$gallery_label = p313_field( 'page_home_gallery_label', 'галерея' );
$gallery_title = p313_field( 'page_home_gallery_title', 'Моменты со сцены' );
$gallery_link  = p313_field( 'page_home_gallery_link', 'Вся галерея' );

$blog_num   = p313_field( 'page_home_blog_num', '«05»' );
$blog_label = p313_field( 'page_home_blog_label', 'блог' );
$blog_title = p313_field( 'page_home_blog_title', 'Из дневника' );

$reviews_num   = p313_field( 'page_home_reviews_num', '«06»' );
$reviews_label = p313_field( 'page_home_reviews_label', 'отзывы' );
$reviews_title = p313_field( 'page_home_reviews_title', 'Голоса нашей семьи' );

$faq_num   = p313_field( 'page_home_faq_num', '«07»' );
$faq_label = p313_field( 'page_home_faq_label', 'вопросы' );
$faq_title = p313_field( 'page_home_faq_title', 'Частые вопросы' );
$faq_sub   = p313_field( 'page_home_faq_sub', 'Не нашли ответ? Позвоните нам — ' . p313_phone() );
?>
<main class="main"><section class="hero"><div class="hero__bg"></div><div class="container"><div class="hero__grid"><div><div class="reveal"><div class="section-num"><span class="section-num__n"><?php echo esc_html( $hero_num ); ?></span><span class="section-num__label"><?php echo esc_html( $hero_label ); ?></span></div></div><div class="reveal"><h1 class="hero__title"><?php echo wp_kses_post( nl2br( esc_html( $hero_title ) ) ); ?></h1></div><?php p313_sweep( 'hero__sweep', '0.7' ); ?><p class="hero__text"><?php echo esc_html( $hero_text ); ?></p><div class="hero__actions"><button class="btn btn--primary" type="button" data-open-form><?php echo esc_html( $hero_cta_1 ); ?></button><a class="btn btn--secondary" href="<?php echo esc_url( p313_page_url( 'schedule' ) ); ?>"><?php echo esc_html( $hero_cta_2 ); ?></a></div></div><div class="hero__media"><div class="hero__img-wrap"><img class="hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( p313_option( 'org_name', 'Project 313' ) ); ?>"></div><div class="hero__badge"><p class="hero__badge-n"><?php echo esc_html( $hero_badge_n ); ?></p><p class="hero__badge-t"><?php echo esc_html( $hero_badge_t ); ?></p></div></div></div><div class="hero__facts" data-facts></div></div></section>
<section class="section"><div class="container"><div class="about"><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $about_num ); ?></span><span class="section-num__label"><?php echo esc_html( $about_label ); ?></span></div><h2 class="about__title"><?php echo esc_html( $about_title ); ?></h2><p class="about__text"><?php echo esc_html( $about_text1 ); ?></p><?php if ( $about_text2 ) : ?><p class="about__text"><?php echo esc_html( $about_text2 ); ?></p><?php endif; ?></div><div class="about__founder"><div class="about__founder-photo-wrap"><img class="about__founder-photo" src="<?php echo esc_url( $founder_image ); ?>" alt="<?php echo esc_attr( $founder_name ); ?>"></div><div class="about__founder-body"><p class="about__founder-label"><?php echo esc_html( $founder_label ); ?></p><h3 class="about__founder-name"><?php echo esc_html( $founder_name ); ?></h3><p class="about__founder-text"><?php echo esc_html( $founder_text ); ?></p><div class="about__founder-actions"><button class="btn btn--primary" type="button" data-open-form><?php echo esc_html( $founder_cta ); ?></button><a class="btn btn--secondary" href="<?php echo esc_url( $founder_url ); ?>"><?php echo esc_html( $founder_link ); ?></a></div></div></div></div></div></section>
<section class="section section--flush"><div class="container"><div class="section-head"><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $services_num ); ?></span><span class="section-num__label"><?php echo esc_html( $services_label ); ?></span></div><h2 class="section-head__title"><?php echo esc_html( $services_title ); ?></h2></div><a class="section-head__link" href="<?php echo esc_url( p313_page_url( 'services' ) ); ?>"><?php echo esc_html( $services_link ); ?> <?php echo p313_icon( 'arrow' ); ?></a></div><div class="services-filters" data-services-filters-home><div class="services-filters__formats filters" data-format-filters></div><div class="services-filters__ages filters panel-hidden" data-age-filters></div></div><div class="services-grid" data-services-home></div></div></section>
<section class="section"><div class="container"><div class="schedule-teaser"><div class="schedule-teaser__grid"><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $schedule_num ); ?></span><span class="section-num__label"><?php echo esc_html( $schedule_label ); ?></span></div><h2 class="schedule-teaser__title"><?php echo esc_html( $schedule_title ); ?></h2><p class="schedule-teaser__text"><?php echo esc_html( $schedule_sub ); ?></p><a class="btn btn--secondary" href="<?php echo esc_url( p313_page_url( 'schedule' ) ); ?>"><?php echo esc_html( $schedule_link ); ?></a></div><div class="schedule-teaser__list" data-schedule-teaser></div></div></div></div></section><div class="ticker" data-ticker aria-hidden="true"></div>
<section class="section"><div class="container"><div class="home-split"><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $gallery_num ); ?></span><span class="section-num__label"><?php echo esc_html( $gallery_label ); ?></span></div><h2 class="home-split__title"><?php echo esc_html( $gallery_title ); ?></h2><div class="home-split__gallery"><img class="home-split__gallery-img" src="<?php echo esc_url( $gallery_img_1 ); ?>" alt="<?php echo esc_attr( $gallery_title ); ?>"><img class="home-split__gallery-img" src="<?php echo esc_url( $gallery_img_2 ); ?>" alt="<?php echo esc_attr( $gallery_title ); ?>"></div><a class="link-arrow" href="<?php echo esc_url( p313_page_url( 'gallery' ) ); ?>"><?php echo esc_html( $gallery_link ); ?> <?php echo p313_icon( 'arrow' ); ?></a></div><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $blog_num ); ?></span><span class="section-num__label"><?php echo esc_html( $blog_label ); ?></span></div><h2 class="home-split__title"><?php echo esc_html( $blog_title ); ?></h2><div class="home-split__blog" data-blog-teaser></div></div></div></div></section>
<section class="section section--no-top"><div class="container"><div class="section-num"><span class="section-num__n"><?php echo esc_html( $reviews_num ); ?></span><span class="section-num__label"><?php echo esc_html( $reviews_label ); ?></span></div><h2 class="section-head__title"><?php echo esc_html( $reviews_title ); ?></h2><div class="reviews-grid" data-reviews-home></div></div></section><section class="section section--no-top"><div class="container"><div class="faq"><div><div class="section-num"><span class="section-num__n"><?php echo esc_html( $faq_num ); ?></span><span class="section-num__label"><?php echo esc_html( $faq_label ); ?></span></div><h2 class="faq__title"><?php echo esc_html( $faq_title ); ?></h2><p class="faq__hint"><?php echo esc_html( $faq_sub ); ?></p></div><div class="faq__list" data-faq></div></div></div></section></main>
