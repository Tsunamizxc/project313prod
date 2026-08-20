<?php
/* Template Name: Контакты */
get_header();
$head = p313_page_head_args(
	'page_contacts',
	array(
		'label' => 'контакты',
		'title' => 'Как нас найти',
		'sub'   => 'Приходите на просмотр — или напишите, и мы сами подскажем удобное время.',
	)
);
$form_title   = p313_field( 'page_contacts_form_title', 'Записаться на просмотр' );
$form_text    = p313_field( 'page_contacts_form_text', 'Оставьте контакты — перезвоним и подберём направление.' );
$phone_label  = p313_field( 'page_contacts_phone_label', 'Телефон' );
$vk_label     = p313_field( 'page_contacts_vk_label', 'ВКонтакте' );
$hours_label  = p313_field( 'page_contacts_hours_label', 'Часы работы' );
$phone        = p313_phone();
$phone_href   = p313_phone_href();
$vk_url       = p313_vk_url();
$hours        = p313_option( 'work_hours', 'Пн–Сб: 10:00–21:00' );
$map          = p313_map_embed();
$branches     = p313_get_branches();
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm"><div class="container">
 <div class="contacts">
  <div class="contacts__info">
   <ul class="contacts__list">
    <li class="contacts__item"><span class="contacts__label"><?php echo esc_html( $phone_label ); ?></span><a class="contacts__value" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a></li>
    <li class="contacts__item"><span class="contacts__label"><?php echo esc_html( $vk_label ); ?></span><a class="contacts__value" href="<?php echo esc_url( $vk_url ); ?>" target="_blank" rel="noopener noreferrer">vk.ru/dance.project313</a></li>
    <?php foreach ( $branches as $branch ) : ?>
    <li class="contacts__item"><span class="contacts__label"><?php echo esc_html( $branch['title'] ); ?></span><span class="contacts__value"><?php echo esc_html( $branch['address'] ); ?></span></li>
    <?php endforeach; ?>
    <?php if ( $hours ) : ?><li class="contacts__item"><span class="contacts__label"><?php echo esc_html( $hours_label ); ?></span><span class="contacts__value"><?php echo esc_html( $hours ); ?></span></li><?php endif; ?>
   </ul>
  </div>
  <div class="contacts__form">
   <?php if ( $form_title ) : ?><h2 class="contacts__form-title"><?php echo esc_html( $form_title ); ?></h2><?php endif; ?>
   <?php if ( $form_text ) : ?><p class="contacts__form-text"><?php echo esc_html( $form_text ); ?></p><?php endif; ?>
   <?php get_template_part( 'template-parts/signup-form' ); ?>
  </div>
 </div>
 <?php if ( $map ) : ?>
 <div class="contacts__map-block">
  <div class="contacts__map"><?php echo $map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- embed HTML from admin or generated widget ?></div>
 </div>
 <?php endif; ?>
</div></section>
</main>
<?php get_footer();
