<?php
$title  = $args['title'] ?? get_the_title();
$sub    = $args['sub'] ?? '';
$label  = $args['label'] ?? '';
$text   = $args['text'] ?? '';
$images = $args['images'] ?? array();
if ( ! is_array( $images ) ) {
	$images = array();
}
$images = array_values( array_filter( $images ) );
$split  = $images ? ' page-head--split' : '';
?>
<section class="page-hero">
<div class="container page-head<?php echo esc_attr( $split ); ?>">
 <div class="page-head__copy reveal">
  <?php if ( $label ) : ?><div class="section-num"><span class="section-num__label"><?php echo esc_html( $label ); ?></span></div><?php endif; ?>
  <h1 class="page-head__title"><?php echo esc_html( $title ); ?></h1>
  <?php if ( $sub ) : ?><p class="page-head__sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
  <?php if ( $text ) : ?><div class="page-head__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div><?php endif; ?>
  <?php p313_sweep( 'page-head__sweep', '0.7' ); ?>
 </div>
 <?php if ( $images ) : ?>
 <div class="page-head__photos reveal">
  <?php foreach ( $images as $src ) : ?>
  <img class="page-head__photo" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $title ); ?>">
  <?php endforeach; ?>
 </div>
 <?php endif; ?>
</div>
</section>
