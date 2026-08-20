<?php
/** Theme header. */
$logo = p313_img_url( p313_option( 'logo' ), 'full' ) ?: p313_asset( 'assets/img/logo-navy.webp' );
$phone = p313_phone();
$phone_href = p313_phone_href();
$items = array(
	'home'     => p313_option( 'nav_home', 'Главная' ),
	'team'     => p313_option( 'nav_team', 'Коллектив' ),
	'services' => p313_option( 'nav_services', 'Услуги' ),
	'schedule' => p313_option( 'nav_schedule', 'Расписание' ),
	'awards'   => p313_option( 'nav_awards', 'Награды' ),
	'gallery'  => p313_option( 'nav_gallery', 'Галерея' ),
	'blog'     => p313_option( 'nav_blog', 'Блог' ),
	'events'   => p313_option( 'nav_events', 'Мероприятия' ),
	'contacts' => p313_option( 'nav_contacts', 'Контакты' ),
);
$logo_sub = trim( (string) p313_option( 'logo_sub', 'школа современной хореографии, Омск' ), "\xEF\xBB\xBF \t\n\r\0\x0B" );
$cta_label = p313_option( 'cta_label', 'Записаться' );
$org_name = p313_option( 'org_name', 'Project 313' );
?>
<!doctype html>
<html <?php language_attributes(); ?> class="page">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="<?php echo esc_url( p313_asset( 'assets/img/favicon.png' ) ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'page__body' ); ?>>
<?php wp_body_open(); ?>
<div class="site-container">
<header class="header fix-block" data-header>
 <div class="container header__inner">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo"><img class="header__logo-img" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $org_name ); ?>"><span class="header__logo-sub"><?php echo esc_html( $logo_sub ); ?></span></a>
  <nav class="header__nav" aria-label="Основное меню"><?php foreach ( $items as $slug => $label ) : ?><a class="<?php echo p313_nav_class( $slug ); ?>" href="<?php echo esc_url( 'home' === $slug ? home_url( '/' ) : p313_page_url( $slug ) ); ?>" data-nav="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></nav>
  <div class="header__actions"><a class="header__phone" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a><button class="btn btn--primary header__cta" type="button" data-open-form><?php echo esc_html( $cta_label ); ?></button><button class="header__burger btn-reset" type="button" data-burger aria-expanded="false" aria-label="Открыть меню"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" data-burger-open><path d="M4 7h16M4 12h16M4 17h16"/></svg><svg class="icon hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" data-burger-close><path d="M6 6l12 12M18 6L6 18"/></svg></button></div>
 </div>
 <div class="header__drawer" data-menu><div class="container header__drawer-inner"><?php foreach ( $items as $slug => $label ) : ?><a class="<?php echo p313_nav_class( $slug, 'header__drawer-link' ); ?>" href="<?php echo esc_url( 'home' === $slug ? home_url( '/' ) : p313_page_url( $slug ) ); ?>" data-nav="<?php echo esc_attr( $slug ); ?>" data-menu-item><?php echo esc_html( $label ); ?></a><?php endforeach; ?><a class="header__drawer-phone" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a><button class="btn btn--secondary header__drawer-cta" type="button" data-open-form data-menu-item><?php echo esc_html( $cta_label ); ?></button></div></div>
</header>
<div class="header__backdrop" data-menu-backdrop hidden></div>
