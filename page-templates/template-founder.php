<?php
/* Template Name: Руководитель */
get_header();
$head = p313_page_head_args(
	'page_founder',
	array(
		'label' => 'руководитель',
		'title' => p313_option( 'founder_name', 'Анна Волкова' ),
		'sub'   => p313_option( 'founder_short', 'Художественный руководитель Project 313.' ),
	)
);
$photo     = p313_img_url( p313_founder_value( 'page_founder_photo', 'founder_photo', '' ), 'large' ) ?: p313_asset( 'assets/img/founder.webp' );
$role      = p313_founder_value( 'page_founder_role', 'founder_role', 'Художественный руководитель Project 313' );
$exp       = p313_founder_value( 'page_founder_exp', 'founder_exp', '15 лет' );
$education = p313_founder_value( 'page_founder_education', 'founder_education', '' );
$page_bio  = p313_field( 'page_founder_bio', '' );
$facts     = p313_normalize_founder_facts( p313_field( 'page_founder_facts', null ) );
if ( ! $facts ) {
	$facts = p313_normalize_founder_facts( p313_option( 'founder_facts', array() ) );
}
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm"><div class="container"><div class="founder">
 <div class="founder__aside">
  <div class="founder__media"><img class="founder__photo" src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $head['title'] ); ?>"></div>
  <?php if ( $facts ) : ?>
  <div class="founder__facts">
   <?php foreach ( $facts as $fact ) : ?>
   <div class="founder__fact">
    <p class="founder__fact-n"><?php echo esc_html( $fact['n'] ); ?></p>
    <p class="founder__fact-l"><?php echo esc_html( $fact['label'] ); ?></p>
   </div>
   <?php endforeach; ?>
  </div>
  <?php else : ?>
  <div class="founder__facts" data-founder-facts></div>
  <?php endif; ?>
  <div class="founder__actions"><button class="btn btn--primary" type="button" data-open-form><?php echo esc_html( p313_option( 'cta_label', 'Записаться' ) ); ?></button></div>
 </div>
 <div class="founder__copy">
  <p class="founder__role"><?php echo esc_html( $role ); ?></p>
  <?php if ( $exp ) : ?><div class="teacher-card__meta"><span class="teacher-card__meta-label">Опыт</span><p class="teacher-card__meta-text founder__exp"><?php echo esc_html( $exp ); ?></p></div><?php endif; ?>
  <?php if ( $education ) : ?><div class="teacher-card__meta"><span class="teacher-card__meta-label">Образование</span><p class="teacher-card__meta-text"><?php echo nl2br( esc_html( $education ) ); ?></p></div><?php endif; ?>
  <?php if ( $page_bio ) : ?>
  <div class="founder__text founder__text--rich"><?php echo wp_kses_post( $page_bio ); ?></div>
  <?php else : ?>
  <div class="founder__text" data-founder-bio></div>
  <?php endif; ?>
 </div>
</div></div></section>
</main>
<?php get_footer();
