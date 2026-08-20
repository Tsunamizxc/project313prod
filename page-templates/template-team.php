<?php
/* Template Name: Коллектив */
get_header();
$head = p313_page_head_args(
	'page_team',
	array(
		'label' => 'коллектив',
		'title' => 'Те, кто ведёт на сцену',
		'sub'   => 'Педагоги Project 313 — хореографы, наставники и часть большой семьи студии.',
	)
);
$tab_teachers = p313_field( 'page_team_tab_teachers', 'Педагоги' );
$tab_kids     = p313_field( 'page_team_tab_kids', 'Группа' );
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm"><div class="container">
 <div class="filters" data-team-tabs>
  <button class="tag tag--active" type="button" data-tab="teachers"><?php echo esc_html( $tab_teachers ); ?></button>
  <button class="tag" type="button" data-tab="kids"><?php echo esc_html( $tab_kids ); ?></button>
 </div>
 <div data-teachers-grid></div>
 <div class="panel-hidden" data-kids-grid></div>
</div></section>
<div class="modal modal--detail" data-teacher-modal aria-hidden="true"><div class="modal__overlay" data-teacher-close></div><div class="modal__dialog" data-teacher-content></div></div>
</main>
<?php get_footer();
