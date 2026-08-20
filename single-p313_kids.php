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
$show_switch = in_array( $level, array( 'junior', 'middle' ), true );
$members_title = p313_field( 'members_title', 'Участницы', $id );
$head          = array(
	'label' => 'группа',
	'title' => $title,
	'sub'   => trim( implode( ' · ', array_filter( array( $age, $note ) ) ) ),
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
		$name   = trim( (string) ( $member['name'] ?? '' ) );
		$murl   = p313_img_url( $member['photo'] ?? '', 'p313-teacher' );
		$branch = (string) ( $member['branch'] ?? '' );
		if ( ! $name && ! $murl ) {
			continue;
		}
		?>
   <article class="card teacher-card" data-member-branch="<?php echo esc_attr( $branch ); ?>">
    <div style="overflow: hidden;">
     <?php if ( $murl ) : ?>
     <img class="teacher-card__img" src="<?php echo esc_url( $murl ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
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
