<?php
/* Template Name: Отзывы */
get_header();
$head = p313_page_head_args(
	'page_reviews',
	array(
		'number' => '«06»',
		'label'  => 'отзывы',
		'title'  => 'Голоса нашей семьи',
		'sub'    => 'Истории учеников и родителей, для которых Project 313 стал важной частью жизни.',
	)
);
$form_title = p313_field( 'page_reviews_form_title', '' );
$form_text  = p313_field( 'page_reviews_form_text', '' );
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?><section class="section section--top-sm"><div class="container"><div class="reviews-grid" data-reviews-page></div><?php if ( $form_title || $form_text ) : ?><div class="contacts__form" style="margin-top:4.8rem"><?php if ( $form_title ) : ?><h2 class="contacts__form-title"><?php echo esc_html( $form_title ); ?></h2><?php endif; ?><?php if ( $form_text ) : ?><p class="contacts__form-text"><?php echo esc_html( $form_text ); ?></p><?php endif; ?><?php get_template_part( 'template-parts/signup-form' ); ?></div><?php endif; ?></div></section></main>
<?php get_footer();
