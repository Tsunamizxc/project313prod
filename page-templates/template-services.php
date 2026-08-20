<?php
/* Template Name: Услуги */
get_header();
$head = p313_page_head_args(
	'page_services',
	array(
		'number' => '«03»',
		'label'  => 'услуги',
		'title'  => 'Направления и форматы',
		'sub'    => 'Групповые и индивидуальные занятия для детей и взрослых — выберите своё.',
	)
);
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm"><div class="container">
 <div class="services-filters" data-services-filters-page>
  <div class="services-filters__formats filters" data-format-filters></div>
  <div class="services-filters__ages filters panel-hidden" data-age-filters></div>
 </div>
 <div class="services-grid" data-services-page></div>
</div></section>
<div class="modal modal--detail" data-service-modal aria-hidden="true"><div class="modal__overlay" data-service-close></div><div class="modal__dialog" data-service-content></div></div>
</main>
<?php get_footer();
