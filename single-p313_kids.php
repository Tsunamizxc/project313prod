<?php
get_header();
$id        = get_the_ID();
$title     = get_the_title();
$age       = p313_field( 'age', '', $id );
$note      = p313_field( 'note', '', $id );
$level     = p313_field( 'level', '', $id );
$photo     = p313_img_url( p313_field( 'photo', '', $id ), 'p313-hero' ) ?: get_the_post_thumbnail_url( $id, 'p313-hero' );
$members   = p313_field( 'members', array(), $id );
$team_url  = p313_page_url( 'team' );
$is_senior = ( 'senior' === $level );
$used_branches = array();
if ( is_array( $members ) ) {
	foreach ( $members as $member ) {
		foreach ( p313_member_branches( $member['branch'] ?? '' ) as $branch_key ) {
			$used_branches[ $branch_key ] = true;
		}
	}
}
$show_switch   = count( $used_branches ) > 1;
$members_title = p313_field( 'members_title', 'Участницы', $id );
$head          = array(
	'label' => 'группа',
	'title' => $title,
	'sub'   => $age,
	'text'  => $note,
);
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm">
 <div class="container">
  <a class="link-arrow group-page__back" href="<?php echo esc_url( $team_url ); ?>"><?php echo p313_icon( 'arrow' ); ?> К коллективу</a>
  <?php if ( $photo ) : ?>
  <div class="group-page__hero">
   <img class="group-page__hero-img" src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $title ); ?>">
  </div>
  <?php endif; ?>
  <?php if ( $show_switch ) : ?>
  <div class="filters group-page__switch" data-group-branch-switch>
   <button class="tag tag--active" type="button" data-group-branch="krasny">Филиал на Красном Пути</button>
   <button class="tag" type="button" data-group-branch="khimik">Филиал на Химике</button>
  </div>
  <?php endif; ?>
  <?php if ( is_array( $members ) && $members ) : ?>
  <h2 class="group-page__title"><?php echo esc_html( $members_title ); ?></h2>
  <div class="teachers-grid" data-group-members>
   <?php
	foreach ( $members as $member ) :
		$name    = trim( (string) ( $member['name'] ?? '' ) );
		$photos  = p313_gallery_urls( $member['photos'] ?? array(), 'p313-teacher' );
		$single  = p313_img_url( $member['photo'] ?? '', 'p313-teacher' );
		if ( $single && ! in_array( $single, $photos, true ) ) {
			array_unshift( $photos, $single );
		}
		$branches = p313_member_branches( $member['branch'] ?? '' );
		if ( ! $name && ! $photos ) {
			continue;
		}
		?>
   <article class="card teacher-card" data-member-branch="<?php echo esc_attr( implode( ' ', $branches ) ); ?>">
    <div class="teacher-card__media">
     <?php if ( $is_senior && count( $photos ) > 1 ) : ?>
     <div class="card-slider" data-card-slider>
      <div class="card-slider__viewport">
       <?php foreach ( $photos as $index => $src ) : ?>
       <img class="card-slider__img teacher-card__img<?php echo 0 === $index ? ' is-active' : ''; ?>" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
       <?php endforeach; ?>
      </div>
      <button class="card-slider__nav card-slider__nav--prev" type="button" aria-label="Предыдущее фото" data-slider-prev></button>
      <button class="card-slider__nav card-slider__nav--next" type="button" aria-label="Следующее фото" data-slider-next></button>
      <div class="card-slider__dots" data-slider-dots>
       <?php foreach ( $photos as $index => $_src ) : ?>
       <button class="card-slider__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" aria-label="Фото <?php echo esc_attr( (string) ( $index + 1 ) ); ?>" data-slider-to="<?php echo esc_attr( (string) $index ); ?>"></button>
       <?php endforeach; ?>
      </div>
     </div>
     <?php elseif ( $photos ) : ?>
     <img class="teacher-card__img" src="<?php echo esc_url( $photos[0] ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
     <?php else : ?>
     <div class="teacher-card__img teacher-card__img--empty"></div>
     <?php endif; ?>
    </div>
    <div class="teacher-card__body">
     <h3 class="teacher-card__name"><?php echo esc_html( $name ); ?></h3>
    </div>
   </article>
   <?php endforeach; ?>
  </div>
  <p class="group-page__empty panel-hidden" data-group-empty>В этом филиале состав группы скоро появится.</p>
  <?php else : ?>
  <p class="group-page__empty">Состав группы скоро появится.</p>
  <?php endif; ?>
 </div>
</section>
</main>
<?php get_footer();
