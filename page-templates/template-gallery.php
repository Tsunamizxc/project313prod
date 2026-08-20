<?php
/* Template Name: Галерея */
get_header();
$head = p313_page_head_args(
	'page_gallery',
	array(
		'number' => '«07»',
		'label'  => 'галерея',
		'title'  => 'Кадры со сцены и из зала',
		'sub'    => 'Конкурсы, отчётные концерты и будни Project 313.',
	)
);
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?>
<section class="section section--top-sm"><div class="container">
 <div class="filters" data-gallery-cats></div>
 <div class="filters year-filters" data-gallery-years></div>
 <div class="gallery-masonry" data-gallery-grid></div>
</div></section>
<div class="lightbox" data-lightbox aria-hidden="true">
 <div class="lightbox__overlay" data-lightbox-close></div>
 <button class="lightbox__close btn-reset" type="button" aria-label="Закрыть" data-lightbox-close><?php echo p313_icon( 'close' ); ?></button>
 <img class="lightbox__img" src="" alt="" data-lightbox-img>
</div>
</main>
<?php get_footer();
